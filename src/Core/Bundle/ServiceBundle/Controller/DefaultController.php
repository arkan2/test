<?php

namespace Core\Bundle\ServiceBundle\Controller;

use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package Core\Bundle\ServiceBundle\Controller
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        try {
            /*$httpClient = new Client(array('base_url'=>'https://dev-sdetrev.web-fr-front-19.boursorama.com/services/api/v1.0/'));
            $response = $httpClient->get('feed/instrument/quotes/1rPGLE',array(
                'verify'=>false,
                'headers'=>['Authorization' => 'Bearer 108a649ae18b57d3dee5b8bb02a5ffc8326bdd32'])
            );
            var_dump($response->getBody()->getContents());*/

            $httpClient = $this->get('service_http_client');
            $httpClient->get('feed/instrument/quotes/1rPGLE');
        }
        catch(RequestException $e) {
            var_dump($e);
            var_dump($e->getRequest()->getBody());
        }
        return $this->render('CoreServiceBundle:Default:index.html.twig', array('name' => ''));
    }
}
