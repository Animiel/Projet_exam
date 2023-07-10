<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]*$/',
        match: true,
        message: 'Ce champ n\'accepte pas les chiffres et caractères spéciaux.',
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]*$/',
        match: true,
        message: 'Ce champ n\'accepte pas les chiffres et caractères spéciaux.',
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 6)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z-]+@[a-zA-Z-]+\.[a-zA-Z]{2,6}$/',
        match: true,
        message: 'Veuillez entrer une adresse e-mail valide.',
    )]
    private ?string $email = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\Regex(
        pattern: '/^[0-9]*$/',
        match: true,
        message: 'Ce champ n\'accepte pas les lettres et caractères spéciaux.',
    )]
    private ?string $numero = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 2)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9]*$/',
        match: true,
        message: 'Ce champ n\'accepte pas les caractères spéciaux.',
    )]
    private ?string $sujet = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min: 2)]
    #[Assert\Regex(
        pattern: '/^\w+/',
        match: true,
        message: 'Veuillez entrer des mots valides.',
    )]
    private ?string $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
