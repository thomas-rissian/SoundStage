<?php

namespace App\Entity;

use App\Repository\UserEventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserEventRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_EVENT_ID', columns: ['user_ref_id', 'event_id'])]
class UserEvent
{
    #[ORM\ManyToOne(inversedBy: 'userEvents')]
    #[ORM\JoinColumn(name: 'user_ref_id',nullable: false)]
    #[Groups(['event:read'])]
    private ?User $userRef = null;
    #[ORM\ManyToOne(inversedBy: 'userEvents')]
    #[ORM\JoinColumn(name: 'event_id',nullable: false)]
    #[Groups(['event:read'])]
    private ?Event $event = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }
}
