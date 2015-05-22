<?php

namespace Core\Bundle\ServiceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ServicePluginPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $clients = $container->findTaggedServiceIds('servicebundle.client');

        if (empty($clients)) {
            return;
        }

        foreach ($clients as $clientId => $attribute) {
            $clientDefinition = $container->findDefinition($clientId);

            //if($container->
            //$apiServiceDefinition->addMethodCall('configureOauth', $oauthConfig);

            //$this->registerGuzzlePlugin($clientDefinition, $plugins);

            if ($container->hasDefinition('profiler')) {
                $clientDefinition->addMethodCall(
                    'addSubscriber',
                    array(new Reference('playbloom_guzzle.client.plugin.profiler'))
                );
            }
        }
    }
}