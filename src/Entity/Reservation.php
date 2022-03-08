<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
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
     * @ORM\Column(type="datetime")
     */
    private $rendezVous;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $estReservee;

    /**
     * @ORM\OneToOne(targetEntity=Commande::class, inversedBy="reservation", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $commande;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRendezVous(): ?\DateTimeInterface
    {
        return $this->rendezVous;
    }

    public function setRendezVous(\DateTimeInterface $rendezVous): self
    {
        $this->rendezVous = $rendezVous;

        return $this;
    }

    public function getEstReservee(): ?bool
    {
        return $this->estReservee;
    }

    public function setEstReservee(?bool $estReservee): self
    {
        $this->estReservee = $estReservee;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }
}
