<?php

namespace App\Tests\Form;

use App\Entity\Artist;
use App\Entity\Event;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use DateTime;

class EventTypeTest extends KernelTestCase
{
    private $entityManager;
    private $formFactory;
    private $artist;

    protected function setUp(): void
    {
        // Démarrage du kernel Symfony pour accéder aux services
        self::bootKernel();

        // Récupération de l'EntityManager
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // Récupération de la factory de formulaires
        $this->formFactory = static::getContainer()->get(FormFactoryInterface::class);

        // Création d'un artiste de test dans la base de données
        $this->artist = new Artist();
        $this->artist->setName('Test Artist For Form');
        $this->artist->setDescription('Artist created for testing forms');
        $this->artist->setImage('default-image.jpg'); // Ajout d'une valeur par défaut pour l'image

        // Persistance de l'artiste dans la base de données
        $this->entityManager->persist($this->artist);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        // Suppression de l'artiste de test après le test
        if ($this->artist && $this->artist->getId()) {
            $this->entityManager->remove($this->artist);
            $this->entityManager->flush();
        }

        // Fermeture de l'EntityManager
        $this->entityManager->close();
        $this->entityManager = null;

        // Fermeture du kernel
        parent::tearDown();
    }

    public function testSubmitValidData(): void
    {
        // Création d'un formulaire EventType
        $form = $this->formFactory->create(EventType::class);

        // La date à utiliser dans le test
        $testDate = new DateTime('2025-04-20');

        // Soumission des données de formulaire
        $form->submit([
            'name' => 'Test Concert Event',
            'date' => $testDate->format('Y-m-d'),
            'artist' => $this->artist->getId(), // On utilise l'ID de l'artiste créé
        ]);

        // Vérification que le formulaire est valide
        $this->assertTrue($form->isValid());

        // Récupération de l'objet Event
        $event = $form->getData();

        // Vérification des données
        $this->assertInstanceOf(Event::class, $event);
        $this->assertEquals('Test Concert Event', $event->getName());
        $this->assertEquals($testDate->format('Y-m-d'), $event->getDate()->format('Y-m-d'));
        $this->assertEquals($this->artist->getId(), $event->getArtist()->getId());
    }

    public function testFormStructure(): void
    {
        // Création d'un formulaire EventType
        $form = $this->formFactory->create(EventType::class);

        // Vérification de la structure du formulaire
        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('date'));
        $this->assertTrue($form->has('artist'));

        // Vérification des options du champ date
        $dateField = $form->get('date');
        $dateOptions = $dateField->getConfig()->getOptions();
        $this->assertEquals('single_text', $dateOptions['widget']);

        // Vérification du champ artist
        $artistField = $form->get('artist');
        $artistOptions = $artistField->getConfig()->getOptions();
        $this->assertEquals(Artist::class, $artistOptions['class']);
        $this->assertEquals('name', $artistOptions['choice_label']);
    }

    public function testInitialData(): void
    {
        // Création d'un Event avec des données initiales
        $event = new Event();
        $event->setName('Existing Event');
        $event->setDate(new DateTime('2025-05-15'));
        $event->setArtist($this->artist);

        // Création du formulaire avec l'Event existant
        $form = $this->formFactory->create(EventType::class, $event);

        // Vérification des données initiales
        $this->assertEquals('Existing Event', $form->get('name')->getData());
        $this->assertEquals('2025-05-15', $form->get('date')->getData()->format('Y-m-d'));
        $this->assertEquals($this->artist->getId(), $form->get('artist')->getData()->getId());
    }
}