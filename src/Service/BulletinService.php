<?php

namespace App\Service;

use App\Entity\AnneeAcademique;
use App\Entity\Bulletin;
use App\Entity\Classe;
use App\Entity\Eleve;
use App\Repository\BulletinRepository;
use App\Repository\InscriptionRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;

class BulletinService
{
    public function __construct(
        private NoteRepository        $noteRepo,
        private InscriptionRepository $inscriptionRepo,
        private BulletinRepository    $bulletinRepo,
        private EntityManagerInterface $em,
    ) {}

    public function genererBulletin(Eleve $eleve, Classe $classe, int $periode, AnneeAcademique $annee, string $generePar): Bulletin
    {
        // Find or create bulletin
        $bulletin = $this->bulletinRepo->findOneBy([
            'eleve'           => $eleve,
            'classe'          => $classe,
            'periode'         => $periode,
            'anneeAcademique' => $annee,
        ]) ?? new Bulletin();

        $notes = $this->noteRepo->findByEleveAndPeriode($eleve, $periode, $annee);

        $donnees = [];
        $totalPoints = 0.0;
        $totalCoefs  = 0.0;

        foreach ($notes as $note) {
            $matiere = $note->getMatiere();
            $coef    = $matiere->getCoefficient();
            $valeur  = $note->getValeur();
            $totalPoints += $valeur * $coef;
            $totalCoefs  += $coef;

            $donnees[] = [
                'matiere'      => $matiere->getNom(),
                'code'         => $matiere->getCode(),
                'coefficient'  => $coef,
                'note'         => $valeur,
                'noteMax'      => $note->getValeurMax(),
                'observations' => $note->getObservationsProf(),
            ];
        }

        $moyenne = $totalCoefs > 0 ? round($totalPoints / $totalCoefs, 2) : 0;
        $mention = $this->getMention($moyenne);

        $bulletin->setEleve($eleve);
        $bulletin->setClasse($classe);
        $bulletin->setAnneeAcademique($annee);
        $bulletin->setPeriode($periode);
        $bulletin->setDonneesJson($donnees);
        $bulletin->setMoyenneGenerale($moyenne);
        $bulletin->setMention($mention);
        $bulletin->setGenerePar($generePar);
        $bulletin->setGenereAt(new \DateTimeImmutable());
        $bulletin->setStatut('brouillon');

        $this->em->persist($bulletin);
        return $bulletin;
    }

    public function genererPourClasse(Classe $classe, int $periode, AnneeAcademique $annee, string $generePar): array
    {
        $inscriptions = $this->inscriptionRepo->findByClasse($classe);
        $bulletins    = [];

        foreach ($inscriptions as $inscription) {
            $bulletins[] = $this->genererBulletin($inscription->getEleve(), $classe, $periode, $annee, $generePar);
        }

        // Compute ranks
        usort($bulletins, fn ($a, $b) => $b->getMoyenneGenerale() <=> $a->getMoyenneGenerale());
        foreach ($bulletins as $rang => $b) {
            $b->setRangClasse($rang + 1);
        }

        $this->em->flush();
        return $bulletins;
    }

    private function getMention(float $moyenne): string
    {
        return match (true) {
            $moyenne >= 18 => 'Excellent',
            $moyenne >= 16 => 'Très Bien',
            $moyenne >= 14 => 'Bien',
            $moyenne >= 12 => 'Assez Bien',
            $moyenne >= 10 => 'Passable',
            default        => 'Échec',
        };
    }
}
