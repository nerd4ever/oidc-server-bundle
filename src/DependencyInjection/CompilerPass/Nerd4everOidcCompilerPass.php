<?php
/**
 * @package   oidc-server-bundle
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\DependencyInjection\CompilerPass;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\ResourceServer;
use Nerd4ever\OidcServerBundle\Model\IdTokenResponse;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

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
        if (!$container->hasDefinition('league.oauth2_server.authorization_server')) {
            return;
        }

        if (!$container->hasDefinition('league.oauth2_server.authorization_server')) {
            return;
        }

        $oauth2ServerDefinition = $container->getDefinition('league.oauth2_server.authorization_server');
        $oauth2Arguments = $oauth2ServerDefinition->getArguments();

        // Response Type
        if (count($oauth2ServerDefinition->getArguments()) < 5) {
            return;
        }
        $responseType = new Reference(IdTokenResponse::class);
        $oauth2Arguments[5] = $responseType;
        $oauth2ServerDefinition->setArguments($oauth2Arguments);

        // Key
        $encryptionKey = $container->getParameter('league.oauth2_server.encryption_key');
        if ($container->has('league.oauth2_server.defuse_key')) {
            $encryptionKey = new Reference('league.oauth2_server.defuse_key');
        }
        $oidcServer = $container->findDefinition('nerd4ever.oidc.oidc-server');
        $oauth2PrivateKey = $oauth2Arguments[3];

        $oidcServer->replaceArgument(0, $encryptionKey);
        $oidcServer->replaceArgument(1, $oauth2PrivateKey);
    }
}