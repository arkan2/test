<?php

namespace Core\Bundle\ServiceBundle\Service;

use GuzzleHttp\Client;
use CommerceGuys\Guzzle\Oauth2\GrantType\ClientCredentials;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CommerceGuys\Guzzle\Oauth2\Oauth2Subscriber;

class HttpServiceGateway
{
    protected $baseUrl = '';

    protected $oauthConfig = array(
        'oauth_url' => '',
        'oauth_client_id' => '',
        'oauth_client_secret' => '',
        'oauth_scope' => ''
    );

    /**
     * @param $apiProto
     * @param $apiHost
     * @param $apiEndpoint
     */
    public function __construct($apiProto, $apiHost, $apiEndpoint)
    {
        $this->baseUrl = $apiProto.'://'.$apiHost.'/'.$apiEndpoint;
    }

    /**
     * @param $oauthProto
     * @param $oauthHost
     * @param $oauthEndpoint
     * @param $oauthClientId
     * @param $oauthClientSecret
     */
    public function configureOauth($oauthProto, $oauthHost, $oauthEndpoint, $oauthClientId, $oauthClientSecret, $oauthScope) {
        $this->oauthConfig = array_merge($this->oauthConfig, array(
            'oauth_url' => $oauthProto.'://'.$oauthHost.'/'.$oauthEndpoint,
            'oauth_client_id' => $oauthClientId,
            'oauth_client_secret' => $oauthClientSecret,
            'oauth_scope' => $oauthScope
        ));
    }

    public function hasOauth() {
        return !empty($this->oauthConfig['oauth_endpoint']);
    }

    public function execute() {
        $httpClient = new Client(['base_url' => $this->baseUrl]);

        $config = [
            'client_id' => $this->oauthConfig['oauth_client_id'],
            'client_secret' => $this->oauthConfig['oauth_client_secret'],
            'scope' => $this->oauthConfig['oauth_scope'],
        ];

        $token = new ClientCredentials($httpClient, $config);
        $oauth2 = new Oauth2Subscriber($token);

        $client = new Client([
            'defaults' => [
                'auth' => 'oauth2',
                'token_url' => $this->oauthConfig['oauth_url'],
                'subscribers' => [$oauth2],
            ],
        ]);

        $response = $client->get(
            $this->getContainer()->getParameter('service_bundle.api.protocol').'://'.
            $this->getContainer()->getParameter('service_bundle.api.host').'/'.
            $this->getContainer()->getParameter('service_bundle.oauth.endpoint'));

        print_r($response->json());
    }

}