<?php

namespace App\Entity;


use App\Repository\EtudiantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use JMS\Serializer\Annotation\MaxDepth;
//use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
class Etudiant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['etudiant','prof','entreprise','filiere'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['etudiant','prof','entreprise','filiere'])]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['etudiant'])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(['etudiant'])]
    private ?string $nv_scolaire;

    #[ORM\ManyToOne(inversedBy: 'Stagiaires')]
    #[MaxDepth(1)]
    #[Groups(['etudiant'])]
    private ?Entreprise $entreprise = null;

    #[ORM\ManyToOne(inversedBy: 'etudiants_encadre')]
    #[MaxDepth(1)]
    #[Groups(['etudiant'])]
    private ?Prof $encadrant = null;

    #[ORM\ManyToOne(inversedBy: 'etudiants')]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(1)]
    #[Groups(['etudiant'])]
    private ?Filiere $filiere;

    
    //#[Vich\UploadableField(mapping: 'cv', fileNameProperty:'cvName')]
    //private ?File $cv;

    #[ORM\Column(length: 255)]
    #[Groups(['etudiant'])]
    private ?string $cv_name;

    public function __construct()
    {
        $this->setRoles(['ROLE_ETUDIANT']);
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

    public function getCvName(): ?string
    {
        return $this->cv_name;
    }

    public function setCvName(string $cvName): self
    {
        $this->cv_name = $cvName;

        return $this;
    }



    public function getNvScolaire(): ?string
    {
        return $this->nv_scolaire;
    }

    public function setNvScolaire(string $nv_scolaire): self
    {
        $this->nv_scolaire = $nv_scolaire;

        return $this;
    }


    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getEncadrant(): ?Prof
    {
        return $this->encadrant;
    }

    public function setEncadrant(?Prof $encadrant): self
    {
        $this->encadrant = $encadrant;

        return $this;
    }

    public function getFiliere(): ?Filiere
    {
        return $this->filiere;
    }

    public function setFiliere(?Filiere $filiere): self
    {
        $this->filiere = $filiere;

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
