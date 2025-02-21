<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApiArtistController extends AbstractController{
    #[Route('/api/artist', name: 'app_api_artist')]
    public function index(): Response
    {
        return $this->render('api_artist/index.html.twig', [
            'controller_name' => 'ApiArtistController',
        ]);
    }
}
