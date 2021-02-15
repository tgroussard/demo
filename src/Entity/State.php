<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EtatRepository::class)
 */
class State
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
    private $label;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outing", mappedBy="state")
     */
    private $outings;

    /**
     * State constructor.
     */
    public function __construct()
    {
        $this->outings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
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
            $outing->setState($this);
        }
    }

    public function removeOutings(Outing $outing): void
    {
        if ($this->outings->contains($outing)) {
            $this->outings->removeElement($outing);

            if ($outing->getState() === $this) {
                $outing->setState(null);
            }
        }
    }
}
