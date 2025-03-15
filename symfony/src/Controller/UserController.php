<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserEvent;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    #[Route('/user/edit', name: 'app_user_edit')]
    public function edit(Request $request,EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        if($user === null){
            return $this->redirectToRoute('app_user');
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $plainPassword = $form->get('password')->getData();

            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user');

        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/user/delete', name: 'app_user_delete')]
    public function delete(EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser());
        if($user === null){
            return $this->redirectToRoute('app_default');
        }
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_logout');
    }
}
