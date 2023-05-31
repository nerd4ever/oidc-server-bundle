<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle;

use Jose\Component\Core\JWKSet;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use Nerd4ever\OidcServerBundle\Model\SessionEntityInterface;

interface OidcServerInterface
{
    public function getJWKSet(): JWKSet;

    public function getKeyId(): string;

    public function getNewIdToken(AccessTokenEntityInterface $accessToken): ?string;

    public function getNewSession(RefreshTokenEntityInterface $refreshToken): ?SessionEntityInterface;
}