<?php

namespace App\Controller;

use App\Entity\UserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/user/event', name: 'app_user_events')]
    public function userEvent(EntityManagerInterface $entityManager): Response
    {
        $userEvent = $entityManager->getRepository(UserEvent::class)->findBy(['userRef' => $this->getUser()]);
        return $this->render('user/event.html.twig', [
            'events' => $userEvent,
        ]);
    }

}
