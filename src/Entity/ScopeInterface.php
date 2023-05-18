<?php
/**
 * @author Steve Rhoades <sedonami@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Nerd4ever\OidcServerBundle\Entity;

interface ScopeInterface
{
    /**
     * @return string
     */
    public function getScope(): string;
}
