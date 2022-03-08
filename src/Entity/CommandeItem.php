<?php

namespace App\Entity;

use App\Repository\CommandeItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeItemRepository::class)
 */
class CommandeItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantitee;

    /**
     * @ORM\ManyToOne(targetEntity=Plat::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $plat;

    /**
     * @ORM\ManyToOne(targetEntity=Commande::class, inversedBy="commandeItems")
     */
    private $commande;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantitee(): ?int
    {
        return $this->quantitee;
    }

    public function setQuantitee(int $quantitee): self
    {
        $this->quantitee = $quantitee;

        return $this;
    }

    public function getPlat(): ?Plat
    {
        return $this->plat;
    }

    public function setPlat(?Plat $plat): self
    {
        $this->plat = $plat;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }
}
