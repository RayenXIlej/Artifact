<?php

namespace App\Entity;

use App\Repository\PetSitterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=PetSitterRepository::class)
 */
class PetSitter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"PetSitter"})
     */
    private $id;
    /**
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="veuillez saisir  ")
     * @Groups({"PetSitter"})

     */
    private $Nom;

    /**

     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ce champs est obligatoire")
     * @Groups({"PetSitter"})


     */

    private $Prenom;
    /**
     *  @ORM\Column(type="integer")
     * @Assert\NotBlank(message="ce champs est obligatoire")
     * @Groups({"PetSitter"})



     */
    private $NumTel;


    /**

     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ce champs est obligatoire")
     * @Groups({"PetSitter"})



     */

    private $Adresse;
    /**

     * @ORM\Column(type="string", length=255)
     * @Groups({"PetSitter"})


     */

    private $Photo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): self
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getNumTel(): ?int
    {
        return $this->NumTel;
    }

    public function setNumTel(int $NumTel): self
    {
        $this->NumTel = $NumTel;

        return $this;
    }
    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(string $Adresse): self
    {
        $this->Adresse = $Adresse;

        return $this;
    }
    public function getPhoto(): ?string
    {
        return $this->Photo;
    }

    public function setPhoto(string $Photo): self
    {
        $this->Photo = $Photo;

        return $this;
    }
   // public function __toString()
   // {
   //   return $this->Name;  
   // }

}