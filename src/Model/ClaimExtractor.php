<?php
/**
 * @author Steve Rhoades <sedonami@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Nerd4ever\OidcServerBundle\Model;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use Nerd4ever\OidcServerBundle\Entity\ClaimSetEntity;
use Nerd4ever\OidcServerBundle\Entity\ClaimSetEntityInterface;
use Nerd4ever\OidcServerBundle\Exception\InvalidArgumentException;

class ClaimExtractor
{
    protected array $claimSets = [];

    protected array $protectedClaims = ['profile', 'email', 'address', 'phone'];

    /**
     * ClaimExtractor constructor.
     *
     * @param ClaimSetEntity[] $claimSets
     */
    public function __construct(array $claimSets = [])
    {

    }

    /**
     * @param ClaimSetEntityInterface $claimSet
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addClaimSet(ClaimSetEntityInterface $claimSet): static
    {
        $scope = $claimSet->getScope();

        if (in_array($scope, $this->protectedClaims) && !empty($this->claimSets[$scope])) {
            throw new InvalidArgumentException(
                sprintf("%s is a protected scope and is pre-defined by the OpenID Connect specification.", $scope)
            );
        }

        $this->claimSets[$scope] = $claimSet;

        return $this;
    }

    /**
     * @param string $scope
     * @return ClaimSetEntity|null
     */
    public function getClaimSet(string $scope): ?ClaimSetEntity
    {
        if (!$this->hasClaimSet($scope)) {
            return null;
        }

        return $this->claimSets[$scope];
    }

    /**
     * @param string $scope
     * @return bool
     */
    public function hasClaimSet(string $scope): bool
    {
        return array_key_exists($scope, $this->claimSets);
    }


    public function extractAllScopes(): array
    {
        return array_keys($this->claimSets);
    }

    public function extractAllClaimSet(): array
    {
        $out = [];
        foreach ($this->claimSets as $c) {
            /**
             * @var ClaimSetEntity $c
             */
            $out = array_merge($c->getClaims(), $out);
        }
        return array_unique($out);
    }

    /**
     * For given scopes and aggregated claims get all claims that have been configured on the extractor.
     *
     * @param array $scopes
     * @param array $claims
     * @return array
     */
    public function extract(array $scopes, array $claims): array
    {
        $claimData = [];
        $keys = array_keys($claims);

        foreach ($scopes as $scope) {
            $scopeName = ($scope instanceof ScopeEntityInterface) ? $scope->getIdentifier() : $scope;

            $claimSet = $this->getClaimSet($scopeName);
            if (null === $claimSet) {
                continue;
            }

            $intersected = array_intersect($claimSet->getClaims(), $keys);

            if (empty($intersected)) {
                continue;
            }

            $data = array_filter($claims,
                function ($key) use ($intersected) {
                    return in_array($key, $intersected);
                },
                ARRAY_FILTER_USE_KEY
            );

            $claimData = array_merge($claimData, $data);
        }

        return $claimData;
    }
}
