<?php

namespace Core\Bundle\ServiceBundle;

use Core\Bundle\ServiceBundle\DependencyInjection\Compiler\ServicePluginPass;
use Core\Bundle\ServiceBundle\DependencyInjection\CoreServiceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CoreServiceBundle extends Bundle
{

    public function getContainerExtension() {
        return new CoreServiceExtension();
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container) {
        parent::build($container);
        $container->addCompilerPass(new ServicePluginPass());
    }
}
