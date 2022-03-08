<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM; 
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PlatRepository::class)
 */
class Plat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @Groups("plat:read")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("plat:read")
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Groups("plat:read")
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @Groups("plat:read")
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @Groups("plat:read")
     * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="plats")
     * @ORM\JoinColumn(nullable=true)
     */
    private $restaurant;

    /**
     * @Groups("plat:read")
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="plats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @Groups("plat:read")
     * @ORM\ManyToOne(targetEntity=Chef::class, inversedBy="plats")
     */
    private $chef;

    /**
     * @Groups("plat:read")
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="plat")
     */
    private $images;

    /**
     * @Groups("plat:read")
     * @ORM\OneToMany(targetEntity=CommandeItem::class, mappedBy="plat")
     */
    private $commandeItems;
    /**
     * @Groups("plat:read")
     * @ORM\Column(type="string", length=255)
     */
     private $img;

     public function getImg(): ?string
             {
                 return $this->img;
             }

             public function setImg(string $img): self
             {
                 $this->img = $img;

                 return $this;
             }

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->commandeItems = new ArrayCollection();
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

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getChef(): ?Chef
    {
        return $this->chef;
    }

    public function setChef(?Chef $chef): self
    {
        $this->chef = $chef;

        return $this;
    }

    /**
     * @return Collection|Images[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setPlat($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getPlat() === $this) {
                $image->setPlat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CommandeItem[]
     */
    public function getCommandeItems(): Collection
    {
        return $this->commandeItems;
    }

    public function addCommandeItem(CommandeItem $commandeItem): self
    {
        if (!$this->commandeItems->contains($commandeItem)) {
            $this->commandeItems[] = $commandeItem;
            $commandeItem->setPlat($this);
        }

        return $this;
    }

    public function removeCommandeItem(CommandeItem $commandeItem): self
    {
        if ($this->commandeItems->removeElement($commandeItem)) {
            // set the owning side to null (unless already changed)
            if ($commandeItem->getPlat() === $this) {
                $commandeItem->setPlat(null);
            }
        }

        return $this;
    }
    public function __toString() {
        return (string) $this->chef;
        return (string) $this->getNom();
    }
        // TODO: Implement toString() method.

}

