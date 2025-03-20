<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\UserEvent;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EventController extends AbstractController{
    #[Route('/event', name: 'app_event_all')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }
    #[Route('/event/create', name: 'app_event_create', methods: ['GET', 'POST'])]
    public function createEvent(Request $request,EntityManagerInterface $entityManager): Response
    {
        $event = new Event();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $event->setCreatedBy($this->getUser());
            $event->addUser($this->getUser());
            $entityManager->persist($event);
            $entityManager->flush();
            return $this->redirectToRoute('app_event_all');

        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView(),
            'title' => "Création",
            "ButtonName" => "Créer",
        ]);
    }
    #[Route('/event/search', name: 'app_event_search', methods: ['GET', 'POST'])]
    public function searchEvent(Request $request,EntityManagerInterface $entityManager): Response
    {
        $date = $request->get('date');
        if(!$date)
            $date = "";

        $events = $entityManager->getRepository(Event::class)->findByDate($date);

        return $this->render('event/index.html.twig', ['events' => $events,'date'=>$date]);
    }
    #[Route('/event/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function editEvent(Request $request,EntityManagerInterface $entityManager, int $id): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        if($event === null){
            return $this->redirectToRoute('app_event_all');
        }
        if($event->getCreatedBy() === $this->getUser() || $this->isGranted("ROLE_ADMIN")) {

            $form = $this->createForm(EventType::class, $event);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($event);
                $entityManager->flush();
                return $this->redirectToRoute('app_event_all');

            }
            return $this->render('event/create.html.twig', [
                'form' => $form->createView(),
                'title' => "Modification",
                "ButtonName" => "Modifier",
            ]);
        }else
            return $this->redirectToRoute('app_event_all');
    }
    #[Route('/event/{id}', name: 'app_event_one', methods: ['GET'])]
    public function getEvent(EntityManagerInterface $entityManager, int $id): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        if($event === null){
            return $this->redirectToRoute('app_event_all');
        }
        return $this->render('event/event.html.twig', [
            'event' => $event,
        ]);
    }
    #[Route('/event/{id}/delete', name: 'app_event_delete', methods: ['GET'])]
    public function deleteEvent(EntityManagerInterface $entityManager, int $id): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        if($event === null){
            return $this->redirectToRoute('app_event_all');
        }
        if($event->getCreatedBy() === $this->getUser() || $this->isGranted("ROLE_ADMIN")) {
            $entityManager->remove($event);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_event_all');
    }
    #[Route('/event/{id}/register', name: 'app_event_register', methods: ['GET'])]
    public function registerEventUser(EntityManagerInterface $entityManager, int $id): Response
    {
        $event = $entityManager->getRepository(Event::class)->find($id);
        if($event === null){
            return $this->redirectToRoute('app_event_all');
        }
        $user = $this->getUser();
        if(!$event->isRegister($user)){
            $event->addUser($user);
            $entityManager->persist($event);
        }else{
            $userEvent = $entityManager->getRepository(UserEvent::class)
                ->findOneBy( ['userRef' => $user, 'event' => $event]);
            $entityManager->remove($userEvent);
        }
        $entityManager->flush();
        return $this->redirectToRoute("app_event_all");
    }
}
