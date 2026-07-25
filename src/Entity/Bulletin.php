<?php

namespace App\Entity;

use App\Repository\BulletinRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BulletinRepository::class)]
#[ORM\Table(name: 'bulletin')]
#[ORM\UniqueConstraint(name: 'unique_bulletin', columns: ['eleve_id', 'classe_id', 'periode', 'annee_academique_id'])]
#[ORM\HasLifecycleCallbacks]
class Bulletin
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
    private ?Classe $classe = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AnneeAcademique $anneeAcademique = null;

    #[ORM\Column]
    private int $periode = 1;

    #[ORM\Column(type: 'json')]
    private array $donneesJson = [];

    #[ORM\Column(nullable: true)]
    private ?float $moyenneGenerale = null;

    #[ORM\Column(nullable: true)]
    private ?int $rangClasse = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mention = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $generePar = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $genereAt = null;

    #[ORM\Column(length: 20)]
    private string $statut = 'brouillon';

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

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;

        return $this;
    }

    public function getAnneeAcademique(): ?AnneeAcademique
    {
        return $this->anneeAcademique;
    }

    public function setAnneeAcademique(?AnneeAcademique $anneeAcademique): static
    {
        $this->anneeAcademique = $anneeAcademique;

        return $this;
    }

    public function getPeriode(): int
    {
        return $this->periode;
    }

    public function setPeriode(int $periode): static
    {
        $this->periode = $periode;

        return $this;
    }

    public function getDonneesJson(): array
    {
        return $this->donneesJson;
    }

    public function setDonneesJson(array $donneesJson): static
    {
        $this->donneesJson = $donneesJson;

        return $this;
    }

    public function getMoyenneGenerale(): ?float
    {
        return $this->moyenneGenerale;
    }

    public function setMoyenneGenerale(?float $moyenneGenerale): static
    {
        $this->moyenneGenerale = $moyenneGenerale;

        return $this;
    }

    public function getRangClasse(): ?int
    {
        return $this->rangClasse;
    }

    public function setRangClasse(?int $rangClasse): static
    {
        $this->rangClasse = $rangClasse;

        return $this;
    }

    public function getMention(): ?string
    {
        return $this->mention;
    }

    public function setMention(?string $mention): static
    {
        $this->mention = $mention;

        return $this;
    }

    public function getGenerePar(): ?string
    {
        return $this->generePar;
    }

    public function setGenerePar(?string $generePar): static
    {
        $this->generePar = $generePar;

        return $this;
    }

    public function getGenereAt(): ?\DateTimeImmutable
    {
        return $this->genereAt;
    }

    public function setGenereAt(?\DateTimeImmutable $genereAt): static
    {
        $this->genereAt = $genereAt;

        return $this;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

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
