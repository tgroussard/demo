<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlaceRepository::class)
 */
class Place
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
     * @ORM\Column(type="string", length=180)
     */
    private $street;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="places")
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outing", mappedBy="place")
     */
    private $outings;

    /**
     * Place constructor.
     */
    public function __construct()
    {
        $this->outings = new ArrayCollection();
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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city): void
    {
        $this->city = $city;
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
