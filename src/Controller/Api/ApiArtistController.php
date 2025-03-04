<?php

namespace App\Controller\Api;

use App\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiArtistController extends AbstractController
{
    #[Route('/api/artist', name: 'app_api_get_all_artist', methods: ['GET'])]
    #[OA\Get(
        path: '/api/artist',
    )]
    public function getAllArtist(EntityManagerInterface $entityManage, SerializerInterface $serializer): JsonResponse
    {
        $artists = $entityManage->getRepository(Artist::class)->findAll();

        return JsonResponse::fromJsonString($serializer->serialize($artists, 'json'));
    }

    #[Route('/api/artist/{id}', name: 'app_api_get_artist', methods: ['GET'])]
    #[OA\Get(
        path: '/api/artist/{id}',
    )]
    public function getArtist(EntityManagerInterface $entityManage, SerializerInterface $serializer, int $id): JsonResponse
    {
        $artist = $entityManage->getRepository(Artist::class)->find($id);

        return JsonResponse::fromJsonString($serializer->serialize($artist, 'json'));
    }
}
