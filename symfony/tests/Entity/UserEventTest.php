<?php

namespace App\Tests\Entity;

use App\Entity\UserEvent;
use App\Entity\User;
use App\Entity\Event;
use PHPUnit\Framework\TestCase;


class UserEventTest extends TestCase
{
    private UserEvent $userEvent;
    private User $user;
    private Event $event;

    protected function setUp(): void
    {
        $this->userEvent = new UserEvent();
        $this->user = new User();
        $this->event = new Event();
    }

    public function testGetSetUserRef(): void
    {
        $this->userEvent->setUserRef($this->user);
        $this->assertSame($this->user, $this->userEvent->getUserRef());
    }

    public function testGetSetEvent(): void
    {
        $this->userEvent->setEvent($this->event);
        $this->assertSame($this->event, $this->userEvent->getEvent());
    }

    public function testGetSetId(): void
    {
        $id = 123;
        $this->userEvent->setId($id);
        $this->assertEquals($id, $this->userEvent->getId());
    }
}