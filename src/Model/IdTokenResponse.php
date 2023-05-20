<?php
/**
 * @author Steve Rhoades <sedonami@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Nerd4ever\OidcServerBundle\Model;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use Nerd4ever\OidcServerBundle\OidcServer;

/**
 * My IdTokenResponse
 *
 * @package Nerd4ever\OidcServerBundle\Model
 * @author Sileno de Oliveira Brito
 */
class IdTokenResponse extends BearerTokenResponse
{
    private OidcServer $oidcServer;

    /**
     * @param OidcServer $oidcServer
     */
    public function __construct(OidcServer $oidcServer)
    {
        $this->oidcServer = $oidcServer;
    }

    protected function getExtraParams(AccessTokenEntityInterface $accessToken): array
    {
        $idToken = $this->oidcServer->getNewIdToken($accessToken);
        return null === $idToken ? [] : ['id_token' => $idToken];
    }
}
