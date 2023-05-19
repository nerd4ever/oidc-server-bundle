<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\Model;

use DateTimeImmutable;

/**
 * My AbstractSession
 *
 * @package Nerd4ever\OidcServerBundle\Model
 * @author Sileno de Oliveira Brito
 */
abstract class AbstractSessionEntity implements SessionEntityInterface
{
    protected string $identifier;
    protected ?string $userAgent;
    protected ?string $clientAddress;
    protected string $userIdentifier;
    protected string $refreshTokenIdentifier;
    protected ?string $accessTokenIdentifier = null;
    protected ?DateTimeImmutable $revokedAt = null;

    /**
     * @param string $refreshTokenIdentifier
     * @param string $userIdentifier
     * @param string $identifier
     * @param string|null $userAgent
     * @param string|null $clientAddress
     */
    public function __construct(string $refreshTokenIdentifier, string $userIdentifier, string $identifier, ?string $userAgent, ?string $clientAddress)
    {
        $this->refreshTokenIdentifier = $refreshTokenIdentifier;
        $this->userIdentifier = $userIdentifier;
        $this->identifier = $identifier;
        $this->userAgent = $userAgent;
        $this->clientAddress = $clientAddress;
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
    public function getClientAddress(): ?string
    {
        return $this->clientAddress;
    }

    /**
     * @return string
     */
    public function getRefreshTokenIdentifier(): string
    {
        return $this->refreshTokenIdentifier;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * @return string|null
     */
    public function getAccessTokenIdentifier(): ?string
    {
        return $this->accessTokenIdentifier;
    }

    /**
     * @param string|null $accessTokenIdentifier
     * @return AbstractSessionEntity
     */
    public function setAccessTokenIdentifier(?string $accessTokenIdentifier): AbstractSessionEntity
    {
        $this->accessTokenIdentifier = $accessTokenIdentifier;
        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getRevokedAt(): ?DateTimeImmutable
    {
        return $this->revokedAt;
    }

    /**
     * @param DateTimeImmutable|null $revokedAt
     * @return AbstractSessionEntity
     */
    public function setRevokedAt(?DateTimeImmutable $revokedAt): AbstractSessionEntity
    {
        $this->revokedAt = $revokedAt;
        return $this;
    }
}
