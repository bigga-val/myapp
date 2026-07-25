<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ORM\Table(name: 'note')]
#[ORM\UniqueConstraint(name: 'unique_note_eleve_matiere_examen', columns: ['eleve_id', 'matiere_id', 'examen_id'])]
#[ORM\HasLifecycleCallbacks]
class Note
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
    private ?Matiere $matiere = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Examen $examen = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Classe $classe = null;

    #[ORM\Column]
    private float $valeur = 0.0;

    #[ORM\Column]
    private float $valeurMax = 20.0;

    #[ORM\Column]
    private int $periode = 1;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AnneeAcademique $anneeAcademique = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observationsProf = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $saisiePar = null;

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

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): static
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getExamen(): ?Examen
    {
        return $this->examen;
    }

    public function setExamen(?Examen $examen): static
    {
        $this->examen = $examen;

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

    public function getValeur(): float
    {
        return $this->valeur;
    }

    public function setValeur(float $valeur): static
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getValeurMax(): float
    {
        return $this->valeurMax;
    }

    public function setValeurMax(float $valeurMax): static
    {
        $this->valeurMax = $valeurMax;

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

    public function getAnneeAcademique(): ?AnneeAcademique
    {
        return $this->anneeAcademique;
    }

    public function setAnneeAcademique(?AnneeAcademique $anneeAcademique): static
    {
        $this->anneeAcademique = $anneeAcademique;

        return $this;
    }

    public function getObservationsProf(): ?string
    {
        return $this->observationsProf;
    }

    public function setObservationsProf(?string $observationsProf): static
    {
        $this->observationsProf = $observationsProf;

        return $this;
    }

    public function getSaisiePar(): ?string
    {
        return $this->saisiePar;
    }

    public function setSaisiePar(?string $saisiePar): static
    {
        $this->saisiePar = $saisiePar;

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
