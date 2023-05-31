<?php
/**
 * @package   oidc-server-bundle
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Nerd4ever\OidcServerBundle\Repository\SessionRepositoryInterface;
use Nerd4ever\OidcServerBundle\Repository\SessionRepository;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestStack;
use League\Bundle\OAuth2ServerBundle\Manager\RefreshTokenManagerInterface;
use League\Bundle\OAuth2ServerBundle\Manager\AccessTokenManagerInterface;

use Nerd4ever\OidcServerBundle\Model\ClaimExtractor;
use Nerd4ever\OidcServerBundle\Repository\IdentityProviderInterface;
use Nerd4ever\OidcServerBundle\DependencyInjection\CompilerPass\Nerd4everOidcCompilerPass;
use Nerd4ever\OidcServerBundle\Controller\OidcController;
use Nerd4ever\OidcServerBundle\EventListener\OAuth2ServerListener;
use Nerd4ever\OidcServerBundle\Manager\SessionManagerInterface;
use Nerd4ever\OidcServerBundle\OidcServer;
use Nerd4ever\OidcServerBundle\Model\IdTokenResponse;
use Nerd4ever\OidcServerBundle\OidcServerInterface;
use Nerd4ever\OidcServerBundle\Grant\OidcImplicitGrant;
use Symfony\Component\DependencyInjection\Definition;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services
        ->set('nerd4ever.oidc.repository.session', SessionRepository::class)
        ->args([
            service(SessionManagerInterface::class)
        ])
        ->autoconfigure(true)
        ->alias(SessionRepositoryInterface::class, SessionRepository::class);

    $services->set(IdentityProviderInterface::class)->autoconfigure(true);
    $services->set(ClaimExtractor::class)->autoconfigure(true);

    $services->set('nerd4ever.oidc.oidc-server', OidcServer::class)
        ->args([
            null,
            null,
            new Reference(RequestStack::class),
            service(IdentityProviderInterface::class),
            service(ClaimExtractor::class),
            service(EventDispatcherInterface::class),
            service('nerd4ever.oidc.repository.session'),
            service(RefreshTokenManagerInterface::class),
            service(AccessTokenManagerInterface::class),
        ])
        ->autoconfigure(true)
        ->alias(OidcServerInterface::class, 'nerd4ever.oidc.oidc-server');

    $services->set(IdTokenResponse::class)
        ->args([service('nerd4ever.oidc.oidc-server')]);

    $services->set(OidcImplicitGrant::class)
        ->args([
            service('nerd4ever.oidc.oidc-server'),
            new Definition(\DateInterval::class, ['PT1H'])
        ])
        ->autoconfigure(true);

    $services->set(Nerd4everOidcCompilerPass::class)
        ->tag('kernel.compiler_pass');


    // Controller
    $services
        ->set(OidcController::class)
        ->autowire(true)
        ->public();

    $services->set('nerd4ever.oidc.listener.oauth2-server', OAuth2ServerListener::class)
        ->args([service('nerd4ever.oidc.oidc-server')])
        ->autoconfigure(true);

};
