<?php
/**
 * @package   oidc-server-bundle
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Nerd4ever\OidcServerBundle\DependencyInjection\OidcServerExtension;
use Nerd4ever\OidcServerBundle\DependencyInjection\CompilerPass\OidcCompilerPass;

final class OidcServerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->registerExtension(new OidcServerExtension());
        $container->addCompilerPass(new OidcCompilerPass());
    }
}