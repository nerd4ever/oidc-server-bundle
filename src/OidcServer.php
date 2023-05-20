<?php
/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */

namespace Nerd4ever\OidcServerBundle;

use Defuse\Crypto\Key;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Key\LocalFileReference;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Builder;
use League\Bundle\OAuth2ServerBundle\Manager\AccessTokenManagerInterface;
use League\Bundle\OAuth2ServerBundle\Manager\RefreshTokenManagerInterface;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Nerd4ever\OidcServerBundle\Entity\ClaimSetInterface;
use Nerd4ever\OidcServerBundle\Event\OidcServerIdTokenBuilderResolveEvent;
use Nerd4ever\OidcServerBundle\Exception\SessionIdentifierConstraintViolationExceptionNerd4ever;
use Nerd4ever\OidcServerBundle\Model\ClaimExtractor;
use Nerd4ever\OidcServerBundle\Model\SessionEntityInterface;
use Nerd4ever\OidcServerBundle\Repository\IdentityProviderInterface;
use Nerd4ever\OidcServerBundle\Repository\SessionRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * My OidcServer
 *
 * @package Nerd4ever\OidcServerBundle
 * @author Sileno de Oliveira Brito
 */
final class OidcServer implements OidcServerInterface
{
    use CryptTrait;

    private ?CryptKey $privateKey;
    private EventDispatcherInterface $eventDispatcher;
    private IdentityProviderInterface $identityProvider;
    private ClaimExtractor $claimExtractor;
    private RequestStack $requestStack;
    private SessionRepositoryInterface $sessionRepository;
    private RefreshTokenManagerInterface $refreshTokenManager;
    private AccessTokenManagerInterface $accessTokenManager;

    public function __construct(
        string|Key|null              $encryptionKey,
        CryptKey|null                $privateKey,
        RequestStack                 $requestStack,
        IdentityProviderInterface    $identityProvider,
        ClaimExtractor               $claimExtractor,
        EventDispatcherInterface     $eventDispatcher,
        SessionRepositoryInterface   $sessionRepository,
        RefreshTokenManagerInterface $refreshTokenManager,
        AccessTokenManagerInterface  $accessTokenManager
    )
    {
        $this->setEncryptionKey($encryptionKey);
        $this->privateKey = $privateKey;
        $this->requestStack = $requestStack;
        $this->identityProvider = $identityProvider;
        $this->claimExtractor = $claimExtractor;
        $this->eventDispatcher = $eventDispatcher;
        $this->sessionRepository = $sessionRepository;
        $this->accessTokenManager = $accessTokenManager;
        $this->refreshTokenManager = $refreshTokenManager;
    }

    /**
     * @throws SessionIdentifierConstraintViolationExceptionNerd4ever
     */
    public function getNewSession(RefreshTokenEntityInterface $refreshToken): ?SessionEntityInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        $userAgent = $request->headers->get('User-Agent');
        $userAddress = $request->getClientIp();

        $session = $this->sessionRepository->getNewSession(
            $refreshToken,
            Uuid::uuid4()->toString(),
            $userAgent,
            $userAddress
        );
        $this->sessionRepository->persistNewSession($session);
        return $session;
    }

    private function getBuilder(AccessTokenEntityInterface $accessToken, UserEntityInterface $userEntity): Builder
    {
        $claimsFormatter = ChainedFormatter::withUnixTimestampDates();
        $builder = new Builder(new JoseEncoder(), $claimsFormatter);

        // Since version 8.0 league/oauth2-server returns \DateTimeImmutable
        $expiresAt = $accessToken->getExpiryDateTime();
        if ($expiresAt instanceof \DateTime) {
            $expiresAt = \DateTimeImmutable::createFromMutable($expiresAt);
        }

        // Add required id_token claims
        $builder
            ->permittedFor($accessToken->getClient()->getIdentifier())
            ->issuedBy('https://' . $_SERVER['HTTP_HOST'])
            ->issuedAt(new \DateTimeImmutable())
            ->expiresAt($expiresAt)
            ->relatedTo($userEntity->getIdentifier());

        return $builder;
    }

    /**
     * @param AccessTokenEntityInterface $accessToken
     * @return string|null
     */
    public function getNewIdToken(AccessTokenEntityInterface $accessToken): ?string
    {
        if (false === $this->isOpenIDRequest($accessToken->getScopes())) {
            return null;
        }

        /** @var UserEntityInterface $userEntity */
        $userEntity = $this->identityProvider->getUserEntityByIdentifier($accessToken->getUserIdentifier());

        if (false === is_a($userEntity, UserEntityInterface::class)) {
            throw new \RuntimeException('UserEntity must implement UserEntityInterface');
        } else if (false === is_a($userEntity, ClaimSetInterface::class)) {
            throw new \RuntimeException('UserEntity must implement ClaimSetInterface');
        }

        // Add required id_token claims
        $builder = $this->getBuilder($accessToken, $userEntity);

        // Need a claim factory here to reduce the number of claims by provided scope.
        $claims = $this->claimExtractor->extract($accessToken->getScopes(), $userEntity->getClaims());

        foreach ($claims as $claimName => $claimValue) {
            $builder = $builder->withClaim($claimName, $claimValue);
        }

        $session = $this->sessionRepository->findByAccessToken($accessToken->getUserIdentifier());

        if (null !== $session) {
            $builder = $builder->withClaim('sid', $session->getIdentifier());
        }
        if (
            method_exists($this->privateKey, 'getKeyContents')
            && !empty($this->privateKey->getKeyContents())
        ) {
            $key = InMemory::plainText($this->privateKey->getKeyContents(), (string)$this->privateKey->getPassPhrase());
        } else {
            $key = LocalFileReference::file($this->privateKey->getKeyPath(), (string)$this->privateKey->getPassPhrase());
        }

        $builder->withHeader('kid', $this->getKeyId());

        $this->eventDispatcher->dispatch(
            new OidcServerIdTokenBuilderResolveEvent($builder, $accessToken, $userEntity),
            Nerd4everOidcServerEvents::ID_TOKEN_BUILDER_RESOLVE
        );
        $token = $builder->getToken(new Sha256(), $key);
        return $token->toString();
    }

    /**
     * @param ScopeEntityInterface[] $scopes
     * @return bool
     */
    private function isOpenIDRequest(array $scopes): bool
    {
        // Verify scope and make sure openid exists.
        $valid = false;

        foreach ($scopes as $scope) {
            if ($scope->getIdentifier() === 'openid') {
                $valid = true;
                break;
            }
        }
        return $valid;
    }

    public function getKeyId(): string
    {

        $privateKey = $this->privateKey->getKeyContents();

        $hash = hash('sha256', $privateKey);

        // Definir a versão 4 do UUID
        $version = hexdec($hash[12]) & 0x0F | 0x40;
        $variant = hexdec($hash[16]) & 0x3F | 0x80;

        // Formatar no padrão UUID
        return vsprintf('%08s-%04s-%04x-%02x%02x-%012s', [
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            hexdec(substr($hash, 13, 3)) & 0x0FFF | 0x4000,
            $version,
            $variant,
            substr($hash, 16, 12),
        ]);
    }

}