<?php

namespace App\Entity;

use App\Repository\UserEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserEventRepository::class)]
class UserEvent
{

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'userEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userRef = null;
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'userEvents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    public function getUserRef(): ?User
    {
        return $this->userRef;
    }

    public function setUserRef(?User $userRef): static
    {
        $this->userRef = $userRef;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }
}
