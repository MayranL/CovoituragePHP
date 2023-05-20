<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @ORM\Entity
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reservations")
     */
    private $passager;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Annonce", inversedBy="reservations")
     *
     */
    private $annonce;

    // Ajoutez ici les getters et les setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassager(): ?User
    {
        return $this->passager;
    }

    public function setPassager(?User $passager): self
    {
        $this->passager = $passager;

        return $this;
    }
    public function getOld()
    {
        $currentDate = new DateTime();
        $date = $this->annonce->getDate();
        return $date < $currentDate;
    }


    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonce $annonce): self
    {
        $this->annonce = $annonce;

        return $this;
    }
}
