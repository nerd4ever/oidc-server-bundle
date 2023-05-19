<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\Event;

use Nerd4ever\OidcServerBundle\Model\SessionEntityInterface;

/**
 * My OidcServerBeforeSaveSessionEvent
 *
 * @package Nerd4ever\OidcServerBundle\Event
 * @author Sileno de Oliveira Brito
 */
class OidcServerBeforeSaveSessionEvent
{
    private SessionEntityInterface $session;

    /**
     * @param SessionEntityInterface $session
     */
    public function __construct(SessionEntityInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return SessionEntityInterface
     */
    public function getSession(): SessionEntityInterface
    {
        return $this->session;
    }

}