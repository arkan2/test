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
     * @Route("/{name}")
     */
    public function indexAction($name)
    {
        $this->get('api_service')->execute();


        return $this->render('CoreServiceBundle:Default:index.html.twig', array('name' => $name));
    }
}
