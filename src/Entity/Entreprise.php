<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation\Groups;



#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['entreprise'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['etudiant','entreprise'])]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['entreprise'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Etudiant::class)]
    #[MaxDepth(1)]
    #[Groups(['entreprise'])]
    private Collection $Stagiaires;

  

    public function __construct()
    {
        $this->Stagiaires = new ArrayCollection();
        $this->setRoles(['ROLE_ENTREPRISE']);
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

    /**
     * @return Collection<int, Etudiant>
     */
    public function getStagiaires(): Collection
    {
        return $this->Stagiaires;
    }

    public function addStagiaire(Etudiant $stagiaire): self
    {
        if (!$this->Stagiaires->contains($stagiaire)) {
            $this->Stagiaires->add($stagiaire);
            $stagiaire->setEntreprise($this);
        }

        return $this;
    }

    public function removeStagiaire(Etudiant $stagiaire): self
    {
        if ($this->Stagiaires->removeElement($stagiaire)) {
            // set the owning side to null (unless already changed)
            if ($stagiaire->getEntreprise() === $this) {
                $stagiaire->setEntreprise(null);
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
