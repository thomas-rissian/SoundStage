<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="login"]'); // Vérifie que le formulaire existe
        $this->assertSelectorExists('input[name="_username"]'); // Vérifie le champ username
        $this->assertSelectorExists('input[name="_password"]'); // Vérifie le champ password
    }

    public function testLogout(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/');
    }

    public function testRegisterPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="registrationForm"]'); // Vérifie que le formulaire existe
        $this->assertSelectorExists('input[name="registration_form[email]"]'); // Vérifie le champ email
        $this->assertSelectorExists('input[name="registration_form[plainPassword]"]'); // Vérifie le champ password
    }

    public function testRegisterFormSubmission(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        dump($client->getResponse()->getContent());

        $this->assertSelectorExists('form[name="registrationForm"]');

        $form = $crawler->selectButton('Register')->form([
            'registration_form[pseudo]' => 'testuser',
            'registration_form[email]' => 'test@example.com',
            'registration_form[plainPassword]' => 'password123',
            'registration_form[agreeTerms]' => true, // Si c'est une case à cocher
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(302);

        $this->assertResponseRedirects('/login');

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'test@example.com']);

        $this->assertNotNull($user);
        $this->assertEquals('test@example.com', $user->getEmail());

        $entityManager->remove($user);
        $entityManager->flush();
    }
}