<?php

namespace App\Tests\Entity;

use App\Entity\Event;
use App\Entity\Artist;
use App\Entity\User;
use App\Entity\UserEvent;
use PHPUnit\Framework\TestCase;
class EventTest extends TestCase
{
    private Event $event;
    private User $user;
    private Artist $artist;

    protected function setUp(): void
    {
        $this->event = new Event();
        $this->user = new User();
        $this->artist = new Artist();
    }

    public function testGetSetName(): void
    {
        $name = 'Test Event';
        $this->event->setName($name);
        $this->assertEquals($name, $this->event->getName());
    }

    public function testGetSetDate(): void
    {
        $date = new \DateTime('2023-01-01');
        $this->event->setDate($date);
        $this->assertEquals($date, $this->event->getDate());
    }

    public function testGetSetArtist(): void
    {
        $this->event->setArtist($this->artist);
        $this->assertSame($this->artist, $this->event->getArtist());
    }

    public function testGetSetCreatedBy(): void
    {
        $this->event->setCreatedBy($this->user);
        $this->assertSame($this->user, $this->event->getCreatedBy());
    }
    public function testInitialState(): void
    {
        // Vérification de l'état initial
        $this->assertNull($this->event->getId());
        $this->assertNull($this->event->getName());
        $this->assertNull($this->event->getDate());
        $this->assertNull($this->event->getArtist());
        $this->assertNull($this->event->getCreatedBy());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->event->getUserEvents());
        $this->assertCount(0, $this->event->getUserEvents());
    }
    public function testAddRemoveUserEvent(): void
    {
        $userEvent = new UserEvent();

        // Test adding a user event
        $this->event->addUserEvent($userEvent);
        $this->assertCount(1, $this->event->getUserEvents());
        $this->assertSame($this->event, $userEvent->getEvent());

        // Test removing a user event
        $this->event->removeUserEvent($userEvent);
        $this->assertCount(0, $this->event->getUserEvents());
        $this->assertNull($userEvent->getEvent());
    }

    public function testAddUser(): void
    {
        $this->event->addUser($this->user);
        $this->assertCount(1, $this->event->getUserEvents());

        // Get the UserEvent object
        $userEvent = $this->event->getUserEvents()->first();
        $this->assertSame($this->user, $userEvent->getUserRef());
        $this->assertSame($this->event, $userEvent->getEvent());
    }

    public function testIsRegister(): void
    {
        // User is not registered initially
        $this->assertFalse($this->event->isRegister($this->user));

        // Register the user
        $this->event->addUser($this->user);

        // User should now be registered
        $this->assertTrue($this->event->isRegister($this->user));
    }

    public function testRemoveUser(): void
    {
        // Add a user
        $this->event->addUser($this->user);
        $this->assertCount(1, $this->event->getUserEvents());

        // Remove the user
        $this->event->removeUser($this->user);
        $this->assertCount(0, $this->event->getUserEvents());

        // User should not be registered anymore
        $this->assertFalse($this->event->isRegister($this->user));
    }
}
