<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message:"Le champs de l'email est obligatoire")]
    #[Assert\Email(message:"L'e-mail '{{ value }}'N'est pas valide.")]
    private ?string $email;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message:"Le champs mot de passe est obligatoire")]
    #[Assert\Length(min:"8",minMessage:"Votre mot de passe doit contenir au moins 8 caractères")]
    private ?string $password;

    #[ORM\Column(length: 15)]
    #[Assert\NotBlank(message:"Le champs nom est obligatoire")]
    #[Assert\Length(min:2,max:15, minMessage:"Le nom doit contenir au moins 2 caractères",maxMessage:"Le nom doit contenir au plus 15 caractères")]
    #[Assert\Regex(pattern:"/^[a-zA-Z]+$/",message:"Le nom ne doit contenir que des lettres")]
    private ?string $nom = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\NotBlank(message:"Le champs prenom est obligatoire")]
    #[Assert\Length(min:2,max:15, minMessage:"Le prenom doit comporter au moins 2 caractères",maxMessage:"Le prenom doit contenir au plus 15 caractères")]
    #[Assert\Regex(pattern:"/^[a-zA-Z]+$/",message:"Le prenom ne doit contenir que des lettres")]
    private ?string $prenom = null;

    
    #[ORM\Column(nullable: true)]
    private ?int $acces = null;
 
    #[ORM\Column(nullable: true)]
    private ?int $bloque = null;




    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\NotBlank(message:"Le champs type est obligatoire")]
    private ?string $type = null;


    #[ORM\Column(length: 15)]
    #[Assert\NotBlank(message:"Le champs adresse est obligatoire")]
    private ?string $adresse = null;

    #[Assert\NotBlank(message:"Le champs de confirmation du mot de passe est obligatoire")]
    #[Assert\Length(min:"8",minMessage:"Votre mot de passe doit contenir au moins 8 caractères")]
    public $confirm_password;

    
    #[ORM\Column(type:'string', nullable: true)]
    private $diplome;

    #[ORM\OneToMany(mappedBy: 'idPetOwner', targetEntity: RendezVous::class)]
    private Collection $rendezVouses;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message:"Le champs numero de telephone est obligatoire")]
    #[Assert\Length(min:"8",max:"8",minMessage:"Votre Numéro doit contenir 8 chiffres .")]
    #[Assert\Regex(pattern:"/^[0-9]*$/", message:"Doit contenir des chiffres")]
    private ?string $numtel = null;



    public function __construct()
    {
        $this->rendezVouses = new ArrayCollection();
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

   

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getAcces(): ?int
    {
        return $this->acces;
    }

    public function setAcces(?int $acces): self
    {
        $this->acces = $acces;

        return $this;
    }

    public function getBloque(): ?int
    {
        return $this->bloque;
    }

    public function setBloque(?int $bloque): self
    {
        $this->bloque = $bloque;

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVouses(): Collection
    {
        return $this->rendezVouses;
    }

    public function addRendezVouse(RendezVous $rendezVouse): self
    {
        if (!$this->rendezVouses->contains($rendezVouse)) {
            $this->rendezVouses->add($rendezVouse);
            $rendezVouse->setIdPetOwner($this);
        }

        return $this;
    }

    public function removeRendezVouse(RendezVous $rendezVouse): self
    {
        if ($this->rendezVouses->removeElement($rendezVouse)) {
            // set the owning side to null (unless already changed)
            if ($rendezVouse->getIdPetOwner() === $this) {
                $rendezVouse->setIdPetOwner(null);
            }
        }

        return $this;
    }

    public function getDiplome()
    {
        return $this->diplome;
    }

    public function setDiplome($diplome)
    {
        $this->diplome = $diplome;

        return $this;
    }

    public function __toString() {
        return $this->password;
    }

    public function getNumtel(): ?string
    {
        return $this->numtel;
    }

    public function setNumtel(string $numtel): self
    {
        $this->numtel = $numtel;

        return $this;
    }


}
