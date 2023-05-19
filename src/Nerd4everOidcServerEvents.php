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
    /**
     * This event occurs before saving a session.
     *
     * You can use this event to perform specific actions before the session is saved, such as validating data or
     * making modifications to the session.
     */
    public const BEFORE_SAVE_SESSION = 'nerd4ever.oidc_server.event.before_save_session';
    /**
     * This event occurs before updating an existing session.
     *
     * You can use this event to perform actions before the session is updated, such as validating data or applying
     * custom logic to the session update.
     */
    public const BEFORE_UPDATE_SESSION = 'nerd4ever.oidc_server.event.before_update_session';
    /**
     * This event occurs before revoking a session.
     *
     * You can use this event to perform actions before the session is revoked, such as clearing session-related data
     * or performing additional operations before terminating the session.
     */
    public const BEFORE_REVOKE_SESSION = 'nerd4ever.oidc_server.event.before_revoke_session';

}