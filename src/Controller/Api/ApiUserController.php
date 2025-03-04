<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class ApiUserController extends AbstractController{
    #[Route('/api/user', name: 'api_user', methods: ['GET'])]
    #[OA\Get(
        path: '/api/user',
    )]
    public function getAllUser(): Response
    {
        return $this->render('api_user/index.html.twig', [
            'controller_name' => 'ApiUserController',
        ]);
    }
}
