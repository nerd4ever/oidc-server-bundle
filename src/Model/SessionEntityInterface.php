<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\Model;

use DateTimeImmutable;

interface SessionEntityInterface
{
    public function getIdentifier(): string;

    public function getUserAgent(): ?string;

    public function getUserAddress(): ?string;

    public function getRefreshTokenIdentifier(): string;

    public function getCreatedAt(): ?DateTimeImmutable;

    public function getRevokedAt(): ?DateTimeImmutable;

    public function revoke(): static;
}