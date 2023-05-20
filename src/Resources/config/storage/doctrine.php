<?php

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Nerd4ever\OidcServerBundle\Entity\Session;
use Nerd4ever\OidcServerBundle\Manager\SessionManager;
use Nerd4ever\OidcServerBundle\Manager\SessionManagerInterface;
use Nerd4ever\OidcServerBundle\Persistence\Mapping\Driver;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();
    $services->set('nerd4ever.oidc_server.persistence.driver', Driver::class)
        ->args([
            Session::class,
            null
        ])
        ->alias(Driver::class, 'nerd4ever.oidc_server.persistence.driver');

    $services->set(SessionManager::class)
        ->args([
            null,
            service(EventDispatcherInterface::class),
            null
        ])
        ->alias(SessionManagerInterface::class, SessionManager::class);
};