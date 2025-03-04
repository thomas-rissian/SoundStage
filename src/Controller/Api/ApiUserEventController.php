<?php

namespace App\Controller\Api;

use App\Entity\Artist;
use App\Entity\UserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiUserEventController extends AbstractController{

    #[Route('/api/user/event', name: 'app_api_get_all_user_event', methods: ['GET'])]
    #[OA\Get(
        path: '/api/user/event',
    )]
    public function getAllUserEvent(EntityManagerInterface $entityManage, SerializerInterface $serializer): JsonResponse
    {
        $userEvents = $entityManage->getRepository(UserEvent::class)->findAll();

        return JsonResponse::fromJsonString($serializer->serialize($userEvents, 'json'));
    }

    #[Route('/api/user/event/{id}', name: 'app_api_get_user_event', methods: ['GET'])]
    #[OA\Get(
        path: '/api/user/event/{id}',
    )]
    public function getUserEvent(EntityManagerInterface $entityManage, SerializerInterface $serializer, int $id): JsonResponse
    {
        $userEvent = $entityManage->getRepository(UserEvent::class)->find($id);

        return JsonResponse::fromJsonString($serializer->serialize($userEvent, 'json'));
    }
}
