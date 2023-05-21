<?php
declare(strict_types=1);

namespace Nerd4ever\OidcServerBundle\Grant;

use DateInterval;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use League\OAuth2\Server\ResponseTypes\RedirectResponse;
use Nerd4ever\OidcServerBundle\OidcGrant;
use Nerd4ever\OidcServerBundle\OidcServerInterface;
use Psr\Http\Message\ServerRequestInterface;
use LogicException;

/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */
class OidcImplicitGrant extends ImplicitGrant
{
    private OidcServerInterface $oidcServer;
    private string $queryDelimiter;
    private DateInterval $accessTokenTTL;

    public function __construct(OidcServerInterface $oidcServer, DateInterval $accessTokenTTL, $queryDelimiter = '#')
    {
        parent::__construct($accessTokenTTL, $queryDelimiter);
        $this->queryDelimiter = $queryDelimiter;
        $this->accessTokenTTL = $accessTokenTTL;
        $this->oidcServer = $oidcServer;
    }

    /**
     * {@inheritdoc}
     */
    public function canRespondToAuthorizationRequest(ServerRequestInterface $request): bool
    {
        if (!isset($request->getQueryParams()['response_type']) || !isset($request->getQueryParams()['response_type'])) return false;
        $responseTypes = explode(' ', $request->getQueryParams()['response_type']);
        return !in_array('code', $responseTypes) && in_array('id_token', $responseTypes);
    }

    /**
     * Return the grant identifier that can be used in matching up requests.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return OidcGrant::IMPLICIT;
    }

    public function completeAuthorizationRequest(AuthorizationRequest $authorizationRequest): RedirectResponse
    {

        if ($authorizationRequest->getUser() instanceof UserEntityInterface === false) {
            throw new LogicException('An instance of UserEntityInterface should be set on the AuthorizationRequest');
        }

        $finalRedirectUri = ($authorizationRequest->getRedirectUri() === null)
            ? \is_array($authorizationRequest->getClient()->getRedirectUri())
                ? $authorizationRequest->getClient()->getRedirectUri()[0]
                : $authorizationRequest->getClient()->getRedirectUri()
            : $authorizationRequest->getRedirectUri();

        // The user approved the client, redirect them back with an access token
        if ($authorizationRequest->isAuthorizationApproved() === true) {
            // Finalize the requested scopes
            $finalizedScopes = $this->scopeRepository->finalizeScopes(
                $authorizationRequest->getScopes(),
                $this->getIdentifier(),
                $authorizationRequest->getClient(),
                $authorizationRequest->getUser()->getIdentifier()
            );

            $accessToken = $this->issueAccessToken(
                $this->accessTokenTTL,
                $authorizationRequest->getClient(),
                $authorizationRequest->getUser()->getIdentifier(),
                $finalizedScopes
            );
            $data = [
                'id_token' => $this->oidcServer->getNewIdToken($accessToken),
                'access_token' => (string)$accessToken,
                'token_type' => 'Bearer',
                'expires_in' => $accessToken->getExpiryDateTime()->getTimestamp() - \time(),
                'state' => $authorizationRequest->getState(),
            ];

            $response = new RedirectResponse();
            $response->setRedirectUri(
                $this->makeRedirectUri(
                    $finalRedirectUri,
                    $data,
                    $this->queryDelimiter
                )
            );

            return $response;
        }

        // The user denied the client, redirect them back with an error
        throw OAuthServerException::accessDenied(
            'The user denied the request',
            $this->makeRedirectUri(
                $finalRedirectUri,
                [
                    'state' => $authorizationRequest->getState(),
                ]
            )
        );
    }
}