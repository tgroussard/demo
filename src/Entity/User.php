<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $phonenumber;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled = true;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus", inversedBy="users")
     */
    private $campus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Outing", mappedBy="organizerUser")
     */
    private $outingsOrganized;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Outing", mappedBy="registeredUsers")
     */
    private $outingsRegistered;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->outingsOrganized = new ArrayCollection();
        $this->outingsRegistered = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPseudo()
    {
        return $this->pseudo;
    }

    public function setPseudo($pseudo): void
    {
        $this->pseudo = $pseudo;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getPhonenumber()
    {
        return $this->phonenumber;
    }

    public function setPhonenumber($phonenumber): void
    {
        $this->phonenumber = $phonenumber;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getCampus(): Campus
    {
        return $this->campus;
    }

    public function setCampus(Campus $campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return ArrayCollection
     */
    public function getOutingsOrganized(): ArrayCollection
    {
        return $this->outingsOrganized;
    }

    public function addOutingsOrganized(Outing $outing): void
    {
        if (!$this->outingsOrganized->contains($outing)) {
            $this->outingsOrganized[] = $outing;
            $outing->setOrganizerUser($this);
        }
    }

    public function removeOutingsOrganized(Outing $outing): void
    {
        if ($this->outingsOrganized->contains($outing)) {
            $this->outingsOrganized->removeElement($outing);

            if ($outing->getOrganizerUser() === $this) {
                $outing->setOrganizerUser(null);
            }
        }
    }

    /**
     * @return Collection
     */
    public function getOutingsRegistered(): Collection
    {
        return $this->outingsRegistered;
    }

    public function addOutingsRegistered(Outing $outing): void
    {
        if (!$this->outingsRegistered->contains($outing)) {
            $this->outingsRegistered[] = $outing;
            $outing->addRegisteredUsers($this);
        }
    }

    public function removeOutingsRegistered(Outing $outing): void
    {
        if ($this->outingsRegistered->contains($outing)) {
            $this->outingsRegistered->removeElement($outing);

            if ($outing->getRegisteredUsers()->contains($this)) {
                $outing->removeRegisteredUsers($this);
            }
        }
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
