<?php

namespace App\Tests\Controller;

use App\Entity\Artist;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ArtistControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?UserPasswordHasherInterface $passwordHasher = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Ne pas initialiser les services ici
    }

    protected function tearDown(): void
    {
        // Nettoie la base de données après chaque test
        if ($this->entityManager) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'testuser@example.com']);
            if ($user) {
                $this->entityManager->remove($user);
            }

            $artist = $this->entityManager->getRepository(Artist::class)->findOneBy(['name' => 'Test Artist']);
            if ($artist) {
                $this->entityManager->remove($artist);
            }

            $this->entityManager->flush();
        }

        parent::tearDown();
    }

    private function createAuthenticatedClient(bool $isAdmin = false, bool $createArtist = false): object
    {
        $client = static::createClient();

        // Initialise les services après avoir appelé createClient
        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->passwordHasher = $container->get(UserPasswordHasherInterface::class);

        // Crée ou récupère l'utilisateur de test
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'testuser@example.com']);
        if (!$user) {
            $user = new User();
            $user->setEmail('testuser@example.com');
            $user->setPseudo('test');
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setRoles($isAdmin ? ['ROLE_USER', 'ROLE_ADMIN'] : ['ROLE_USER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        // Crée un artiste de test si nécessaire
        if ($createArtist) {
            // Check if test artist already exists
            $existingArtist = $this->entityManager->getRepository(Artist::class)->findOneBy(['name' => 'Test Artist']);

            if (!$existingArtist) {
                $artist = new Artist();
                $artist->setName('Test Artist');
                $this->entityManager->persist($artist);
                $this->entityManager->flush();
            }
        }

        // Connecte l'utilisateur
        $client->loginUser($user);

        return $client;
    }

    private function createTestArtist(): Artist
    {
        // Crée un artiste de test
        $artist = new Artist();
        $artist->setName('Test Artist');
        $artist->setDescription('Test Description');
        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        return $artist;
    }

    // Test pour vérifier que l'accès à la création est refusé sans ROLE_ADMIN
    public function testCreateArtistAccessDeniedForNonAdmin(): void
    {
        $client = $this->createAuthenticatedClient(false); // Utilisateur sans ROLE_ADMIN
        $client->request('GET', '/artist/create');
        $this->assertStringContainsString('ROLE_ADMIN', $client->getResponse());
    }

    // Test pour vérifier que l'accès à la création est autorisé avec ROLE_ADMIN
    public function testCreateArtistAccessGrantedForAdmin(): void
    {
        $client = $this->createAuthenticatedClient(true); // Utilisateur avec ROLE_ADMIN
        $client->request('GET', '/artist/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Création d\'un Artiste');
    }

    // Test pour vérifier que le formulaire de création fonctionne
    public function testCreateArtistFormSubmission(): void
    {
        $client = $this->createAuthenticatedClient(true); // Utilisateur avec ROLE_ADMIN

        $imagePath = __DIR__ . '/../../public/test/test.png';
        $image = new UploadedFile(
            $imagePath,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        // Soumet le formulaire
        $crawler = $client->request('GET', '/artist/create');
        $form = $crawler->selectButton('Créer')->form([
            'artist[name]' => 'New Artist',
            'artist[description]' => 'New Description',
            'artist[image]' => $image,
        ]);

        $client->submit($form);

        // Vérifie la redirection après la création
        $this->assertResponseRedirects('/artist');

        // Vérifie que l'artiste a été créé en base de données
        $artist = $this->entityManager->getRepository(Artist::class)->findOneBy(['name' => 'New Artist']);
        $this->assertNotNull($artist, 'L\'artiste n\'a pas été créé en base de données.');
        $this->assertSame('New Description', $artist->getDescription());
    }

    // Test pour vérifier que l'accès à la modification est refusé sans ROLE_ADMIN
    public function testEditArtistAccessDeniedForNonAdmin(): void
    {
        $client = $this->createAuthenticatedClient(false); // Utilisateur sans ROLE_ADMIN
        $artist = $this->createTestArtist();
        $client->request('GET', '/artist/' . $artist->getId() . '/edit');

        $this->assertStringContainsString('ROLE_ADMIN', $client->getResponse());
    }

    // Test pour vérifier que l'accès à la modification est autorisé avec ROLE_ADMIN
    public function testEditArtistAccessGrantedForAdmin(): void
    {
        $client = $this->createAuthenticatedClient(true); // Utilisateur avec ROLE_ADMIN
        $artist = $this->createTestArtist();
        $client->request('GET', '/artist/' . $artist->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Modification d\'un Artiste');
    }

    // Test pour vérifier que le formulaire de modification fonctionne
    public function testEditArtistFormSubmission(): void
    {
        $client = $this->createAuthenticatedClient(true); // Utilisateur avec ROLE_ADMIN
        $artist = $this->createTestArtist();

        $imagePath = __DIR__ . '/../../public/test/test.png';
        $image = new UploadedFile(
            $imagePath,
            'test_image.jpg',
            'image/jpeg',
            null,
            true
        );

        // Soumet le formulaire
        $crawler = $client->request('GET', '/artist/' . $artist->getId() . '/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'artist[name]' => 'Updated Artist',
            'artist[description]' => 'Updated Description',
            'artist[image]' => $image,
        ]);

        $client->submit($form);

        // Vérifie la redirection après la modification
        $this->assertResponseRedirects('/artist');

        // Vérifie que l'artiste a été modifié en base de données
        $updatedArtist = $this->entityManager->getRepository(Artist::class)->find($artist->getId());
        $this->assertSame('Updated Artist', $updatedArtist->getName());
        $this->assertSame('Updated Description', $updatedArtist->getDescription());
    }

    // Test pour vérifier que l'accès à la suppression est refusé sans ROLE_ADMIN
    public function testDeleteArtistAsUser(): void
    {
        $client = $this->createAuthenticatedClient(false, true); // Utilisateur sans ROLE_ADMIN
        $artist = $this->entityManager->getRepository(Artist::class)->findOneBy(['name' => 'Test Artist']);
        $this->assertNotNull($artist, 'L\'artiste de test n\'a pas été trouvé en base de données.');

        // Tente de supprimer l'artiste
        $client->request('GET', '/artist/' . $artist->getId() . '/delete');

        // Vérifie que l'artiste n'a pas été supprimé
        $artistStillExists = $this->entityManager->getRepository(Artist::class)->find($artist->getId());
        $this->assertNotNull($artistStillExists, 'L\'artiste ne devrait pas être supprimé par un utilisateur non admin.');
    }

    // Test pour vérifier que l'accès à la suppression est autorisé avec ROLE_ADMIN
    public function testDeleteArtistAsAdmin(): void
    {
        $client = $this->createAuthenticatedClient(true, true); // Utilisateur avec ROLE_ADMIN

        // Crée un artiste de test
        $artist = new Artist();
        $artist->setName('Test Artist');
        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        // Vérifie que l'artiste a été créé en base de données
        $artistId = $artist->getId();
        $this->assertNotNull($artistId, 'L\'artiste de test n\'a pas d\'identifiant.');

        // Tente de supprimer l'artiste
        $client->request('GET', '/artist/' . $artistId . '/delete');

        // Vérifie la redirection après la suppression
        $this->assertResponseRedirects('/artist');

        // Vérifie que l'artiste a été supprimé
        $deletedArtist = $this->entityManager->getRepository(Artist::class)->find($artistId);
        $this->assertNull($deletedArtist, 'L\'artiste devrait être supprimé par un admin.');
    }

    // Test pour vérifier la suppression d'un artiste inexistant
    public function testDeleteNonExistentArtist(): void
    {
        $client = $this->createAuthenticatedClient(true); // Utilisateur avec ROLE_ADMIN

        // Tente de supprimer un artiste inexistant
        $nonExistentId = 9999;
        $client->request('GET', '/artist/' . $nonExistentId . '/delete');

        // Vérifie la redirection
        $this->assertStringContainsString('ROLE_ADMIN', $client->getResponse());
    }
    public function testGetArtist(): void
    {
        $client = $this->createAuthenticatedClient(false);

        // Crée un artiste de test
        $artist = new Artist();
        $artist->setName('Test Artist');
        $artist->setDescription('Test Description');
        $artist->setImage('test_image.jpg');

        // Persiste l'artiste en base de données
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $entityManager->persist($artist);
        $entityManager->flush();

        // Récupère l'ID de l'artiste
        $artistId = $artist->getId();
        $this->assertNotNull($artistId, 'L\'artiste de test n\'a pas d\'identifiant.');

        // Effectue une requête GET sur la page de l'artiste
        $client->request('GET', '/artist/' . $artistId);

        // Vérifie que la réponse est réussie
        $this->assertResponseIsSuccessful();

        // Vérifie que le nom de l'artiste est présent dans la page
        $this->assertSelectorTextContains('.detail-page h1', 'Test Artist');

        // Vérifie que la description de l'artiste est présente
        $this->assertSelectorTextContains('.detail-page .description', 'Test Description');

        // Vérifie que l'image de l'artiste est présente
        $this->assertSelectorExists('.detail-page .artist-image[src*="/uploads/images/test_image.jpg"]');

        // Vérifie que la section des événements est présente
        $this->assertSelectorTextContains('.detail-page .right-column h2', 'Événements');

        // Vérifie que le nombre total d'événements est affiché
        $this->assertSelectorTextContains('.detail-page .right-column p', 'Nombre total d\'événements : 0');
    }

    public function testGetNonExistentArtist(): void
    {

        $client = $this->createAuthenticatedClient(false);
        // Tente d'accéder à un artiste inexistant
        $nonExistentId = 9999;
        $client->request('GET', '/artist/' . $nonExistentId);

        // Vérifie que la réponse est une redirection vers la liste des artistes
        $this->assertResponseRedirects('/artist');
    }
}