<?php
/**
 * @package   oidc-server-bundle
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Nerd4ever\OidcServerBundle\Manager\SessionManager;
use Nerd4ever\OidcServerBundle\Repository\IdentityProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Exception;
use Symfony\Component\DependencyInjection\Reference;

/**
 * My OidcServerExtension
 *
 * @package Nerd4ever\OidcServerBundle\DependencyInjection
 * @author Sileno de Oliveira Brito
 */
class Nerd4everOidcServerExtension extends Extension implements CompilerPassInterface
{

    /**
     * Loads a specific configuration.
     *
     * @throws InvalidArgumentException|Exception When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        $config = $this->processConfiguration(new Configuration(), $configs);

        // Obtém o valor de classname da configuração
        $providerClassName = $config['provider']['classname'];
        $this->configureProvider($container, $providerClassName);

        $sessionClassName = $config['session']['classname'];
        $entityManagerName = $config['session']['entity_manager'];
        $this->configureSession($loader, $container, $sessionClassName, $entityManagerName);

        $this->configureDiscovery($container, $config);
    }

    private function assertRequiredBundlesAreEnabled(ContainerBuilder $container): void
    {
        $requiredBundles = [
            'doctrine' => DoctrineBundle::class,
        ];

        foreach ($requiredBundles as $bundleAlias => $requiredBundle) {
            if (!$container->hasExtension($bundleAlias)) {
                throw new \LogicException(sprintf('Bundle \'%s\' needs to be enabled in your application kernel.', $requiredBundle));
            }
        }
    }

    public function process(ContainerBuilder $container)
    {
        $this->assertRequiredBundlesAreEnabled($container);
    }

    private function configureDiscovery(ContainerBuilder $container, array $config,)
    {
        $container->setParameter('nerd4ever.oidc_server.discovery.userinfo', $config['discovery']['userinfo']);
        $container->setParameter('nerd4ever.oidc_server.discovery.authorization', $config['discovery']['authorization']);
        $extras = isset($config['scopes']) && isset($config['scopes']['extras']) ? $config['scopes']['extras'] : [];
        $container->setParameter('nerd4ever.oidc_server.scope.extras', $extras);
    }

    private function configureSession(LoaderInterface $loader, ContainerBuilder $container, string $className, ?string $entityManagerName = null)
    {
        $loader->load('storage/doctrine.php');
        $entityManager = new Reference('doctrine.orm.default_entity_manager');

        $container
            ->getDefinition(SessionManager::class)
            ->replaceArgument(0, $entityManager)
            ->replaceArgument(2, $className);

        $container->setParameter('nerd4ever.oidc_server.persistence.doctrine.enabled', true);
        $container->setParameter('nerd4ever.oidc_server.persistence.doctrine.manager', $entityManagerName);
    }

    private function configureProvider(ContainerBuilder $container, string $className)
    {
        $serviceId = $className;

        if (!$container->has($serviceId)) {
            $container->register($serviceId, $className)->setAutowired(true);
        }

        $container->getDefinition($serviceId)->setPublic(true);

        // Define o serviço IdentityProviderInterface com uma referência para o serviço $className
        $container->register(IdentityProviderInterface::class, $className)
            ->setPublic(true)
            ->setAutowired(true);
    }
}
