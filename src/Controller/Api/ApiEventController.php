<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApiEventController extends AbstractController{
    #[Route('/api/event', name: 'app_api_event')]
    public function index(): Response
    {
        return $this->render('api_event/index.html.twig', [
            'controller_name' => 'ApiEventController',
        ]);
    }
}
