<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CampusRepository::class)
 */
class Campus
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
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="campus")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outing", mappedBy="organizerSite")
     */
    private $outings;

    /**
     * Campus constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->outings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers(): ArrayCollection
    {
        return $this->users;
    }

    public function addUsers(User $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCampus($this);
        }
    }

    public function removeUsers(User $user): void
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);

            if ($user->getCampus() === $this) {
                $user->setCampus(null);
            }
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getOutings(): ArrayCollection
    {
        return $this->outings;
    }

    public function addOutings(Outing $outing): void
    {
        if (!$this->outings->contains($outing)) {
            $this->outings[] = $outing;
            $outing->setOrganizerSite($this);
        }
    }

    public function removeOutings(Outing $outing): void
    {
        if ($this->outings->contains($outing)) {
            $this->outings->removeElement($outing);

            if ($outing->getOrganizerSite() === $this) {
                $outing->setOrganizerSite(null);
            }
        }
    }
}
