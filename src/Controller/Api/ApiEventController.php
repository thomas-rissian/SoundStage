<?php

namespace App\Controller\Api;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiEventController extends AbstractController{
    #[Route('/api/event', name: 'api_get_all_event', methods: ['GET']),]
    #[OA\Get(
        path: '/api/event',
    )]
    public function getAllEvent(EntityManagerInterface $entityManage, SerializerInterface $serializer): JsonResponse
    {
        $events = $entityManage->getRepository(Event::class)->findAll();
        return JsonResponse::fromJsonString($serializer->serialize($events, 'json'));
    }

    #[Route('/api/event/{id}', name: 'api_event', methods: ['GET']),]
    #[OA\Get(
        path: '/api/event/{id}',
    )]
    public function getEvent(EntityManagerInterface $entityManage, SerializerInterface $serializer, $id): JsonResponse
    {
        $event = $entityManage->getRepository(Event::class)->find($id);
        return JsonResponse::fromJsonString($serializer->serialize($event, 'json'));
    }
}
