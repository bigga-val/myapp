<?php

namespace App\Entity;

use App\Repository\PaiementFraisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementFraisRepository::class)]
#[ORM\Table(name: 'paiement_frais')]
#[ORM\HasLifecycleCallbacks]
class PaiementFrais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Eleve $eleve = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?FraisScolaire $fraisScolaire = null;

    #[ORM\Column]
    private ?float $montantPaye = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datePaiement = null;

    #[ORM\Column(length: 30)]
    private ?string $modePaiement = null;

    #[ORM\Column(length: 30, unique: true)]
    private ?string $numeroRecu = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observations = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $enregistrePar = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(?Eleve $eleve): static
    {
        $this->eleve = $eleve;

        return $this;
    }

    public function getFraisScolaire(): ?FraisScolaire
    {
        return $this->fraisScolaire;
    }

    public function setFraisScolaire(?FraisScolaire $fraisScolaire): static
    {
        $this->fraisScolaire = $fraisScolaire;

        return $this;
    }

    public function getMontantPaye(): ?float
    {
        return $this->montantPaye;
    }

    public function setMontantPaye(float $montantPaye): static
    {
        $this->montantPaye = $montantPaye;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTimeInterface $datePaiement): static
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(string $modePaiement): static
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    public function getNumeroRecu(): ?string
    {
        return $this->numeroRecu;
    }

    public function setNumeroRecu(string $numeroRecu): static
    {
        $this->numeroRecu = $numeroRecu;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): static
    {
        $this->observations = $observations;

        return $this;
    }

    public function getEnregistrePar(): ?string
    {
        return $this->enregistrePar;
    }

    public function setEnregistrePar(?string $enregistrePar): static
    {
        $this->enregistrePar = $enregistrePar;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
