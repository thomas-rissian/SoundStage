<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class ApiUserEventController extends AbstractController{
    #[Route('/api/user/event', name: 'api_user_event', methods: ['GET'])]
    #[OA\Get(
        path: '/api/user/event',
    )]
    public function getAllUserEvent(): Response
    {
        return $this->render('api_user_event/index.html.twig', [
            'controller_name' => 'ApiUserEventController',
        ]);
    }
}
