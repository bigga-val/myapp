<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CommandeNumber = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $CommandeDate = null;

    #[ORM\ManyToOne]
    private ?User $CommandePar = null;

    #[ORM\Column(nullable: true)]
    private ?bool $IsApproved = null;

    #[ORM\ManyToOne]
    private ?User $ApprovedBy = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandeNumber(): ?string
    {
        return $this->CommandeNumber;
    }

    public function setCommandeNumber(?string $CommandeNumber): static
    {
        $this->CommandeNumber = $CommandeNumber;

        return $this;
    }

    public function getCommandeDate(): ?\DateTimeInterface
    {
        return $this->CommandeDate;
    }

    public function setCommandeDate(\DateTimeInterface $CommandeDate): static
    {
        $this->CommandeDate = $CommandeDate;

        return $this;
    }

    public function getCommandePar(): ?User
    {
        return $this->CommandePar;
    }

    public function setCommandePar(?User $CommandePar): static
    {
        $this->CommandePar = $CommandePar;

        return $this;
    }

    public function isIsApproved(): ?bool
    {
        return $this->IsApproved;
    }

    public function setIsApproved(?bool $IsApproved): static
    {
        $this->IsApproved = $IsApproved;

        return $this;
    }

    public function getApprovedBy(): ?User
    {
        return $this->ApprovedBy;
    }

    public function setApprovedBy(?User $ApprovedBy): static
    {
        $this->ApprovedBy = $ApprovedBy;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
