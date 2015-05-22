<?php

namespace Core\Bundle\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
    public function indexAction()
    {
        var_dump($this->get('service_http_client')->get('v1.5/feed/instrument/quotes/1rPGLE'));
        return $this->render('CoreServiceBundle:Default:index.html.twig', array('name' => ''));
    }
}
