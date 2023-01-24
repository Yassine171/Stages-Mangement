<?php

namespace App\Entity;

use App\Repository\ProfRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ProfRepository::class)]
class Prof implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['etudiant','prof'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['etudiant','prof'])]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['prof'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::ARRAY)]
    #[Groups(['prof'])]
    private array $Modules = [];

    #[ORM\OneToMany(mappedBy: 'encadrant', targetEntity: Etudiant::class)]
    #[MaxDepth(1)]
    #[Groups(['prof'])]
    private Collection $etudiants_encadre;



    public function __construct()
    {
        $this->etudiants_encadre = new ArrayCollection();
        $this->setRoles(['ROLE_PROF']);
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

    public function getModules(): array
    {
        return $this->Modules;
    }

    public function setModules(array $Modules): self
    {
        $this->Modules = $Modules;

        return $this;
    }

    /**
     * @return Collection<int, Etudiant>
     */
    public function getEtudiantsEncadre(): Collection
    {
        return $this->etudiants_encadre;
    }

    public function addEtudiantsEncadre(Etudiant $etudiantsEncadre): self
    {
        if (!$this->etudiants_encadre->contains($etudiantsEncadre)) {
            $this->etudiants_encadre->add($etudiantsEncadre);
            $etudiantsEncadre->setEncadrant($this);
        }

        return $this;
    }

    public function removeEtudiantsEncadre(Etudiant $etudiantsEncadre): self
    {
        if ($this->etudiants_encadre->removeElement($etudiantsEncadre)) {
            // set the owning side to null (unless already changed)
            if ($etudiantsEncadre->getEncadrant() === $this) {
                $etudiantsEncadre->setEncadrant(null);
            }
        }

        return $this;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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
