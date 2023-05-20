<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle\Exception;
/**
 * My SessionIdentifierConstraintViolationException
 *
 * @package Nerd4ever\OidcServerBundle\Exception
 * @author Sileno de Oliveira Brito
 */
class SessionIdentifierConstraintViolationExceptionNerd4ever extends Nerd4everOidcServerException
{
    public static function create(): static
    {
        $errorMessage = 'Could not create unique session identifier';

        return new static($errorMessage, 100, 'session_identifier_duplicate', 500);
    }
}