<?php

namespace Nerd4ever\OidcServerBundle\Manager;

use Nerd4ever\OidcServerBundle\Model\SessionEntityInterface;

/**
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */
interface SessionManagerInterface
{
    public function save(SessionEntityInterface $session): void;

    public function update(SessionEntityInterface $session): void;

    public function revoke(SessionEntityInterface $session): void;

    public function find(string $identifier): ?SessionEntityInterface;

}