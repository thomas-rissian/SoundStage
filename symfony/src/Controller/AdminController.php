<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/user', name: 'app_admin_user_all', methods: ['GET'])]
    public function allUser(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('admin/usersList.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/admin/user/{id}', name: 'app_admin_user_edit', methods: ['GET','POST'])]
    public function editUser(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if($user === null){
            return $this->redirectToRoute('app_admin_user_all');
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
            return $this->redirectToRoute('app_admin_user_all');

        }

        return $this->render('admin/userEdit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/admin/user/{id}/delete', name: 'app_admin_user_delete', methods: ['GET'])]
    public function deleteUser(EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if($user === null){
            return $this->redirectToRoute('app_admin_user_all');
        }
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_user_all');
    }

}
