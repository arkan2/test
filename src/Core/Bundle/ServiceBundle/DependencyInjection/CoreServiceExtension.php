<?php

namespace Core\Bundle\ServiceBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CoreServiceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // Setup service authentication
        switch($config['auth_method']) {
            case 'oauth2';
                $oauthConfig = array(
                    $container->getParameter('servicebundle.api.protocol'),
                    $container->getParameter('servicebundle.api.host'),
                    $container->getParameter('servicebundle.oauth.endpoint')
                ) + $config['oauth2'];

                $container->setParameter('core_service.httpclient.class', $config['httpclient']['class']);

                $apiServiceDefinition = $container->getDefinition('service_manager');
                $apiServiceDefinition->addMethodCall('configureOauth', $oauthConfig);
                $apiServiceDefinition->addMethodCall('setVerifySsl', array($config['verify_ssl']));
                break;
        }
    }
}
