<?php

namespace App\Tests\Entity;

use App\Entity\Artist;
use App\Entity\Event;
use App\Entity\User;
use PHPUnit\Framework\TestCase;


class ArtistTest extends TestCase
{
    private Artist $artist;

    protected function setUp(): void
    {
        $this->artist = new Artist();
    }
    public function testInitialState(): void
    {
        // Vérification de l'état initial
        $this->assertNull($this->artist->getId());
        $this->assertNull($this->artist->getName());
        $this->assertNull($this->artist->getImage());
        $this->assertNull($this->artist->getDescription());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $this->artist->getEvents());
        $this->assertCount(0, $this->artist->getEvents());
    }
    public function testGetSetName(): void
    {
        $name = 'Test Artist';
        $this->artist->setName($name);
        $this->assertEquals($name, $this->artist->getName());
    }

    public function testGetSetImage(): void
    {
        $image = 'image.jpg';
        $this->artist->setImage($image);
        $this->assertEquals($image, $this->artist->getImage());
    }

    public function testGetSetDescription(): void
    {
        $description = 'Test description';
        $this->artist->setDescription($description);
        $this->assertEquals($description, $this->artist->getDescription());
    }

    public function testAddRemoveEvent(): void
    {
        $event = new Event();

        // Test adding an event
        $this->artist->addEvent($event);
        $this->assertCount(1, $this->artist->getEvents());
        $this->assertSame($this->artist, $event->getArtist());

        // Test removing an event
        $this->artist->removeEvent($event);
        $this->assertCount(0, $this->artist->getEvents());
        $this->assertNull($event->getArtist());
    }
}