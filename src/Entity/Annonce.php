<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $infoUne = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $infoDeux = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $infoTrois = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $annonceUser = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'annonceFavorites')]
    private Collection $usersFavorite;

    #[ORM\Column(length: 50)]
    private ?string $pet_name = null;

    #[ORM\Column(length: 100)]
    private ?string $localisation = null;

    public function __construct()
    {
        $this->usersFavorite = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInfoUne(): ?string
    {
        return $this->infoUne;
    }

    public function setInfoUne(string $infoUne): self
    {
        $this->infoUne = $infoUne;

        return $this;
    }

    public function getInfoDeux(): ?string
    {
        return $this->infoDeux;
    }

    public function setInfoDeux(?string $infoDeux): self
    {
        $this->infoDeux = $infoDeux;

        return $this;
    }

    public function getInfoTrois(): ?string
    {
        return $this->infoTrois;
    }

    public function setInfoTrois(?string $infoTrois): self
    {
        $this->infoTrois = $infoTrois;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getAnnonceUser(): ?User
    {
        return $this->annonceUser;
    }

    public function setAnnonceUser(?User $annonceUser): self
    {
        $this->annonceUser = $annonceUser;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersFavorite(): Collection
    {
        return $this->usersFavorite;
    }

    public function addUsersFavorite(User $usersFavorite): self
    {
        if (!$this->usersFavorite->contains($usersFavorite)) {
            $this->usersFavorite->add($usersFavorite);
            $usersFavorite->addAnnonceFavorite($this);
        }

        return $this;
    }

    public function removeUsersFavorite(User $usersFavorite): self
    {
        if ($this->usersFavorite->removeElement($usersFavorite)) {
            $usersFavorite->removeAnnonceFavorite($this);
        }

        return $this;
    }

    public function getPetName(): ?string
    {
        return $this->pet_name;
    }

    public function setPetName(string $pet_name): self
    {
        $this->pet_name = $pet_name;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }
}
