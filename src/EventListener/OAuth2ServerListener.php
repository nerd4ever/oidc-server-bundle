<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\EventListener;

use League\Bundle\OAuth2ServerBundle\Event\TokenRequestResolveEvent;
use League\Bundle\OAuth2ServerBundle\OAuth2Events;
use League\OAuth2\Server\RequestAccessTokenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * My OAuth2ServerListener
 *
 * @package Nerd4ever\OidcServerBundle\EventListener
 * @author Sileno de Oliveira Brito
 */
final class OAuth2ServerListener implements EventSubscriberInterface
{
    public function onAccessTokenIssuedEvent(RequestAccessTokenEvent $event): void
    {

    }

    public function onTokenRequestResolveEvent(TokenRequestResolveEvent $event): void
    {

    }

    public static function getSubscribedEvents(): array
    {
        return [
            OAuth2Events::TOKEN_REQUEST_RESOLVE => 'onTokenRequestResolveEvent',
        ];
    }
}