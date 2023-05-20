<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Annonce
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="annonces")
     */
    private $conducteur;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $villeDepart;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $villeArrive;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $prix;


    /**
     * @ORM\Column(type="string", length=100)
     */
    private $modeleV;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $nbplace;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reservation", mappedBy="annonce")
     */
    private $reservations;


    // plus les autres champs et leurs getters/setters...

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVilleDepart(): ?string
    {
        return $this->villeDepart;
    }

    public function setVilleDepart(string $villeDepart): self
    {
        $this->villeDepart = $villeDepart;

        return $this;
    }

    public function getConducteur(): ?User
    {
        return $this->conducteur;
    }

    public function setConducteur(?User $conducteur): self
    {
        $this->conducteur = $conducteur;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVilleArrive()
    {
        return $this->villeArrive;
    }

    /**
     * @param mixed $villeArrive
     */
    public function setVilleArrive($villeArrive): void
    {
        $this->villeArrive = $villeArrive;
    }

    /**
     * @return mixed
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * @param mixed $prix
     */
    public function setPrix($prix): void
    {
        $this->prix = $prix;
    }

    /**
     * @return mixed
     */
    public function getModeleV()
    {
        return $this->modeleV;
    }

    /**
     * @param mixed $modeleV
     */
    public function setModeleV($modeleV): void
    {
        $this->modeleV = $modeleV;
    }

    /**
     * @return mixed
     */
    public function getNbplace()
    {
        return $this->nbplace;
    }

    /**
     * @param mixed $nbplace
     */
    public function setNbplace($nbplace): void
    {
        $this->nbplace = $nbplace;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return ArrayCollection
     */
    public function getReservations(): ArrayCollection
    {
        return $this->reservations;
    }

    /**
     * @param ArrayCollection $reservations
     */
    public function setReservations(ArrayCollection $reservations): void
    {
        $this->reservations = $reservations;
    }

    public function getOld()
    {
        $currentDate = new DateTime();
        $date = $this->getDate();

        return $date < $currentDate;
    }

}
