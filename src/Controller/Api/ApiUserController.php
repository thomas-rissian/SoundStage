<?php

namespace App\Controller\Api;

use App\Entity\Artist;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiUserController extends AbstractController{
    #[Route('/api/user', name: 'app_api_get_all_user', methods: ['GET'])]
    #[OA\Get(
        path: '/api/user',
    )]
    public function getAllUser(EntityManagerInterface $entityManage, SerializerInterface $serializer): JsonResponse
    {
        $artists = $entityManage->getRepository(User::class)->findAll();

        return JsonResponse::fromJsonString($serializer->serialize($artists, 'json'));
    }
    #[Route('/api/user/{id}', name: 'app_api_get_user', methods: ['GET'])]
    #[OA\Get(
        path: '/api/user/{id}',
    )]
    public function getOneUser(EntityManagerInterface $entityManage, SerializerInterface $serializer,int $id): JsonResponse
    {
        $artists = $entityManage->getRepository(User::class)->find($id);

        return JsonResponse::fromJsonString($serializer->serialize($artists, 'json'));
    }
}
