<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class ApiEventController extends AbstractController{
    #[Route('/api/event', name: 'api_event', methods: ['GET']),]
    #[OA\Get(
        path: '/api/event',
    )]
    public function getAllEvent(): Response
    {
        return $this->render('api_event/index.html.twig', [
            'controller_name' => 'ApiEventController',
        ]);
    }
}
