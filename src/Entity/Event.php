<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:read','artist:read'])]
    private ?int $id = null;
    #[Groups(['event:read'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;
    #[Groups(['event:read'])]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;
    #[Groups(['event:read'])]
    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Artist $artist = null;
    #[Groups(['event:read'])]
    #[ORM\OneToMany(targetEntity: UserEvent::class, mappedBy: 'event', cascade: ['persist', 'remove'])]
    private Collection $userEvents;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;
    public function __construct()
    {
        $this->userEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * @return Collection<int, UserEvent>
     */
    public function getUserEvents(): Collection
    {
        return $this->userEvents;
    }

    public function addUserEvent(UserEvent $userEvent): static
    {
        if (!$this->userEvents->contains($userEvent)) {
            $this->userEvents->add($userEvent);
            $userEvent->setEvent($this);
        }

        return $this;
    }
    public function addUser(User $user): static
    {

        $userEvent = new UserEvent();
        $userEvent->setUserRef($user);
        $userEvent->setEvent($this);
        $this->addUserEvent($userEvent);

        return $this;
    }

    public function isRegister(User $user): bool
    {
        foreach ($this->userEvents as $userEvent) {
            if ($userEvent->getUserRef() === $user) {
                return true;
            }
        }
        return false;
    }

    public function removeUser(User $user): static
    {
        foreach ($this->userEvents as $userEvent) {
            if($userEvent->getUserRef() === $user) {
                $this->userEvents->removeElement($userEvent);
                $userEvent->setEvent(null);
                $userEvent->setUserRef(null);
            }
        }
        return $this;
    }

    public function removeUserEvent(UserEvent $userEvent): static
    {
        if ($this->userEvents->removeElement($userEvent)) {
            if ($userEvent->getEvent() === $this) {
                $userEvent->setEvent(null);
            }
        }

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
