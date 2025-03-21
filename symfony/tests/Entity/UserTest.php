<?php

namespace App\Tests\Entity;

use App\Entity\Artist;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\UserEvent;
use PHPUnit\Framework\TestCase;
class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }
    public function testInitialState(): void
    {
        // Vérification de l'état initial
        $this->assertNull($this->user->getId());
        $this->assertNull($this->user->getEmail());
        $this->assertNull($this->user->getPassword());
        $this->assertNull($this->user->getPseudo());
        $this->assertCount(1,$this->user->getRoles());
        $this->assertContains('ROLE_USER', $this->user->getRoles());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->user->getUserEvents());
        $this->assertCount(0, $this->user->getUserEvents());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->user->getEvents());
        $this->assertCount(0, $this->user->getEvents());
    }
    public function testGetSetEmail(): void
    {
        $email = 'test.txt@example.com';
        $this->user->setEmail($email);
        $this->assertEquals($email, $this->user->getEmail());
    }

    public function testGetSetPseudo(): void
    {
        $pseudo = 'testuser';
        $this->user->setPseudo($pseudo);
        $this->assertEquals($pseudo, $this->user->getPseudo());
    }

    public function testGetSetPassword(): void
    {
        $password = 'password123';
        $this->user->setPassword($password);
        $this->assertEquals($password, $this->user->getPassword());
    }

    public function testGetSetRoles(): void
    {
        $roles = ['ROLE_ADMIN'];
        $this->user->setRoles($roles);
        $this->assertContains('ROLE_USER', $this->user->getRoles());
        $this->assertContains('ROLE_ADMIN', $this->user->getRoles());
    }

    public function testUserIdentifier(): void
    {
        $email = 'test.txt@example.com';
        $this->user->setEmail($email);
        $this->assertEquals($email, $this->user->getUserIdentifier());
    }

    public function testAddRemoveUserEvent(): void
    {
        $userEvent = new UserEvent();

        // Test adding a user event
        $this->user->addUserEvent($userEvent);
        $this->assertCount(1, $this->user->getUserEvents());
        $this->assertSame($this->user, $userEvent->getUserRef());

        // Test removing a user event
        $this->user->removeUserEvent($userEvent);
        $this->assertCount(0, $this->user->getUserEvents());
        $this->assertNull($userEvent->getUserRef());
    }

    public function testAddRemoveEvent(): void
    {
        $event = new Event();

        // Test adding an event
        $this->user->addEvent($event);
        $this->assertCount(1, $this->user->getEvents());
        $this->assertSame($this->user, $event->getCreatedBy());

        // Test removing an event
        $this->user->removeEvent($event);
        $this->assertCount(0, $this->user->getEvents());
        $this->assertNull($event->getCreatedBy());
    }

    public function testEraseCredentials(): void
    {
        // This method is empty in the implementation but should be tested for coverage
        $this->user->eraseCredentials();
        $this->assertTrue(true); // Just to assert something
    }
}