<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\DependencyInjection\CompilerPass;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Nerd4ever\OidcServerBundle\Persistence\Mapping\Driver;
use Symfony\Component\DependencyInjection\Reference;

/**
 * My RegisterDoctrineOrmMappingPass
 *
 * @package Nerd4ever\OidcServerBundle\DependencyInjection\CompilerPass
 * @author Sileno de Oliveira Brito
 */
class RegisterDoctrineOrmMappingPass extends DoctrineOrmMappingsPass
{

    public function __construct()
    {
        parent::__construct(
            new Reference(Driver::class),
            ['Nerd4ever\OidcServerBundle\Entity'],
            ['nerd4ever.oidc_server.persistence.doctrine.manager'],
            'nerd4ever.oidc_server.persistence.doctrine.enabled'
        );
    }
}