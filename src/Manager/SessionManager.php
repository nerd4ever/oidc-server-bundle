<?php

namespace Nerd4ever\OidcServerBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\RefreshToken;
use Nerd4ever\OidcServerBundle\Event\OidcServerBeforeSaveSessionEvent;
use Nerd4ever\OidcServerBundle\Nerd4everOidcServerEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Nerd4ever\OidcServerBundle\Model\SessionEntityInterface;
use Exception;

/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */
final class SessionManager implements SessionManagerInterface
{
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $dispatcher;
    private string $sessionClass;

    /**
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $dispatcher
     * @param string $sessionClass
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher, string $sessionClass)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->sessionClass = $sessionClass;
    }

    public function find(string $identifier): ?SessionEntityInterface
    {
        $repository = $this->entityManager->getRepository($this->sessionClass);
        return $repository->findOneBy(['identifier' => $identifier]);
    }

    public function update(SessionEntityInterface $session): void
    {
        $event = $this->dispatcher->dispatch(new OidcServerBeforeSaveSessionEvent($session), Nerd4everOidcServerEvents::BEFORE_SAVE_SESSION);
        $entity = $event->getSession();
        $this->entityManager->flush($entity);
    }

    public function save(SessionEntityInterface $session): void
    {
        $this->dispatcher->dispatch(new OidcServerBeforeSaveSessionEvent($session), Nerd4everOidcServerEvents::BEFORE_UPDATE_SESSION);
        $this->entityManager->persist($session);
        $this->entityManager->flush($session);
    }

    public function revoke(SessionEntityInterface $session): void
    {
        $session->revoke();
        $this->dispatcher->dispatch(new OidcServerBeforeSaveSessionEvent($session), Nerd4everOidcServerEvents::BEFORE_REVOKE_SESSION);
        $this->entityManager->flush();
    }

    public function findByAccessToken(string $accessTokenIdentifier): ?SessionEntityInterface
    {
        $repository = $this->entityManager->getRepository($this->sessionClass);
        try {
            return $repository->createQueryBuilder('s')
                ->innerJoin(RefreshToken::class, 'r', 'WITH', 's.refreshTokenIdentifier = r.identifier')
                ->where('r.accessToken = :accessTokenIdentifier')
                ->setParameter('accessTokenIdentifier', $accessTokenIdentifier)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (Exception $ex) {
            return null;
        }
    }

}