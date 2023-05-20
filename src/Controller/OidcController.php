<?php

namespace Nerd4ever\OidcServerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * My OidcController
 *
 * @package   oidc-server-bundle
 * @author    Sileno de Oliveira Brito
 * @email     sobrito@nerd4ever.com.br
 * @copyright Copyright (c) 2023
 */
class OidcController extends AbstractController
{

    public function openidConfigurationAction(Request $request): Response
    {
        return new JsonResponse();
    }

    public function jwksUriAction(Request $request): Response
    {
        return new JsonResponse();
    }
}