<?php

namespace App\Controller\Api;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


final class ApiEventController extends AbstractController
{
    #[Route('/api/event', name: 'api_get_all_event', methods: ['GET'])]
    public function getAllEvent(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $events = $entityManager->getRepository(Event::class)->findAll();

        $jsonContent = $serializer->serialize($events, 'json', [
            'groups' => ['event:read'],
            'circular_reference_handler' => function ($object) {
                return $object instanceof Event ? $object->getId() : null;
            },
        ]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    #[Route('/api/event/{id}', name: 'api_event', methods: ['GET'])]
    public function getEvent(EntityManagerInterface $entityManager, SerializerInterface $serializer, $id): JsonResponse
    {
        $event = $entityManager->getRepository(Event::class)->find($id);

        $jsonContent = $serializer->serialize($event, 'json', [
            'groups' => ['event:read'],
            'circular_reference_handler' => function ($object) {
                return $object instanceof Event ? $object->getId() : null;
            },
        ]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }
}