<?php

namespace Core\Bundle\ServiceBundle\Service;

use CommerceGuys\Guzzle\Oauth2\GrantType\ClientCredentials;
use CommerceGuys\Guzzle\Oauth2\Oauth2Subscriber;
use Core\Bundle\MainBundle\DependencyInjection\ContainerAwareTrait;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Auth implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    protected $token = '';

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function retrieveAccessToken() {
        $oauth2Client = new Client(['base_url' => $this->getContainer()->getParameted('service_bundle.api.endpoint')]);

        $config = [
            'client_id' => $this->getContainer()->getParameted('service_bundle.oauth.client_id'),
            'client_secret' => $this->getContainer()->getParameted('service_bundle.oauth.client_secret'),
            'scope' => $this->getContainer()->getParameted('service_bundle.oauth.scope'),
        ];

        $token = new ClientCredentials($oauth2Client, $config);
        $oauth2 = new Oauth2Subscriber($token);

        $client = new Client([
            'defaults' => [
                'auth' => 'oauth2',
                'subscribers' => [$oauth2],
            ],
        ]);

        $response = $client->get(
            $this->getContainer()->getParameted('service_bundle.api.protocol').'://'.
            $this->getContainer()->getParameted('service_bundle.api.host').'/'.
            $this->getContainer()->getParameted('service_bundle.oauth.endpoint'));

        print_r($response->json());
    }
}