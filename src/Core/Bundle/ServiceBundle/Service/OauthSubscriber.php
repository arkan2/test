<?php

namespace Core\Bundle\ServiceBundle\Service;

use CommerceGuys\Guzzle\Oauth2\Oauth2Subscriber;
use Symfony\Component\HttpFoundation\Session\Session;

class OauthSubscriber extends Oauth2Subscriber
{
    protected $storage = null;

    /**
     * @param Session $storage
     */
    public function setStorage(Session $storage) {
        $this->storage = $storage;
    }

    /**
     * @return Session $storage
     */
    public function getStorage() {
        return $this->storage;
    }

    /**
     * @return \CommerceGuys\Guzzle\Oauth2\AccessToken
     */
    public function getAccessToken()
    {
        $accessToken = $this->getStorage()->get('access_token');

        if(empty($accessToken)) {
            $accessToken = parent::getAccessToken();
            $this->getStorage()->set('access_token',array(
                'access_token' => $accessToken->getToken(),
                'type' => $accessToken->getType(),
                'expires' => $accessToken->getExpires()->getTimestamp()
            ));
        }
        else {
            $this->setAccessToken($accessToken['access_token'], $accessToken['type'], $accessToken['expires']);
        }
        return parent::getAccessToken();
    }
}