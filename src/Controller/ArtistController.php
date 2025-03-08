<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Form\ArtistType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ArtistController extends AbstractController{
    #[Route('/artist', name: 'app_artist_all', methods: ['GET'])]
    public function artist(EntityManagerInterface $entityManager): Response
    {
        $artists = $entityManager->getRepository(Artist::class)->findAll();
        return $this->render('artist/index.html.twig', [
            'artists' => $artists,
        ]);

    }

    #[Route('/artist/create', name: 'app_artist_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createArtist(Request $request,EntityManagerInterface $entityManager): Response
    {
        $artist = new Artist();

        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $entityManager->persist($artist);
            $entityManager->flush();
            return $this->redirectToRoute('app_artist_all');

        }

        return $this->render('artist/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/artist/{id}/edit', name: 'app_artist_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editArtist(Request $request,EntityManagerInterface $entityManager, int $id): Response
    {
        $artist = $entityManager->getRepository(Artist::class)->find($id);
        if($artist === null){
            return $this->redirectToRoute('app_artist_all');
        }
        $form = $this->createForm(ArtistType::class, $artist);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $entityManager->persist($artist);
            $entityManager->flush();
            return $this->redirectToRoute('app_artist_all');

        }

        return $this->render('artist/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/artist/{id}', name: 'app_artist_one', methods: ['GET'])]
    public function getArtiste(EntityManagerInterface $entityManager, int $id): Response
    {
        $artist = $entityManager->getRepository(Artist::class)->find($id);
        if($artist === null){
            return $this->redirectToRoute('app_artist_all');
        }
        return $this->render('artist/artist.html.twig', [
            'artist' => $artist,
        ]);
    }
    #[Route('/artist/{id}/delete', name: 'app_artist_delete', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteArtiste(EntityManagerInterface $entityManager, int $id): Response
    {
        $artist = $entityManager->getRepository(Artist::class)->find($id);
        if($artist === null){
            return $this->redirectToRoute('app_artist_all');
        }
        $entityManager->remove($artist);
        $entityManager->flush();

        return $this->redirectToRoute('app_artist_all');
    }
}
