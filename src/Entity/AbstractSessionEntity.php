<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\Entity;

use DateTimeImmutable;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
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
    protected ?string $userAgent;
    protected ?string $userAddress;
    protected RefreshTokenEntityInterface $refreshToken;
    protected ?DateTimeImmutable $revokedAt = null;
    protected ?DateTimeImmutable $createdAt = null;

    /**
     * @param RefreshTokenEntityInterface $refreshToken
     * @param string $identifier
     * @param string|null $userAgent
     * @param string|null $userAddress
     */
    public function __construct(RefreshTokenEntityInterface $refreshToken, string $identifier, ?string $userAgent, ?string $userAddress)
    {
        $this->refreshToken = $refreshToken;
        $this->identifier = $identifier;
        $this->userAgent = $userAgent;
        $this->userAddress = $userAddress;
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
     * @return RefreshTokenEntityInterface
     */
    public function getRefreshToken(): RefreshTokenEntityInterface
    {
        return $this->refreshToken;
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

    /**
     * @param DateTimeImmutable|null $createdAt
     * @return AbstractSessionEntity
     */
    public function setCreatedAt(?DateTimeImmutable $createdAt): AbstractSessionEntity
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function updateCreatedAt(AbstractSessionEntity $entity): void
    {
        if (!$entity->getCreatedAt()) {
            $entity->setCreatedAt(new DateTimeImmutable());
        }
    }
}
