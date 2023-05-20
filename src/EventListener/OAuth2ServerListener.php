<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\EventListener;

use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\RequestRefreshTokenEvent;
use Nerd4ever\OidcServerBundle\Exception\SessionIdentifierConstraintViolationExceptionNerd4ever;
use Nerd4ever\OidcServerBundle\OidcServer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * My OAuth2ServerListener
 *
 * @package Nerd4ever\OidcServerBundle\EventListener
 * @author Sileno de Oliveira Brito
 */
final class OAuth2ServerListener implements EventSubscriberInterface
{

    private OidcServer $oidcServer;

    /**
     * @param OidcServer $oidcServer
     */
    public function __construct(OidcServer $oidcServer)
    {
        $this->oidcServer = $oidcServer;
    }

    /**
     * @throws SessionIdentifierConstraintViolationExceptionNerd4ever
     */
    public function onRefreshTokenIssued(RequestRefreshTokenEvent $event): void
    {
        $this->oidcServer->getNewSession($event->getRefreshToken());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::REFRESH_TOKEN_ISSUED => 'onRefreshTokenIssued'
        ];
    }
}