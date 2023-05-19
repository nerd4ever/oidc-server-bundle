<?php
/**
 * @package   oidc-server-bundle
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Nerd4ever\OidcServerBundle\Model\ClaimExtractor;
use Nerd4ever\OidcServerBundle\Repository\IdentityProviderInterface;
use Nerd4ever\OidcServerBundle\DependencyInjection\CompilerPass\Nerd4everOidcCompilerPass;
use Nerd4ever\OidcServerBundle\Model\IdTokenResponse;
use Nerd4ever\OidcServerBundle\Controller\OidcController;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set(IdentityProviderInterface::class)->autoconfigure(true);
    $services->set(ClaimExtractor::class)->autoconfigure(true);

    $services->set(IdTokenResponse::class)
        ->args([
            service(IdentityProviderInterface::class),
            service(ClaimExtractor::class),
            service(EventDispatcherInterface::class),
        ]);

    $services->set(Nerd4everOidcCompilerPass::class)
        ->tag('kernel.compiler_pass');


    $services->set('nerd4ever.oidc.controller.oidc', OidcController::class)->autoconfigure(true);
};
