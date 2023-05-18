<?php
/**
 * @author Steve Rhoades <sedonami@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Nerd4ever\OidcServerBundle\Entity;

class ClaimSetEntity implements ClaimSetEntityInterface
{
    protected string $scope;

    protected array $claims;

    public function __construct($scope, array $claims)
    {
        $this->scope = $scope;
        $this->claims = $claims;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getClaims(): array
    {
        return $this->claims;
    }
}
