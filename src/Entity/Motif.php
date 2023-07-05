<?php

namespace App\Entity;

use App\Repository\MotifRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MotifRepository::class)]
class Motif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'motifAnnonce', targetEntity: Annonce::class, orphanRemoval: true)]
    private Collection $annoncesMotif;

    public function __construct()
    {
        $this->annoncesMotif = new ArrayCollection();
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

    /**
     * @return Collection<int, Annonce>
     */
    public function getAnnoncesMotif(): Collection
    {
        return $this->annoncesMotif;
    }

    public function addAnnoncesMotif(Annonce $annoncesMotif): self
    {
        if (!$this->annoncesMotif->contains($annoncesMotif)) {
            $this->annoncesMotif->add($annoncesMotif);
            $annoncesMotif->setMotifAnnonce($this);
        }

        return $this;
    }

    public function removeAnnoncesMotif(Annonce $annoncesMotif): self
    {
        if ($this->annoncesMotif->removeElement($annoncesMotif)) {
            // set the owning side to null (unless already changed)
            if ($annoncesMotif->getMotifAnnonce() === $this) {
                $annoncesMotif->setMotifAnnonce(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->getName();
    }
}
