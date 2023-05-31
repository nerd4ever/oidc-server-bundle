<?php
/**
 * @package   oidc-server-bundle
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('oidc_jwks_uri', '/jwks_uri')
        ->controller('Nerd4ever\OidcServerBundle\Controller\OidcController::jwksUriAction')
        ->methods(['GET']);

    $routes
        ->add('oidc_configuration', '/.well-known/openid-configuration')
        ->controller('Nerd4ever\OidcServerBundle\Controller\OidcController::openidConfigurationAction')
        ->methods(['GET']);
};
