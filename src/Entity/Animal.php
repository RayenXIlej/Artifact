<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Type = null;

    #[ORM\Column(length: 2)]
    private ?string $sexe = null;

    #[ORM\Column(length: 255)]
    private ?string $Disponibilite = null;

    private $created_at;

    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): self
    {
        $this->Type = $Type;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getDisponibilite(): ?string
    {
        return $this->Disponibilite;
    }

    public function setDisponibilite(string $Disponibilite): self
    {
        $this->Disponibilite = $Disponibilite;

        return $this;
    }
}
    
