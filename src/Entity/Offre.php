<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=OffreRepository::class)
 */
class Offre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")

     */
    private $id;
    /**
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="veuillez saisir  ")

     */
    private $Description;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThan("today", message ="La dade de début ne devrait pas être inférieure à la date du jour ! ")

     */
    public $DateDebut;

    /**
     * @ORM\Column(type="date")
     * @Assert\Expression(
     *     "this.getDateDebut() < this.getDateFin()",
     *     message="La date fin ne doit pas  etre inférieure a la date de début "
     * )

     */
    public $DateFin;
/**
* @ORM\Column(type="integer")

*/
    private $Prix;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->DateDebut;
    }

    public function setDateDebut(\DateTimeInterface $DateDebut): self
    {
        $this->DateDebut = $DateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->DateFin;
    }

    public function setDateFin(\DateTimeInterface $DateFin): self
    {
        $this->DateFin = $DateFin;

        return $this;
    }
    public function getPrix(): ?int
    {
        return $this->Prix;
    }
    public function setPrix(int $Prix): self
    {
        $this->Prix = $Prix;

        return $this;
    }

    public function __toString()
    {
        return(string)$this->getDateDebut();
    }

}