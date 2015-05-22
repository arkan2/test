<?php

namespace Core\Bundle\ServiceBundle\Service;
use CommerceGuys\Guzzle\Oauth2\GrantType\ClientCredentials;
use CommerceGuys\Guzzle\Oauth2\Oauth2Subscriber;

class ServiceFactory
{
    /**
     * @param $class
     * @return Client
     */
    public static function buildHttpClient($httpClientClass, ServiceManager $serviceManager) {
        $oauth2Client = new $httpClientClass(['base_url' => $serviceManager->getBaseUrl()]);

        $config = [
            'client_id' => $serviceManager->getOauthConfig('oauth_client_id'),
            'client_secret' => $serviceManager->getOauthConfig('oauth_client_secret'),
            'scope' => $serviceManager->getOauthConfig('oauth_scope'),
            'token_url' => $serviceManager->getOauthConfig('oauth_url')
        ];

        $token = new ClientCredentials($oauth2Client, $config);
        $oauth2 = new Oauth2Subscriber($token);

        $httpClient = new $httpClientClass([
            'defaults' => [
                'auth' => 'oauth2',
                'subscribers' => [$oauth2],
                'verify' => $serviceManager->isVerifySsl()
            ],
        ]);

        return $httpClient;
    }

}