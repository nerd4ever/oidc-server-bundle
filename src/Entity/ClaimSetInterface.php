<?php
/**
 * @author Steve Rhoades <sedonami@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Nerd4ever\OidcServerBundle\Entity;

interface ClaimSetInterface
{
    /**
     * @return array
     */
    public function getClaims(): array;
}
