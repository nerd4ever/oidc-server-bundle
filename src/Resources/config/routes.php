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
    $routes
        ->add('oidc_configuration', '/.well-known/openid-configuration')
        ->controller(['nerd4ever.oidc.controller.oidc', 'openidConfigurationAction'])
        ->methods(['GET'])
        ->add('oidc_jwks_uri', '/oidc/jwks_uri')
        ->controller(['nerd4ever.oidc.controller.oidc', 'jwksUriAction'])
        ->methods(['GET']);
};
