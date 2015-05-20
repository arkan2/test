<?php

namespace Core\Bundle\ServiceBundle\Service;

use Core\Bundle\MainBundle\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HttpServiceGateway extends Dispatcher implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {


    }

    /**
     * @param Auth $auth
     */
    public function setAuth(Auth $auth) {

    }
}