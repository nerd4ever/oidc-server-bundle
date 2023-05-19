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
use Nerd4ever\OidcServerBundle\DependencyInjection\Nerd4everOidcServerExtension;
use Nerd4ever\OidcServerBundle\DependencyInjection\CompilerPass\Nerd4everOidcCompilerPass;

final class Nerd4everOidcServerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->registerExtension(new Nerd4everOidcServerExtension());
        $container->addCompilerPass(new Nerd4everOidcCompilerPass());
    }
}