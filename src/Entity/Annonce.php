<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
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
}
