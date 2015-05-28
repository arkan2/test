<?php
namespace Core\Bundle\EncryptedCookieSessionBundle\Security\Session;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class EncryptedCookieSessionHandler implements \SessionHandlerInterface
{

    /**
     * secret generated
     * @var string
     */
    protected $secret;


    /**
     * Crypt initialization vectpr
     * @var string
     */
    protected $initializationVector;


    /**
     * Request object
     * @var Request
     */
    protected $request;


    /**
     * Response object
     * @var Response
     */
    protected $response;

    protected $cookieName;

    protected $lifetime;
    protected $lifetimeTablet;
    protected $lifetimeMobile;

    protected $path;

    protected $domain;

    protected $secure;

    protected $httpOnly;

    protected $cookie = false;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * A session data checksum, that allow to check if session need to be rewriten.
     * @var string
     */
    protected $checksum;

    /**
     * Construct the session cookie handler
     *
     * @param string          $secret Secret passphrase to crypt the cookies
     * @param string          $cookieName
     * @param int             $lifetime
     * @param string          $path
     * @param string          $domain
     * @param bool            $secure
     * @param bool            $httpOnly
     */
    public function __construct($secret, $cookieName, $lifetime = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
        $this->secret = $secret;
        $this->cookieName = $cookieName;
        $this->path = $path;
        $this->domain = $domain;
        $this->lifetime = (int)$lifetime;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }


    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param FilterResponseEvent $e
     */
    public function onKernelResponse(FilterResponseEvent $e)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $e->getRequestType()) {
            return;
        }

        if ($this->logger) {
            $this->logger->debug('EncryptedCookieSession::onKernelResponse - Get the Response object');
        }

        $this->request->getSession()->save();

        if ($this->cookie === false) {
            if ($this->logger) {
                $this->logger->debug('EncryptedCookieSession::onKernelResponse - COOKIE not opened');
            }

            return;
        }

        if ($this->cookie === null) {
            if ($this->logger) {
                $this->logger->debug('EncryptedCookieSession::onKernelResponse - CLEAR COOKIE');
            }
            $e->getResponse()->headers->clearCookie($this->cookieName);
        } else {
            $e->getResponse()->headers->setCookie($this->cookie);
        }
    }

    /**
     * @param GetResponseEvent $e
     */
    public function onKernelRequest(GetResponseEvent $e)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $e->getRequestType()) {
            return;
        }

        if ($this->logger) {
            $this->logger->debug('EncryptedCookieSession::onKernelRequest - Receiving the Request object');
        }

        $this->request = $e->getRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        $this->cookie = null;

        if ($this->logger) {
            $this->logger->debug(sprintf('EncryptedCookieSession::destroy sessionId=%s', $sessionId));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionId)
    {
        if (!$this->request) {
            $this->logger->crit('EncryptedCookieSession::open - The Request object is missing');

            throw new \RuntimeException('You cannot access the session without a Request object set');
        }

        $this->logger->debug('EncryptedCookieSession::open');

        return $this->request->cookies->has($this->cookieName);
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        if (!$this->request) {

            $this->logger->crit('EncryptedCookieSession::read - The Request object is missing');

            throw new \RuntimeException('You cannot access the session without a Request object set');
        }

        $this->logger->debug(sprintf('EncryptedCookieSession::read sessionId=%s', $sessionId));

        if (!$this->request->cookies->has($this->cookieName)) {
            return '';
        }

        $content = $this->decrypt($this->request->cookies->get($this->cookieName));
        $this->checksum = md5(serialize($content));

        if ($content === false
            OR !is_array($content)
        ) {
            $content = array(
                'expire' => time(),
                'data'   => ''
            );
        }

        if ($content['expire'] !== 0 && $content['expire'] < time()) {
            return ''; // session expire
        }

        return $content['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData)
    {
        if ($this->logger) {
            $this->logger->debug(sprintf('EncryptedCookieSession::write sessionId=%s', $sessionId));
        }

        $lifetime = $this->getLifetime();

        $expire = $lifetime === 0 ? 0 : (time() + $lifetime);

        $this->logger->debug(sprintf('EncryptedCookieSession::write sessionId=%s', $sessionId));

        $encryptedData = $this->encrypt(array('expire' => $expire, 'data' => $sessionData));

        $this->logger->debug('Crypted cookie : ' . $encryptedData);
        $this->logger->debug('Crypted cookie size : ' . strlen($encryptedData));

        $this->cookie = new Cookie(
            $this->cookieName,
            $encryptedData,
            $expire,
            $this->path,
            $this->domain,
            $this->secure,
            $this->httpOnly
        );

        return true;
    }


    protected function getSecret()
    {
        if (!$this->secret) {
            $this->secret = hash('sha256', "weMissUmicheletettutweenettutween", TRUE);
        }
        return $this->secret;
    }

    protected function getInitializationVector()
    {
        if (!$this->initializationVector) {
            $this->initializationVector = mcrypt_create_iv(32, MCRYPT_DEV_URANDOM);
        }
        return $this->initializationVector;
    }

    protected function encrypt($input)
    {
        $input = gzcompress(serialize($input), 9);
        $output = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->getSecret(), $input, MCRYPT_MODE_ECB, $this->getInitializationVector());
        $output = gzcompress($output, 9);
        $output = base64_encode($output);
        return $output;
    }

    protected function decrypt($input)
    {
        $input = base64_decode($input);
        $input = @gzuncompress($input);

        if (!$input) {
            return false;
        }

        $uncryptOutput = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->getSecret(), $input, MCRYPT_MODE_ECB, $this->getInitializationVector());
        $output = @gzuncompress($uncryptOutput);

        if (!$output) {
            return false;
        }

        $output = trim($output);
        $output = @unserialize($output);
        return $output;
    }

    protected function getLifetime()
    {
        return $this->lifetime;
    }
}

