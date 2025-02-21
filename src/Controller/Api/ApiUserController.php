<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApiUserController extends AbstractController{
    #[Route('/api/user', name: 'app_api_user')]
    public function index(): Response
    {
        return $this->render('api_user/index.html.twig', [
            'controller_name' => 'ApiUserController',
        ]);
    }
}
