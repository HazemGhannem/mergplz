<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @Groups("categorie:read")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("categorie:read")
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Groups("categorie:read")
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @Groups("categorie:read")
     * @ORM\OneToMany(targetEntity=Plat::class, mappedBy="categorie")
     */
    private $plats;

    /**
     * @Groups("categorie:read")
     * @ORM\Column(type="string", length=255)
     */
    private $image;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Plat[]
     */
    public function getPlats(): Collection
    {
        return $this->plats;
    }

    public function addPlat(Plat $plat): self
    {
        if (!$this->plats->contains($plat)) {
            $this->plats[] = $plat;
            $plat->setCategorie($this);
        }

        return $this;
    }

    public function removePlat(Plat $plat): self
    {
        if ($this->plats->removeElement($plat)) {
            // set the owning side to null (unless already changed)
            if ($plat->getCategorie() === $this) {
                $plat->setCategorie(null);
            }
        }

        return $this;
    }
    public function __toString() {
        return (string) $this->getNom();
    }
        // TODO: Implement toString() method.

        public function getImage(): ?string
        {
            return $this->image;
        }

        public function setImage(string $image): self
        {
            $this->image = $image;

            return $this;
        }
}
