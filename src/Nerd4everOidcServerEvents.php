<?php
/**
 * @package   oidc-server-bundle
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle;
final class Nerd4everOidcServerEvents
{
    /**
     * The ID_TOKEN_BUILDER_RESOLVE event occurs right before the system
     * complete token request.
     *
     * You could manipulate the builder.
     */
    public const ID_TOKEN_BUILDER_RESOLVE = 'nerd4ever.oidc_server.event.id_token_builder_resolve';
}