<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AnnonceRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $annonceUser = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'annonceFavorites')]
    private Collection $usersFavorite;

    #[ORM\ManyToOne(inversedBy: 'annoncesMotif')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Motif $motifAnnonce = null;

    #[ORM\Column]
    private array $images = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $publicationDate = null;

    #[ORM\Column(length: 50)]
    #[Assert\Length(min: 2)]
    private ?string $petName = null;

    #[ORM\Column(length: 10)]
    private ?string $petGenre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $petBefriends = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $petHealth = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $petCaractere = null;

    #[ORM\Column(length: 3)]
    private ?string $petAge = null;

    #[ORM\Column(length: 255)]
    private ?string $descImg = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\Column]
    private ?float $latitude = null;

    public function __construct()
    {
        $this->usersFavorite = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMotifAnnonce(): ?Motif
    {
        return $this->motifAnnonce;
    }

    public function setMotifAnnonce(?Motif $motifAnnonce): self
    {
        $this->motifAnnonce = $motifAnnonce;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getPetName(): ?string
    {
        return $this->petName;
    }

    public function setPetName(string $petName): self
    {
        $this->petName = $petName;

        return $this;
    }

    public function getPetGenre(): ?string
    {
        return $this->petGenre;
    }

    public function setPetGenre(string $petGenre): self
    {
        $this->petGenre = $petGenre;

        return $this;
    }

    public function getPetBefriends(): ?string
    {
        return $this->petBefriends;
    }

    public function setPetBefriends(string $petBefriends): self
    {
        $this->petBefriends = $petBefriends;

        return $this;
    }

    public function getPetHealth(): ?string
    {
        return $this->petHealth;
    }

    public function setPetHealth(?string $petHealth): self
    {
        $this->petHealth = $petHealth;

        return $this;
    }

    public function getPetCaractere(): ?string
    {
        return $this->petCaractere;
    }

    public function setPetCaractere(?string $petCaractere): self
    {
        $this->petCaractere = $petCaractere;

        return $this;
    }

    public function getPetAge(): ?string
    {
        return $this->petAge;
    }

    public function setPetAge(string $petAge): self
    {
        $this->petAge = $petAge;

        return $this;
    }

    public function getDescImg(): ?string
    {
        return $this->descImg;
    }

    public function setDescImg(string $descImg): self
    {
        $this->descImg = $descImg;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }
}
