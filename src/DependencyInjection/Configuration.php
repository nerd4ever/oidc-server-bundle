<?php
/**
 * @package   oidc-server-bundle
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\DependencyInjection;

use Nerd4ever\OidcServerBundle\Entity\Session;
use Nerd4ever\OidcServerBundle\Model\AbstractSessionEntity;
use Nerd4ever\OidcServerBundle\Repository\IdentityProviderInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nerd4ever_oidc_server');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->append($this->createSessionNode());
        $rootNode->append($this->createIdentityRepositoryNode());
        return $treeBuilder;
    }

    private function createSessionNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('session');
        $node = $treeBuilder->getRootNode();
        $node
            ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('classname')
                        ->info(sprintf('Set a custom session class. Must be a %s', AbstractSessionEntity::class))
                        ->defaultValue(Session::class)
                        ->beforeNormalization()
                            ->ifNull()
                            ->then(function ($v) {
                                return Session::class;
                            })
                        ->end()
                        ->validate()
                        ->ifTrue(function ($v) {
                            return !is_a($v, AbstractSessionEntity::class, true);
                        })
                        ->thenInvalid(sprintf('%%s must be a %s', AbstractSessionEntity::class))
                        ->end()
                    ->end()
                ->scalarNode('entity_manager')
                    ->info('The name of the entity manager to be used for the session')
                    ->defaultValue(null)
                ->end()
            ->end();
        return $node;
    }

    private function createIdentityRepositoryNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('provider');
        $node = $treeBuilder->getRootNode();
        $node
            ->info(sprintf('Set a custom session class. Must be a %s', IdentityProviderInterface::class))
            ->isRequired()
            ->children()
                    ->scalarNode('classname')
                    ->validate()
                    ->ifTrue(function ($v) {
                        return !is_a($v, IdentityProviderInterface::class, true);
                    })
                    ->thenInvalid(sprintf('%%s must be a %s', IdentityProviderInterface::class))
                    ->end()
                ->end()
            ->end();
        return $node;
    }
}