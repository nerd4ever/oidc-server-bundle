<?php
/**
 * @package   oidc-server-bundle
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Nerd4ever\OidcServerBundle\Model\IdTokenResponse;

/**
 * My OidcCompilerPass
 *
 * @package Nerd4ever\IDP\DependencyInjection\Compiler
 * @author Sileno de Oliveira Brito
 */
class Nerd4everOidcCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $responseType = new Reference(IdTokenResponse::class);
        if (!$container->hasDefinition('league.oauth2_server.authorization_server')) {
            return;
        }
        $definition = $container->getDefinition('league.oauth2_server.authorization_server');
        if (count($definition->getArguments()) < 5) {
            return;
        }
        $arguments = $definition->getArguments();
        $arguments[5] = $responseType;
        $definition->setArguments($arguments);
    }
}