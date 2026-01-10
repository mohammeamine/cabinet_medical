<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 25)]
    private ?string $tele = null;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'medecin')]
    private Collection $consultationsAsMedecin;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'patient')]
    private Collection $consultationsAsPatient;

    /**
     * @var Collection<int, RendezVous>
     */
    #[ORM\OneToMany(targetEntity: RendezVous::class, mappedBy: 'medecin')]
    private Collection $rendezVousAsMedecin;

    /**
     * @var Collection<int, RendezVous>
     */
    #[ORM\OneToMany(targetEntity: RendezVous::class, mappedBy: 'patient')]
    private Collection $rendezVousAsPatient;

    public function __construct()
    {
        $this->consultationsAsMedecin = new ArrayCollection();
        $this->consultationsAsPatient = new ArrayCollection();
        $this->rendezVousAsMedecin = new ArrayCollection();
        $this->rendezVousAsPatient = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTele(): ?string
    {
        return $this->tele;
    }

    public function setTele(string $tele): static
    {
        $this->tele = $tele;

        return $this;
    }

    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultationsAsMedecin(): Collection
    {
        return $this->consultationsAsMedecin;
    }

    public function addConsultationsAsMedecin(Consultation $consultationsAsMedecin): static
    {
        if (!$this->consultationsAsMedecin->contains($consultationsAsMedecin)) {
            $this->consultationsAsMedecin->add($consultationsAsMedecin);
            $consultationsAsMedecin->setMedecin($this);
        }

        return $this;
    }

    public function removeConsultationsAsMedecin(Consultation $consultationsAsMedecin): static
    {
        if ($this->consultationsAsMedecin->removeElement($consultationsAsMedecin)) {
            // set the owning side to null (unless already changed)
            if ($consultationsAsMedecin->getMedecin() === $this) {
                $consultationsAsMedecin->setMedecin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultationsAsPatient(): Collection
    {
        return $this->consultationsAsPatient;
    }

    public function addConsultationsAsPatient(Consultation $consultationsAsPatient): static
    {
        if (!$this->consultationsAsPatient->contains($consultationsAsPatient)) {
            $this->consultationsAsPatient->add($consultationsAsPatient);
            $consultationsAsPatient->setPatient($this);
        }

        return $this;
    }

    public function removeConsultationsAsPatient(Consultation $consultationsAsPatient): static
    {
        if ($this->consultationsAsPatient->removeElement($consultationsAsPatient)) {
            // set the owning side to null (unless already changed)
            if ($consultationsAsPatient->getPatient() === $this) {
                $consultationsAsPatient->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVousAsMedecin(): Collection
    {
        return $this->rendezVousAsMedecin;
    }

    public function addRendezVousAsMedecin(RendezVous $rendezVousAsMedecin): static
    {
        if (!$this->rendezVousAsMedecin->contains($rendezVousAsMedecin)) {
            $this->rendezVousAsMedecin->add($rendezVousAsMedecin);
            $rendezVousAsMedecin->setMedecin($this);
        }

        return $this;
    }

    public function removeRendezVousAsMedecin(RendezVous $rendezVousAsMedecin): static
    {
        if ($this->rendezVousAsMedecin->removeElement($rendezVousAsMedecin)) {
            // set the owning side to null (unless already changed)
            if ($rendezVousAsMedecin->getMedecin() === $this) {
                $rendezVousAsMedecin->setMedecin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVousAsPatient(): Collection
    {
        return $this->rendezVousAsPatient;
    }

    public function addRendezVousAsPatient(RendezVous $rendezVousAsPatient): static
    {
        if (!$this->rendezVousAsPatient->contains($rendezVousAsPatient)) {
            $this->rendezVousAsPatient->add($rendezVousAsPatient);
            $rendezVousAsPatient->setPatient($this);
        }

        return $this;
    }

    public function removeRendezVousAsPatient(RendezVous $rendezVousAsPatient): static
    {
        if ($this->rendezVousAsPatient->removeElement($rendezVousAsPatient)) {
            // set the owning side to null (unless already changed)
            if ($rendezVousAsPatient->getPatient() === $this) {
                $rendezVousAsPatient->setPatient(null);
            }
        }

        return $this;
    }
}
