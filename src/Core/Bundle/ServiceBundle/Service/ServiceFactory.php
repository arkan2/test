<?php

namespace Core\Bundle\ServiceBundle\Service;
use CommerceGuys\Guzzle\Oauth2\GrantType\ClientCredentials;
use Symfony\Component\HttpFoundation\Session\Session;

class ServiceFactory
{
    /**
     * @param $class
     * @return Client
     */
    public static function buildHttpClient(ServiceManager $serviceManager, Session $session) {
        $oauth2Client = new HttpClient([
            'base_url' => $serviceManager->getOauthConfig('oauth_base_url'),
            'defaults' => [
                'verify' => $serviceManager->isVerifySsl()
            ]
        ]);

        $config = [
            'auth_location' => 'body',
            'client_id' => $serviceManager->getOauthConfig('oauth_client_id'),
            'client_secret' => $serviceManager->getOauthConfig('oauth_client_secret'),
            'scope' => $serviceManager->getOauthConfig('oauth_scope'),
            'token_url' => $serviceManager->getOauthConfig('oauth_token_url')
        ];

        $grantType = new ClientCredentials($oauth2Client, $config);
        $oauth2 = new OauthSubscriber($grantType);
        $oauth2->setStorage($session);

        $httpClient = new HttpClient([
            'base_url' => $serviceManager->getBaseUrl(),
            'defaults' => [
                'auth' => 'oauth2',
                'subscribers' => [$oauth2],
                'verify' => $serviceManager->isVerifySsl()
            ],
        ]);

        return $httpClient;
    }

}