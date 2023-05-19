<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\Repository;

use League\Bundle\OAuth2ServerBundle\Model\AccessTokenInterface;
use League\Bundle\OAuth2ServerBundle\Model\RefreshTokenInterface;
use Nerd4ever\OidcServerBundle\Exception\SessionIdentifierConstraintViolationExceptionNerd4ever;
use Nerd4ever\OidcServerBundle\Model\SessionEntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface SessionRepositoryInterface
{
    /**
     * Create a new session
     *
     * @param RefreshTokenInterface $refreshTokenEntity
     * @param UserInterface $user
     * @param string $identifier
     * @param string|null $userAgent
     * @param string|null $clientAddress
     * @return SessionEntityInterface
     */
    public function getNewSession(RefreshTokenInterface $refreshTokenEntity, UserInterface $user, string $identifier, ?string $userAgent = null, ?string $clientAddress = null): SessionEntityInterface;

    /**
     * Persists a new session to permanent storage.
     *
     * @throws SessionIdentifierConstraintViolationExceptionNerd4ever
     */
    public function persistNewSession(SessionEntityInterface $sessionEntity): void;

    /**
     * Update a session
     *
     * @param string $sessionIdentifier
     * @param AccessTokenInterface $accessTokenEntity
     */
    public function updateSession(string $sessionIdentifier, AccessTokenInterface $accessTokenEntity) : void;

    /**
     * Revoke a session.
     *
     * @param string $sessionIdentifier
     */
    public function revokeSession(string $sessionIdentifier) : void;

    /**
     * Check if the session has been revoked.
     *
     * @param string $sessionIdentifier
     *
     * @return bool Return true if this session has been revoked
     */
    public function isSessionRevoked(string $sessionIdentifier): bool;
}