<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\Entity;

use DateTimeImmutable;
use Nerd4ever\OidcServerBundle\Model\SessionEntityInterface;

/**
 * My AbstractSession
 *
 * @package Nerd4ever\OidcServerBundle\Model
 * @author Sileno de Oliveira Brito
 */
abstract class AbstractSessionEntity implements SessionEntityInterface
{
    protected string $identifier;
    protected string $refreshTokenIdentifier;
    protected ?string $userAgent;
    protected ?string $userAddress;
    protected ?DateTimeImmutable $revokedAt = null;
    protected ?DateTimeImmutable $createdAt = null;

    /**
     * @param string $identifier
     * @param string $refreshTokenIdentifier
     * @param string|null $userAgent
     * @param string|null $userAddress
     */
    public function __construct(string $identifier, string $refreshTokenIdentifier, ?string $userAgent, ?string $userAddress)
    {
        $this->identifier = $identifier;
        $this->refreshTokenIdentifier = $refreshTokenIdentifier;
        $this->userAgent = $userAgent;
        $this->userAddress = $userAddress;
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string|null
     */
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    /**
     * @return string|null
     */
    public function getUserAddress(): ?string
    {
        return $this->userAddress;
    }

    /**
     * @return string
     */
    public function getRefreshTokenIdentifier(): string
    {
        return $this->refreshTokenIdentifier;
    }


    /**
     * @return DateTimeImmutable|null
     */
    public function getRevokedAt(): ?DateTimeImmutable
    {
        return $this->revokedAt;
    }

    /**
     * @return AbstractSessionEntity
     */
    public function revoke(): static
    {
        $this->revokedAt = new DateTimeImmutable();
        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }
}
