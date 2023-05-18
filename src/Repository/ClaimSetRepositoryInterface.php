<?php
/**
 * @author Steve Rhoades <sedonami@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Nerd4ever\OidcServerBundle\Repository;

interface ClaimSetRepositoryInterface
{
    public function getClaimSetByScopeIdentifier($scopeIdentifier);
}
