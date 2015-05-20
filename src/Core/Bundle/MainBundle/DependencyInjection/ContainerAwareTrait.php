<?php

namespace Core\Bundle\MainBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait ContainerAwareTrait
{

    /**
     * $container : app container
     *
     * @var Symfony\Component\DependencyInjection\Container
     * @access protected
     */
    protected $container;

    /**
     * Set value for $container
     *
     * @param  Symfony\Component\DependencyInjection\ContainerInterface $value value to set to container
     * @return Object                                                   instance for method chaining
     */
    public function setContainer(ContainerInterface $value = null)
    {
        $this->container = $value;

        return $this;
    }

    /**
     * Get value for $container
     * @return Symfony\Component\DependencyInjection\Container app container
     */
    public function getContainer()
    {
        return $this->container;
    }

}