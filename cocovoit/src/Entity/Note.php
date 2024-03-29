<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $auteur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $conducteur;

    /**
     * @ORM\Column(type="integer")
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Annonce")
     */
    private $annonce;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): self
    {
        $this->auteur = $auteur;

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
    public function getAnnonce()
    {
        return $this->annonce;
    }

    /**
     * @param mixed $annonce
     */
    public function setAnnonce($annonce): void
    {
        $this->annonce = $annonce;
    }

}
