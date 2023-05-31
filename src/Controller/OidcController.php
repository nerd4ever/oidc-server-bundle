<?php

namespace Nerd4ever\OidcServerBundle\Controller;

use League\Bundle\OAuth2ServerBundle\Security\Authentication\Token\OAuth2Token;
use Nerd4ever\OidcServerBundle\Entity\ClaimSetInterface;
use Nerd4ever\OidcServerBundle\Model\ClaimExtractor;
use Nerd4ever\OidcServerBundle\OidcServerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use RuntimeException;
use Exception;

/**
 * My OidcController
 *
 * @package   oidc-server-bundle
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */
final class OidcController
{

    public function __construct(
        private readonly RouterInterface       $router,
        private readonly ParameterBagInterface $params,
        private readonly ClaimExtractor        $claimExtractor,
        private readonly OidcServerInterface   $oidcServer,
        private readonly Security              $security,
    )
    {
    }

    public function openidConfigurationAction(Request $request): Response
    {
        $issuer = $request->getSchemeAndHttpHost();
        $authorization = $this->params->get('nerd4ever.oidc_server.discovery.authorization');
        $userinfo = $this->params->get('nerd4ever.oidc_server.discovery.userinfo');
        $extraScopes = $this->params->get('nerd4ever.oidc_server.scope.extras');
        $data = [
            'issuer' => $issuer,
            'token_endpoint' => $issuer . $this->router->generate('oauth2_token'),
            'authorization_endpoint' => $issuer . $this->router->generate($authorization),
            'userinfo_endpoint' => $issuer . $this->router->generate($userinfo),
            'revocation_endpoint' => $issuer . $this->router->generate('oauth2_sign_out'),
            'jwks_uri' => $issuer . $this->router->generate('oidc_jwks_uri'),
            "response_types_supported" => [
                "code",
                "token",
                "id_token",
                "id_token token"
            ],
            "subject_types_supported" => [
                "public"
            ],
            "scopes_supported" =>
                array_merge(['openid'],
                    $this->claimExtractor->extractAllScopes(),
                    $extraScopes
                ),
            "token_endpoint_auth_methods_supported" => [
                "client_secret_post",
                "client_secret_basic",
                "client_secret_jwt",
                "private_key_jwt"
            ], "grant_types_supported" => [
                "refresh_token",
                "client_credentials",
                "authorization_code",
                "implicit",
                "password"
            ],
            "id_token_signing_alg_values_supported" => ["RS256"],
            "id_token_encryption_alg_values_supported" => ["RSA-OAEP", "RSA-OAEP-256", "RSA1_5"],
            "id_token_encryption_enc_values_supported" => ["A128CBC-HS256", "A192CBC-HS384", "A256CBC-HS512", "A128GCM", "A192GCM", "A256GCM"],
            "response_modes_supported" => ["query", "fragment", "form_post"],
            "claims_supported" => $this->claimExtractor->extractAllClaimSet(),
            "claims_parameter_supported" => false,
            "request_parameter_supported" => false,
            "request_uri_parameter_supported" => false
        ];

        return new JsonResponse($data);
    }

    public function jwksUriAction(Request $request): Response
    {
        try {
            return new JsonResponse($this->oidcServer->getJWKSet());
        } catch (Exception $ex) {
            return $this->exceptionAction($request, $ex);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function userAction(Request $request): Response
    {
        try {
            $user = $this->security->getUser();
            if (!$user instanceof ClaimSetInterface) {
                throw new RuntimeException('user not supported by oidc', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $token = $this->security->getToken();
            if (!$token instanceof OAuth2Token) {
                throw new RuntimeException('token not supported by oidc', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $claims = $this->claimExtractor->extract($token->getScopes(), $user->getClaims());
            return new JsonResponse($claims);
        } catch (Exception $ex) {
            return $this->exceptionAction($request, $ex);
        }
    }

    private function exceptionAction(Request $request, Exception $ex): Response
    {
        return new JsonResponse([
            'error' => $ex->getMessage(),
            'code' => $ex->getCode(),
            'address' => $request->getClientIp()
        ]);
    }
}