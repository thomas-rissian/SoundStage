<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        // Crée un client HTTP pour simuler une requête
        $client = static::createClient();

        // Envoie une requête GET à la route '/'
        $crawler = $client->request('GET', '/');

        // Vérifie que la réponse est réussie (code HTTP 200)
        $this->assertResponseIsSuccessful();

        // Vérifie que le contenu de la réponse contient le titre "Bienvenue sur SoundStage"
        $this->assertSelectorTextContains('h1', 'Bienvenue sur SoundStage');

        // Vérifie que le contenu de la réponse contient le texte "Découvrez des artistes et événements musicaux près de chez vous"
        $this->assertSelectorTextContains('.home-banner h2', 'Découvrez des artistes et événements musicaux près de chez vous');

        // Vérifie que les boutons "Voir les artistes" et "Découvrir les événements" sont présents
        $this->assertSelectorExists('a[href="' . $client->getContainer()->get('router')->generate('app_artist_all') . '"]');
        $this->assertSelectorExists('a[href="' . $client->getContainer()->get('router')->generate('app_event_all') . '"]');
    }
}