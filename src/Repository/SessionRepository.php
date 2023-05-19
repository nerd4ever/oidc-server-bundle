<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\Repository;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\AccessTokenInterface;
use League\Bundle\OAuth2ServerBundle\Model\RefreshTokenInterface;
use Nerd4ever\OidcServerBundle\Entity\SessionEntity;
use Nerd4ever\OidcServerBundle\Exception\SessionIdentifierConstraintViolationExceptionNerd4ever;
use Nerd4ever\OidcServerBundle\Model\SessionEntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * My SessionRepository
 *
 * @package Nerd4ever\OidcServerBundle\Repository
 * @author Sileno de Oliveira Brito
 */
class SessionRepository implements SessionRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
    public function getNewSession(RefreshTokenInterface $refreshTokenEntity, UserInterface $user, string $identifier, ?string $userAgent = null, ?string $clientAddress = null): SessionEntityInterface
    {
        return new SessionEntity(
            $refreshTokenEntity->getIdentifier(),
            $user->getUserIdentifier(),
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
        $session = $this->entityManager->getRepository(SessionEntity::class)->find($sessionEntity->getIdentifier());

        if (null !== $session) {
            throw SessionIdentifierConstraintViolationExceptionNerd4ever::create();
        }
        $session = new SessionEntity(
            $sessionEntity->getRefreshTokenIdentifier(),
            $sessionEntity->getUserIdentifier(),
            $sessionEntity->getIdentifier(),
            $sessionEntity->getUserAgent(),
            $sessionEntity->getClientAddress()
        );
        $session->setAccessTokenIdentifier($sessionEntity->getAccessTokenIdentifier());
        $this->entityManager->persist($session);
        $this->entityManager->flush();
    }

    /**
     * Update a session
     *
     * @param string $sessionIdentifier
     * @param AccessTokenInterface $accessTokenEntity
     */
    public function updateSession(string $sessionIdentifier, AccessTokenInterface $accessTokenEntity): void
    {
        $session = $this->entityManager->getRepository(SessionEntity::class)->find($sessionIdentifier);

        if (null === $session) {
            return;
        }
        /**
         * @var SessionEntity $session ;
         */
        $session->setAccessTokenIdentifier($accessTokenEntity->getUserIdentifier());
        $this->entityManager->flush();
    }

    /**
     * Revoke a session.
     *
     * @param string $sessionIdentifier
     */
    public function revokeSession(string $sessionIdentifier): void
    {
        $session = $this->entityManager->getRepository(SessionEntity::class)->find($sessionIdentifier);

        if (null === $session) {
            return;
        }
        /**
         * @var SessionEntity $session ;
         */
        $session->setRevokedAt(new DateTimeImmutable());
        $this->entityManager->flush();
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
        $session = $this->entityManager->getRepository(SessionEntity::class)->find($sessionIdentifier);
        return $session instanceof SessionEntityInterface && $session->getRevokedAt() !== null;
    }
}