<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\Repository;

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
            $identifier,
            $refreshToken->getIdentifier(),
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
        $session = $this->sessionManager->find($sessionEntity->getIdentifier());

        if (null !== $session) {
            throw SessionIdentifierConstraintViolationExceptionNerd4ever::create();
        }

        $session = new Session(
            $sessionEntity->getIdentifier(),
            $sessionEntity->getRefreshTokenIdentifier(),
            $sessionEntity->getUserAgent(),
            $sessionEntity->getUserAddress()
        );

        $this->sessionManager->save($session);
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
        return $this->sessionManager->findByAccessToken($accessTokenIdentifier);
    }
}
