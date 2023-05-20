<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\Repository;

use League\Bundle\OAuth2ServerBundle\Model\AccessTokenInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use Nerd4ever\OidcServerBundle\Entity\Session;
use Nerd4ever\OidcServerBundle\Exception\SessionIdentifierConstraintViolationExceptionNerd4ever;
use Nerd4ever\OidcServerBundle\Manager\SessionManagerInterface;
use Nerd4ever\OidcServerBundle\Model\SessionEntityInterface;

/**
 * My SessionRepository
 *
 * @package Nerd4ever\OidcServerBundle\Repository
 * @author Sileno de Oliveira Brito
 */
class SessionRepository implements SessionRepositoryInterface
{
    private SessionManagerInterface $sessionManager;

    /**
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(SessionManagerInterface $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Create a new session
     *
     * @param RefreshTokenEntityInterface $refreshToken
     * @param string $identifier
     * @param string|null $userAgent
     * @param string|null $clientAddress
     * @return SessionEntityInterface
     */
    public function getNewSession(RefreshTokenEntityInterface $refreshToken, string $identifier, ?string $userAgent = null, ?string $clientAddress = null): SessionEntityInterface
    {
        return new Session(
            $refreshToken,
            $identifier,
            $userAgent,
            $clientAddress
        );
    }

    /**
     * Persists a new session to permanent storage.
     *
     * @throws SessionIdentifierConstraintViolationExceptionNerd4ever
     */
    public function persistNewSession(SessionEntityInterface $sessionEntity): void
    {
        $session = $this->sessionManager->find($sessionEntity->getUserIdentifier());

        if (null !== $session) {
            throw SessionIdentifierConstraintViolationExceptionNerd4ever::create();
        }
        $session = new Session(
            $sessionEntity->getClientIdentifier(),
            $sessionEntity->getRefreshTokenIdentifier(),
            $sessionEntity->getUserIdentifier(),
            $sessionEntity->getIdentifier(),
            $sessionEntity->getUserAgent(),
            $sessionEntity->getUserAddress()
        );
        $session->setAccessTokenIdentifier($sessionEntity->getAccessTokenIdentifier());
        $this->sessionManager->save($session);
    }

    /**
     * Update a session
     *
     * @param string $sessionIdentifier
     * @param AccessTokenInterface $accessTokenEntity
     */
    public function updateSession(string $sessionIdentifier, AccessTokenInterface $accessTokenEntity): void
    {
        $session = $this->sessionManager->find($sessionIdentifier);

        if (null === $session) {
            return;
        }
        /**
         * @var Session $session ;
         */
        $session->setAccessTokenIdentifier($accessTokenEntity->getUserIdentifier());
        $this->sessionManager->update($session);
    }

    /**
     * Revoke a session.
     *
     * @param string $sessionIdentifier
     */
    public function revokeSession(string $sessionIdentifier): void
    {
        $session = $this->sessionManager->find($sessionIdentifier);

        if (null === $session) {
            return;
        }
        $this->sessionManager->revoke($session);
    }

    /**
     * Check if the session has been revoked.
     *
     * @param string $sessionIdentifier
     *
     * @return bool Return true if this session has been revoked
     */
    public function isSessionRevoked(string $sessionIdentifier): bool
    {
        $session = $this->sessionManager->find($sessionIdentifier);
        return $session instanceof SessionEntityInterface && $session->getRevokedAt() !== null;
    }

    public function findByAccessToken(string $accessTokenIdentifier): ?SessionEntityInterface
    {
        return null;
    }

    public function findByRefreshToken(string $refreshTokenIdentifier): ?SessionEntityInterface
    {
        return null;
    }


}