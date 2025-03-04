<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class ApiArtistController extends AbstractController
{
    #[Route('/api/artist', name: 'app_api_get_artist', methods: ['GET'])]
    #[OA\Get(
        path: '/api/artist',
    )]
    public function getAllArtist(): JsonResponse
    {
        $artists = [
            ['id' => 1, 'name' => 'Daft Punk', 'genre' => 'Electro'],
            ['id' => 2, 'name' => 'Radiohead', 'genre' => 'Rock'],
        ];

        return $this->json($artists);
    }
}
