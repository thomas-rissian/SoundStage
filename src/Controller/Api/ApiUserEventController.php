<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApiUserEventController extends AbstractController{
    #[Route('/api/user/event', name: 'app_api_user_event')]
    public function index(): Response
    {
        return $this->render('api_user_event/index.html.twig', [
            'controller_name' => 'ApiUserEventController',
        ]);
    }
}
