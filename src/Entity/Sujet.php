<?php

namespace App\Entity;

use App\Repository\SujetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SujetRepository::class)]
class Sujet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]*$/',
        match: true,
        message: 'Veuillez entrer des mots valides.',
    )]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\OneToMany(mappedBy: 'sujet', targetEntity: Message::class)]
    private Collection $messages;

    #[ORM\ManyToOne(inversedBy: 'sujets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'sujets')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?User $sujUser = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'sujetFavorites')]
    private Collection $usersFavorite;

    #[ORM\Column]
    private ?bool $closed = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->usersFavorite = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setSujet($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSujet() === $this) {
                $message->setSujet(null);
            }
        }

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

    public function getSujUser(): ?User
    {
        return $this->sujUser;
    }

    public function setSujUser(?User $sujUser): self
    {
        $this->sujUser = $sujUser;

        return $this;
    }

    public function __toString() {
        return $this->getName();
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
            $usersFavorite->addSujetFavorite($this);
        }

        return $this;
    }

    public function removeUsersFavorite(User $usersFavorite): self
    {
        if ($this->usersFavorite->removeElement($usersFavorite)) {
            $usersFavorite->removeSujetFavorite($this);
        }

        return $this;
    }

    public function isClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }
}
