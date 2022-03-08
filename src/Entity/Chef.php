<?php

namespace App\Entity;

use App\Repository\ChefRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ChefRepository::class)
 */
class Chef
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @Groups("chef:read")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("chef:read")
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Groups("chef:read")
     * @ORM\Column(type="string", length=255)
     */
    private $specialitee;


    /**
     * @Groups("chef:read")
     * @ORM\OneToMany(targetEntity=Plat::class, mappedBy="chef",  orphanRemoval=true)
     */
    private $plats;

    public function __construct()
    {
        $this->plats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSpecialitee(): ?string
    {
        return $this->specialitee;
    }

    public function setSpecialitee(string $specialitee): self
    {
        $this->specialitee = $specialitee;

        return $this;
    }

    /**
     * @return Collection|Plat[]
     */
    public function getPlats(): ?Collection
    {
        return $this->plats;
    }

    public function addPlat(Plat $plat): self
    {
        if (!$this->plats->contains($plat)) {
            $this->plats[] = $plat;
            $plat->setChef($this);
        }

        return $this;
    }

    public function removePlat(Plat $plat): self
    {
        if ($this->plats->removeElement($plat)) {
            // set the owning side to null (unless already changed)
            if ($plat->getChef() === $this) {
                $plat->setChef(null);
            }
        }

        return $this;
    }
public function __toString() {
    return (string) $this->getNom();
}
    // TODO: Implement toString() method.

}
