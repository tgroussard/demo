<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 */
class City
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
     * @ORM\Column(type="string", length=5)
     */
    private $postalCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Place", mappedBy="city")
     */
    private $places;

    /**
     * City constructor.
     */
    public function __construct()
    {
        $this->places = new ArrayCollection();
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

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPlaces(): ArrayCollection
    {
        return $this->places;
    }

    public function addPlaces(Place $place): void
    {
        if (!$this->places->contains($place)) {
            $this->places[] = $place;
            $place->setCity($this);
        }
    }

    public function removePlaces(Place $place): void
    {
        if ($this->places->contains($place)) {
            $this->places->removeElement($place);

            if ($place->getCity() === $this) {
                $place->setCity(null);
            }
        }
    }


}
