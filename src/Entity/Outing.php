<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Outing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endRegisterDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxRegister;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\State", inversedBy="outings")
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Place", inversedBy="outings")
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus", inversedBy="outings")
     */
    private $organizerSite;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="outingsOrganized")
     */
    private $organizerUser;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="outingsRegistered")
     */
    private $registeredUsers;

    /**
     * Outing constructor.
     */
    public function __construct()
    {
        $this->registeredUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getEndRegisterDate(): ?DateTimeInterface
    {
        return $this->endRegisterDate;
    }

    public function setEndRegisterDate(DateTimeInterface $endRegisterDate): self
    {
        $this->endRegisterDate = $endRegisterDate;

        return $this;
    }

    public function getMaxRegister(): ?int
    {
        return $this->maxRegister;
    }

    public function setMaxRegister(int $maxRegister): self
    {
        $this->maxRegister = $maxRegister;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state): void
    {
        $this->state = $state;
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function setPlace($place): void
    {
        $this->place = $place;
    }

    public function getOrganizerSite()
    {
        return $this->organizerSite;
    }

    public function setOrganizerSite($organizerSite): void
    {
        $this->organizerSite = $organizerSite;
    }

    public function getOrganizerUser()
    {
        return $this->organizerUser;
    }

    public function setOrganizerUser($organizerUser): void
    {
        $this->organizerUser = $organizerUser;
    }

    /**
     * @return Collection
     */
    public function getRegisteredUsers(): Collection
    {
        return $this->registeredUsers;
    }

    public function addRegisteredUsers($user): void
    {
        if (!$this->registeredUsers->contains($user)) {
            $this->registeredUsers[] = $user;
            $user->addOutingsRegistered($this);
        }
    }

    public function removeRegisteredUsers($user): void
    {
        if ($this->registeredUsers->contains($user)) {
            $this->registeredUsers->removeElement($user);

            if ($user->getOutingsRegistered()->contains($this)) {
                $user->removeOutingsRegistered($this);
            }
        }
    }
}
