<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ArtistControllerTest extends WebTestCase
{
    private function createAuthenticatedClient(bool $createArtist = false): object
    {
        // Create a fresh client for each test
        $client = static::createClient();

        // Get services from container
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        // Check if user already exists
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'testuser@example.com']);

        if (!$user) {
            // Create test user only if it doesn't exist
            $user = new User();
            $user->setEmail('testuser@example.com');
            $user->setPseudo('test');
            $user->setPassword($passwordHasher->hashPassword($user, 'password123'));
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();
        }

        // Create test artist if needed
        if ($createArtist) {
            // Check if test artist already exists
            $existingArtist = $entityManager->getRepository(Artist::class)->findOneBy(['name' => 'Test Artist']);

            if (!$existingArtist) {
                $artist = new Artist();
                $artist->setName('Test Artist');
                $entityManager->persist($artist);
                $entityManager->flush();
            }
        }

        // Login the user
        $client->loginUser($user);

        return $client;
    }

    public function testArtistPageRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/artist');

        $this->assertResponseRedirects('/');
    }

    public function testArtistPageAccessibleToUser(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/artist');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Artistes');
    }

    public function testArtistListDisplaysArtists(): void
    {
        // Crée un client authentifié et un artiste de test
        $client = $this->createAuthenticatedClient(true);

        // Récupère l'EntityManager pour accéder à la base de données
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // Récupère l'artiste de test créé dans createAuthenticatedClient
        $artist = $entityManager->getRepository(Artist::class)->findOneBy(['name' => 'Test Artist']);
        $this->assertNotNull($artist, 'L\'artiste de test n\'a pas été trouvé en base de données.');

        // Effectue une requête GET sur la page de l'artiste
        $client->request('GET', '/artist/' . $artist->getId());

        // Pour le debug, sauvegarde le contenu HTML dans un fichier
        file_put_contents('debug.html', $client->getResponse()->getContent());

        // Vérifie que la réponse est réussie
        $this->assertResponseIsSuccessful();

        // Vérifie que le nom de l'artiste est présent dans la page
        $this->assertSelectorTextContains('.detail-page h1', 'Test Artist');

        // Vérifie que la description de l'artiste est présente (si elle est définie)
        if ($artist->getDescription()) {
            $this->assertSelectorTextContains('.detail-page .description', $artist->getDescription());
        }

        // Vérifie que l'image de l'artiste est présente (si elle est définie)
        if ($artist->getImage()) {
            $this->assertSelectorExists('.detail-page .artist-image[src*="' . $artist->getImage() . '"]');
        }

        // Vérifie que la section des événements est présente
        $this->assertSelectorTextContains('.detail-page .right-column h2', 'Événements');

        // Vérifie que le nombre total d'événements est affiché
        $this->assertSelectorTextContains('.detail-page .right-column p', 'Nombre total d\'événements : ' . count($artist->getEvents()));

        // Vérifie que chaque événement est listé
        foreach ($artist->getEvents() as $event) {
            $this->assertSelectorTextContains('.detail-page .right-column ul', $event->getName());
        }
    }
    public function testArtistSearch(): void
    {
        // Crée un client authentifié
        $client = $this->createAuthenticatedClient();

        // Récupère l'EntityManager pour accéder à la base de données
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // Crée des artistes de test
        $artist1 = new Artist();
        $artist1->setName('Artist One');
        $entityManager->persist($artist1);

        $artist2 = new Artist();
        $artist2->setName('Artist Two');
        $entityManager->persist($artist2);

        $entityManager->flush();

        // Effectue une requête GET sur /artist/search avec un paramètre de recherche
        $client->request('GET', '/artist/search', ['name' => 'Artist']);

        // Vérifie que la réponse est réussie
        $this->assertResponseIsSuccessful();

        // Vérifie que les artistes sont présents dans la réponse
        $this->assertSelectorTextContains('body', 'Artist One');
        $this->assertSelectorTextContains('body', 'Artist Two');

        // Effectue une requête GET avec un nom spécifique
        $client->request('GET', '/artist/search', ['name' => 'One']);

        // Vérifie que la réponse est réussie
        $this->assertResponseIsSuccessful();

        // Vérifie que seul "Artist One" est présent dans la réponse
        $this->assertSelectorTextContains('body', 'Artist One');
        $this->assertSelectorTextNotContains('body', 'Artist Two');
    }

    public function testDeleteArtistAsAdmin(): void
    {
        // Create an admin authenticated client
        $client = $this->createAuthenticatedClient(true);

        // Get services from container
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);

        // Get the current user and make them an admin
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'testuser@example.com']);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $entityManager->flush();

        // Reload the client with admin user
        $client->loginUser($user);

        // Get the artist to delete
        $artist = $entityManager->getRepository(Artist::class)->findOneBy(['name' => 'Test Artist']);
        $this->assertNotNull($artist, 'The test artist was not found in the database.');
        $artistId = $artist->getId();

        // Request the delete route
        $client->request('GET', '/artist/' . $artistId . '/delete');

        // Should redirect to the artist list
        $this->assertResponseRedirects('/artist');

        // Follow the redirect
        $client->followRedirect();

        // Verify the redirect landed on the correct page
        $this->assertSelectorTextContains('h1', 'Artistes');

        // Verify the artist no longer exists in the database
        $deletedArtist = $entityManager->getRepository(Artist::class)->find($artistId);
        $this->assertNull($deletedArtist, 'The artist should be deleted from the database.');
    }

    public function testDeleteNonExistentArtist(): void
    {
        // Create an admin authenticated client
        $client = $this->createAuthenticatedClient();

        // Get the current user and make them an admin
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'testuser@example.com']);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $entityManager->flush();

        // Reload the client with admin user
        $client->loginUser($user);

        // Try to delete a non-existent artist with an ID that shouldn't exist
        $nonExistentId = 9999;
        $client->request('GET', '/artist/' . $nonExistentId . '/delete');

        // Should redirect to the artist list
        $this->assertResponseRedirects('/artist');
    }
    public function testDeleteArtistAsUser(): void
    {
        // Crée un client authentifié avec un utilisateur normal (ROLE_USER)
        $client = $this->createAuthenticatedClient(true);
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'testuser@example.com']);
        $user->setRoles(['ROLE_USER']);
        $entityManager->flush();

        // Reload the client with admin user
        $client->loginUser($user);
        // Récupère l'EntityManager pour accéder à la base de données
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // Récupère l'artiste à supprimer
        $artist = $entityManager->getRepository(Artist::class)->findOneBy(['name' => 'Test Artist']);
        $this->assertNotNull($artist, 'L\'artiste de test n\'a pas été trouvé en base de données.');
        $artistId = $artist->getId();

        // Tente de supprimer l'artiste
        $client->request('GET', '/artist/' . $artistId . '/delete');

        // Vérifie que l'artiste n'a pas été supprimé
        $artistStillExists = $entityManager->getRepository(Artist::class)->find($artistId);
        $this->assertNotNull($artistStillExists, 'L\'artiste ne devrait pas être supprimé par un utilisateur non admin.');
    }
}