<?php
/**
 * @package   oidc-server-bundle
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\Event;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Lcobucci\JWT\Token\Builder;

final class OidcServerIdTokenBuilderResolveEvent extends Event
{
    private Builder $builder;
    private AccessTokenEntityInterface $accessToken;
    private UserEntityInterface $userEntity;

    /**
     * @param Builder $builder
     * @param AccessTokenEntityInterface $accessToken
     * @param UserEntityInterface $userEntity
     */
    public function __construct(Builder $builder, AccessTokenEntityInterface $accessToken, UserEntityInterface $userEntity)
    {
        $this->builder = $builder;
        $this->accessToken = $accessToken;
        $this->userEntity = $userEntity;
    }

    /**
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * @return AccessTokenEntityInterface
     */
    public function getAccessToken(): AccessTokenEntityInterface
    {
        return $this->accessToken;
    }

    /**
     * @return UserEntityInterface
     */
    public function getUserEntity(): UserEntityInterface
    {
        return $this->userEntity;
    }

}