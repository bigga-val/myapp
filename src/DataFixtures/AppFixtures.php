<?php

namespace App\DataFixtures;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Entity\Credit;
use App\Entity\Debit;
use App\Entity\Employe;
use App\Entity\Eleve;
use App\Entity\FraisScolaire;
use App\Entity\Inscription;
use App\Entity\TypeFrais;
use App\Entity\Matiere;
use App\Entity\Paie;
use App\Entity\PaieEmploye;
use App\Entity\PaiementFrais;
use App\Entity\User;
use App\Enum\Niveau;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // ── 1. USERS ────────────────────────────────────────────────
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@masomo.cd');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setAdressephysique('Direction générale');
        $admin->setPassword($this->hasher->hashPassword($admin, 'Admin@2026'));
        $manager->persist($admin);

        $secretaire = new User();
        $secretaire->setUsername('secretaire');
        $secretaire->setEmail('secretaire@masomo.cd');
        $secretaire->setRoles(['ROLE_SECRETAIRE']);
        $secretaire->setAdressephysique('Secrétariat');
        $secretaire->setPassword($this->hasher->hashPassword($secretaire, 'Credol@2026'));
        $manager->persist($secretaire);

        // ── 2. ANNÉE ACADÉMIQUE ──────────────────────────────────────
        $anneePrec = new AnneeAcademique();
        $anneePrec->setLibelle('2024-2025');
        $anneePrec->setDateDebut(new \DateTime('2024-09-01'));
        $anneePrec->setDateFin(new \DateTime('2025-06-30'));
        $anneePrec->setIsCurrent(false);
        $manager->persist($anneePrec);

        $annee = new AnneeAcademique();
        $annee->setLibelle('2025-2026');
        $annee->setDateDebut(new \DateTime('2025-09-01'));
        $annee->setDateFin(new \DateTime('2026-06-30'));
        $annee->setIsCurrent(true);
        $manager->persist($annee);

        $manager->flush();

        // ── 3. CLASSES ───────────────────────────────────────────────
        $classesData = [
            // Maternelle
            ['Petite Section',   Niveau::MATERNELLE,    'Salle 1'],
            ['Moyenne Section',  Niveau::MATERNELLE,    'Salle 2'],
            ['Grande Section',   Niveau::MATERNELLE,    'Salle 3'],
            // Primaire
            ['1ère Primaire',    Niveau::PRIMAIRE,      'Salle 4'],
            ['2ème Primaire',    Niveau::PRIMAIRE,      'Salle 5'],
            ['3ème Primaire',    Niveau::PRIMAIRE,      'Salle 6'],
            ['4ème Primaire',    Niveau::PRIMAIRE,      'Salle 7'],
            ['5ème Primaire',    Niveau::PRIMAIRE,      'Salle 8'],
            ['6ème Primaire',    Niveau::PRIMAIRE,      'Salle 9'],
            // Secondaire
            ['1ère Secondaire A', Niveau::SECONDAIRE,   'Salle 10'],
            ['1ère Secondaire B', Niveau::SECONDAIRE,   'Salle 11'],
            ['2ème Secondaire',  Niveau::SECONDAIRE,    'Salle 12'],
            ['3ème Secondaire',  Niveau::SECONDAIRE,    'Salle 13'],
            ['4ème Secondaire',  Niveau::SECONDAIRE,    'Salle 14'],
            ['5ème Secondaire',  Niveau::SECONDAIRE,    'Salle 15'],
            ['6ème Secondaire',  Niveau::SECONDAIRE,    'Salle 16'],
            // Universitaire
            ['Licence 1 Informatique', Niveau::UNIVERSITAIRE, 'Amphi A'],
            ['Licence 2 Informatique', Niveau::UNIVERSITAIRE, 'Amphi B'],
            ['Licence 1 Gestion',      Niveau::UNIVERSITAIRE, 'Amphi C'],
        ];

        $classes = [];
        foreach ($classesData as [$nom, $niveau, $salle]) {
            $c = new Classe();
            $c->setNom($nom);
            $c->setNiveau($niveau);
            $c->setAnneeAcademique($annee);
            $c->setSalle($salle);
            $c->setEffectifMax(40);
            $manager->persist($c);
            $classes[$nom] = $c;
        }

        $manager->flush();

        // ── 4. MATIÈRES ──────────────────────────────────────────────
        $matieresData = [
            // Primaire
            ['Français',      'FR-P',   2.0, Niveau::PRIMAIRE],
            ['Mathématiques', 'MATH-P', 2.0, Niveau::PRIMAIRE],
            ['Sciences',      'SCI-P',  1.5, Niveau::PRIMAIRE],
            ['Histoire-Géo',  'HG-P',   1.0, Niveau::PRIMAIRE],
            ['Éveil',         'EV-P',   1.0, Niveau::PRIMAIRE],
            // Secondaire
            ['Français',      'FR-S',   3.0, Niveau::SECONDAIRE],
            ['Mathématiques', 'MATH-S', 3.0, Niveau::SECONDAIRE],
            ['Physique',      'PHY-S',  2.0, Niveau::SECONDAIRE],
            ['Chimie',        'CHI-S',  2.0, Niveau::SECONDAIRE],
            ['Biologie',      'BIO-S',  2.0, Niveau::SECONDAIRE],
            ['Histoire-Géo',  'HG-S',   1.5, Niveau::SECONDAIRE],
            ['Anglais',       'ANG-S',  2.0, Niveau::SECONDAIRE],
            ['Informatique',  'INFO-S', 1.5, Niveau::SECONDAIRE],
            // Universitaire
            ['Algorithmique',       'ALGO-U',  3.0, Niveau::UNIVERSITAIRE],
            ['Base de données',     'BDD-U',   3.0, Niveau::UNIVERSITAIRE],
            ['Programmation Web',   'WEB-U',   2.5, Niveau::UNIVERSITAIRE],
            ['Systèmes d\'exploit', 'SYS-U',   2.0, Niveau::UNIVERSITAIRE],
            ['Comptabilité',        'COMPTA-U', 3.0, Niveau::UNIVERSITAIRE],
            ['Gestion',             'GEST-U',  2.5, Niveau::UNIVERSITAIRE],
            // Maternelle
            ['Éveil',         'EV-M',   1.0, Niveau::MATERNELLE],
            ['Lecture',       'LEC-M',  1.0, Niveau::MATERNELLE],
        ];

        foreach ($matieresData as [$nom, $code, $coef, $niveau]) {
            $m = new Matiere();
            $m->setNom($nom);
            $m->setCode($code);
            $m->setCoefficient($coef);
            $m->setNiveau($niveau);
            $manager->persist($m);
        }

        $manager->flush();

        // ── 5. ENSEIGNANTS ───────────────────────────────────────────
        $enseignantsData = [
            ['Jean-Baptiste Mutombo',   'ENS-001', 'Mathématiques',    'Licence',  'actif',  Niveau::SECONDAIRE],
            ['Marie-Claire Kabila',     'ENS-002', 'Français',         'Master',   'actif',  Niveau::PRIMAIRE],
            ['Pascal Ilunga',           'ENS-003', 'Informatique',     'Master',   'actif',  Niveau::UNIVERSITAIRE],
            ['Chantal Mwamba',          'ENS-004', 'Biologie',         'Licence',  'actif',  Niveau::SECONDAIRE],
            ['Robert Kazadi',           'ENS-005', 'Histoire-Géo',     'Licence',  'actif',  Niveau::SECONDAIRE],
            ['Véronique Tshilomba',     'ENS-006', 'Anglais',          'Master',   'actif',  Niveau::SECONDAIRE],
            ['Alphonse Ngoy',           'ENS-007', 'Sciences',         'Licence',  'actif',  Niveau::PRIMAIRE],
            ['Jeanne Lukusa',           'ENS-008', 'Maternelle',       'ISPED',    'actif',  Niveau::MATERNELLE],
        ];

        foreach ($enseignantsData as [$nom, $matricule, $specialite, $diplome, $statut, $niveau]) {
            $e = new Employe();
            $e->setNomcomplet($nom);
            $e->setMatricule($matricule);
            $e->setDateembauche(new \DateTime('2020-09-01'));
            $e->setCategorie('Enseignant');
            $e->setTitre('Prof.');
            $e->setSalaireJournalier(35.0);
            $e->setSpecialite($specialite);
            $e->setDiplome($diplome);
            $e->setStatut($statut);
            $e->setNiveauEnseignement($niveau->value);
            $manager->persist($e);
        }

        $manager->flush();

        // ── 6. ÉLÈVES ────────────────────────────────────────────────
        $elevesData = [
            ['Kabongo',   'Junior',  'Mpiana',   'M', '2010-03-15', 'Lubumbashi', 'Jean Kabongo',    '+243812345678', 'père'],
            ['Mwanza',    'Grace',   null,        'F', '2011-07-22', 'Kinshasa',   'Alice Mwanza',    '+243823456789', 'mère'],
            ['Tshilombo', 'David',   'Kalala',   'M', '2012-01-05', 'Kolwezi',    'Pierre Tshilombo','+243834567890', 'père'],
            ['Muteba',    'Rachel',  null,        'F', '2010-11-18', 'Lubumbashi', 'Paul Muteba',     '+243845678901', 'père'],
            ['Ilunga',    'Espoir',  'Mukendi',  'M', '2011-05-30', 'Likasi',     'Marie Ilunga',    '+243856789012', 'mère'],
            ['Ngoy',      'Joëlle',  null,        'F', '2013-08-14', 'Lubumbashi', 'Victor Ngoy',     '+243867890123', 'père'],
            ['Kazadi',    'Samuel',  'Kabeya',   'M', '2012-04-25', 'Kipushi',    'Rose Kazadi',     '+243878901234', 'mère'],
            ['Lukusa',    'Ange',    null,        'F', '2009-12-03', 'Lubumbashi', 'Émile Lukusa',    '+243889012345', 'tuteur légal'],
            ['Mwamba',    'Patrick', 'Kalonji',  'M', '2008-06-19', 'Kolwezi',    'Cécile Mwamba',   '+243890123456', 'mère'],
            ['Kabila',    'Esther',  null,        'F', '2007-09-27', 'Lubumbashi', 'Marc Kabila',     '+243801234567', 'père'],
            ['Mutombo',   'Théo',    'Nkulu',    'M', '2006-02-11', 'Kinshasa',   'Hélène Mutombo',  '+243812345679', 'mère'],
            ['Kalonji',   'Diane',   null,        'F', '2005-07-08', 'Lubumbashi', 'Antoine Kalonji', '+243823456780', 'père'],
            ['Mpiana',    'Joël',    'Tshimba',  'M', '2004-03-22', 'Likasi',     'Brigitte Mpiana', '+243834567891', 'mère'],
            ['Nkulu',     'Laure',   null,        'F', '2003-10-16', 'Lubumbashi', 'Serge Nkulu',     '+243845678902', 'père'],
            ['Mukendi',   'Christian','Banza',   'M', '2005-01-29', 'Kolwezi',    'Agnès Mukendi',   '+243856789013', 'mère'],
        ];

        $eleves = [];
        foreach ($elevesData as $i => [$nom, $prenom, $postnom, $sexe, $dob, $lieu, $tuteur, $tel, $relation]) {
            $e = new Eleve();
            $e->setNom($nom);
            $e->setPrenom($prenom);
            $e->setPostnom($postnom);
            $e->setSexe($sexe);
            $e->setDateNaissance(new \DateTime($dob));
            $e->setLieuNaissance($lieu);
            $e->setNomTuteur($tuteur);
            $e->setTelephoneTuteur($tel);
            $e->setRelationTuteur($relation);
            $manager->persist($e);
            $eleves[] = $e;
        }

        $manager->flush();

        // Set matricules after flush (need IDs)
        foreach ($eleves as $eleve) {
            $eleve->setMatricule(sprintf('ELV-%s-%05d', date('Y'), $eleve->getId()));
        }
        $manager->flush();

        // ── 7. INSCRIPTIONS ──────────────────────────────────────────
        $inscriptionsData = [
            // Petite Section (maternelle)
            [$eleves[0],  'Petite Section'],
            [$eleves[1],  'Petite Section'],
            // 4ème Primaire
            [$eleves[2],  '4ème Primaire'],
            [$eleves[3],  '4ème Primaire'],
            [$eleves[4],  '4ème Primaire'],
            // 1ère Secondaire A
            [$eleves[5],  '1ère Secondaire A'],
            [$eleves[6],  '1ère Secondaire A'],
            [$eleves[7],  '1ère Secondaire A'],
            // 4ème Secondaire
            [$eleves[8],  '4ème Secondaire'],
            [$eleves[9],  '4ème Secondaire'],
            // 6ème Secondaire
            [$eleves[10], '6ème Secondaire'],
            [$eleves[11], '6ème Secondaire'],
            // Licence 1 Informatique
            [$eleves[12], 'Licence 1 Informatique'],
            [$eleves[13], 'Licence 1 Informatique'],
            [$eleves[14], 'Licence 1 Informatique'],
        ];

        $enseignants = [];
        foreach ($inscriptionsData as [$eleve, $classeNom]) {
            $inscription = new Inscription();
            $inscription->setEleve($eleve);
            $inscription->setClasse($classes[$classeNom]);
            $inscription->setAnneeAcademique($annee);
            $inscription->setDateInscription(new \DateTime('2025-09-05'));
            $inscription->setStatut('actif');
            $manager->persist($inscription);
        }

        $manager->flush();

        // Récupérer les enseignants persistés
        $enseignants = $manager->getRepository(Employe::class)->findAll();

        // ── 8. TYPES DE FRAIS ─────────────────────────────────────────
        $typesData = [
            ['Inscription', 'inscription', 1],
            ['Mensuel',     'mensuel',     2],
            ['Examen',      'examen',      3],
            ['Autre',       'autre',       4],
        ];
        $typesFrais = [];
        foreach ($typesData as [$libelle, $code, $ordre]) {
            $t = new TypeFrais();
            $t->setLibelle($libelle)->setCode($code)->setOrdre($ordre)->setActif(true);
            $manager->persist($t);
            $typesFrais[$code] = $t;
        }
        $manager->flush();

        // ── 9. FRAIS SCOLAIRES ────────────────────────────────────────
        $fraisInscription = new FraisScolaire();
        $fraisInscription->setLibelle('Frais d\'inscription 2025-2026');
        $fraisInscription->setMontant(50000);
        $fraisInscription->setType($typesFrais['inscription']);
        $fraisInscription->setAnneeAcademique($annee);
        $fraisInscription->setActif(true);
        $manager->persist($fraisInscription);

        $fraisExamen = new FraisScolaire();
        $fraisExamen->setLibelle('Frais d\'examen annuel');
        $fraisExamen->setMontant(20000);
        $fraisExamen->setType($typesFrais['examen']);
        $fraisExamen->setAnneeAcademique($annee);
        $fraisExamen->setActif(true);
        $manager->persist($fraisExamen);

        $fraisParClasse = [
            'Petite Section'        => [30000, 'Mensualité — Maternelle'],
            '4ème Primaire'         => [25000, 'Mensualité — Primaire'],
            '1ère Secondaire A'     => [35000, 'Mensualité — Secondaire'],
            '4ème Secondaire'       => [35000, 'Mensualité — Secondaire'],
            '6ème Secondaire'       => [40000, 'Mensualité — Terminale'],
            'Licence 1 Informatique'=> [60000, 'Mensualité — Université'],
        ];

        $fraisMensuels = [];
        foreach ($fraisParClasse as $classeNom => [$montant, $libelle]) {
            $f = new FraisScolaire();
            $f->setLibelle($libelle . ' (' . $classeNom . ')');
            $f->setMontant($montant);
            $f->setType($typesFrais['mensuel']);
            $f->addClasse($classes[$classeNom]);
            $f->setAnneeAcademique($annee);
            $f->setActif(true);
            $manager->persist($f);
            $fraisMensuels[$classeNom] = $f;
        }

        $manager->flush();

        // ── 9. PAIEMENTS DES FRAIS ───────────────────────────────────
        // Mapping élève → frais mensuel de sa classe
        $elevesFrais = [
            [$eleves[0],  $fraisMensuels['Petite Section'],         30000],
            [$eleves[1],  $fraisMensuels['Petite Section'],         30000],
            [$eleves[2],  $fraisMensuels['4ème Primaire'],          25000],
            [$eleves[3],  $fraisMensuels['4ème Primaire'],          25000],
            [$eleves[4],  $fraisMensuels['4ème Primaire'],          25000],
            [$eleves[5],  $fraisMensuels['1ère Secondaire A'],      35000],
            [$eleves[6],  $fraisMensuels['1ère Secondaire A'],      35000],
            [$eleves[7],  $fraisMensuels['1ère Secondaire A'],      35000],
            [$eleves[8],  $fraisMensuels['4ème Secondaire'],        35000],
            [$eleves[9],  $fraisMensuels['4ème Secondaire'],        35000],
            [$eleves[10], $fraisMensuels['6ème Secondaire'],        40000],
            [$eleves[11], $fraisMensuels['6ème Secondaire'],        40000],
            [$eleves[12], $fraisMensuels['Licence 1 Informatique'], 60000],
            [$eleves[13], $fraisMensuels['Licence 1 Informatique'], 60000],
            [$eleves[14], $fraisMensuels['Licence 1 Informatique'], 60000],
        ];

        $modes  = ['especes', 'mobile_money', 'especes', 'virement', 'especes', 'cheque', 'mobile_money', 'especes'];
        $recuSeq = 1;
        $makeRecu = static function () use (&$recuSeq): string {
            return sprintf('REC-2026-%05d', $recuSeq++);
        };

        $makePaiement = static function (
            ObjectManager $mgr,
            Eleve $eleve,
            FraisScolaire $frais,
            float $montant,
            string $date,
            string $mode,
            string $recu
        ): void {
            $p = new PaiementFrais();
            $p->setEleve($eleve);
            $p->setFraisScolaire($frais);
            $p->setMontantPaye($montant);
            $p->setDatePaiement(new \DateTime($date));
            $p->setModePaiement($mode);
            $p->setNumeroRecu($recu);
            $p->setEnregistrePar('secretaire');
            $mgr->persist($p);
        };

        // Frais d'inscription (septembre 2025 — tous les élèves)
        foreach ($eleves as $i => $eleve) {
            $day = 5 + ($i % 10);
            $makePaiement($manager, $eleve, $fraisInscription, 50000,
                sprintf('2025-09-%02d', $day), $modes[$i % count($modes)], $makeRecu());
        }

        // Mensualités 2026 — avec décroissance réaliste du taux de paiement
        // Jan=15, Feb=14, Mar=14, Apr=13, Mai=12, Jun=10, Jul=8
        $payingCountByMonth = [1 => 15, 2 => 14, 3 => 14, 4 => 13, 5 => 12, 6 => 10, 7 => 8];
        $daysByStudent = [3,5,7,8,10,12,14,15,17,18,20,22,23,25,26];

        foreach ($payingCountByMonth as $mois => $nbPayants) {
            foreach (array_slice($elevesFrais, 0, $nbPayants) as $i => [$eleve, $frais, $montant]) {
                $day = $daysByStudent[$i % count($daysByStudent)];
                $makePaiement($manager, $eleve, $frais, $montant,
                    sprintf('2026-%02d-%02d', $mois, $day),
                    $modes[$i % count($modes)], $makeRecu());
            }
        }

        // Frais d'examen (mars 2026 — tous les élèves sauf 2 en retard)
        foreach (array_slice($eleves, 0, 13) as $i => $eleve) {
            $day = 3 + ($i * 2);
            $makePaiement($manager, $eleve, $fraisExamen, 20000,
                sprintf('2026-03-%02d', min($day, 28)),
                $modes[$i % count($modes)], $makeRecu());
        }

        $manager->flush();

        // ── 10. CRÉDITS (trésorerie entrante 2026) ───────────────────
        $creditsData = [
            ['2026-01-10', 500000, 'Subvention ministère de l\'Éducation nationale'],
            ['2026-01-25', 150000, 'Don de l\'association des parents d\'élèves'],
            ['2026-02-08', 200000, 'Subvention ONG Éducation Pour Tous'],
            ['2026-02-20', 75000,  'Location salle pour événement'],
            ['2026-03-05', 320000, 'Subvention trimestrielle province'],
            ['2026-03-22', 80000,  'Recettes vente livres usagés'],
            ['2026-04-10', 150000, 'Don mécène local'],
            ['2026-04-28', 60000,  'Vente photos scolaires'],
            ['2026-05-03', 420000, 'Subvention programme UNICEF'],
            ['2026-05-19', 120000, 'Recettes fête de l\'école'],
            ['2026-06-12', 280000, 'Don comité des parents d\'élèves'],
            ['2026-06-25', 90000,  'Subvention mairie'],
            ['2026-07-04', 180000, 'Aide bailleur diaspora'],
            ['2026-07-15', 55000,  'Cession matériel ancien'],
        ];

        foreach ($creditsData as [$date, $montant, $raison]) {
            $c = new Credit();
            $c->setMontant($montant);
            $c->setRaison($raison);
            $c->setDateCredit(new \DateTime($date));
            $c->setDevise('FC');
            $c->setTaux(1.0);
            $c->setCreatedBy('admin');
            $c->setCreatedAt(new \DateTimeImmutable($date));
            $manager->persist($c);
        }

        // ── 11. DÉBITS (trésorerie sortante 2026) ────────────────────
        $debitsData = [
            ['2026-01-05', 180000, 'Loyer janvier'],
            ['2026-01-10', 85000,  'Facture électricité'],
            ['2026-01-18', 45000,  'Fournitures bureau'],
            ['2026-01-28', 30000,  'Internet et téléphonie'],
            ['2026-02-05', 180000, 'Loyer février'],
            ['2026-02-12', 60000,  'Eau et assainissement'],
            ['2026-02-20', 55000,  'Entretien mobilier scolaire'],
            ['2026-03-05', 180000, 'Loyer mars'],
            ['2026-03-10', 95000,  'Facture électricité'],
            ['2026-03-15', 120000, 'Achat matériel pédagogique'],
            ['2026-03-22', 40000,  'Frais organisation examens'],
            ['2026-04-05', 180000, 'Loyer avril'],
            ['2026-04-14', 70000,  'Entretien des locaux'],
            ['2026-04-25', 35000,  'Fournitures de nettoyage'],
            ['2026-05-05', 180000, 'Loyer mai'],
            ['2026-05-12', 88000,  'Facture électricité'],
            ['2026-05-20', 65000,  'Réparation toiture'],
            ['2026-06-05', 180000, 'Loyer juin'],
            ['2026-06-10', 75000,  'Facture eau et électricité'],
            ['2026-06-22', 50000,  'Achat trophées remise des prix'],
            ['2026-07-05', 180000, 'Loyer juillet'],
            ['2026-07-10', 55000,  'Facture électricité'],
        ];

        foreach ($debitsData as [$date, $montant, $raison]) {
            $d = new Debit();
            $d->setMontant($montant);
            $d->setRaison($raison);
            $d->setDateDebit(new \DateTime($date));
            $d->setDevise('FC');
            $d->setTaux(1.0);
            $d->setCreatedBy('admin');
            $d->setCreatedAt(new \DateTimeImmutable($date));
            $manager->persist($d);
        }

        $manager->flush();

        // ── 12. PAIE & SALAIRES (jan–jul 2026) ───────────────────────
        $moisLabels = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet'];

        // Base mensuelle par enseignant (FC) + légère variation
        $salairesBases = [280000, 260000, 310000, 250000, 245000, 270000, 240000, 235000];

        for ($mois = 1; $mois <= 7; $mois++) {
            $paie = new Paie();
            $paie->setLabel($moisLabels[$mois] . ' 2026');
            $paie->setMonthPay($mois);
            $paie->setYearPay(2026);
            $manager->persist($paie);
            $manager->flush();

            foreach ($enseignants as $i => $enseignant) {
                $base      = $salairesBases[$i % count($salairesBases)];
                $primes    = ($mois === 3 || $mois === 6) ? 30000 : 15000; // prime trimestrielle
                $deductions = 8000;
                $total     = $base + $primes - $deductions;

                $pe = new PaieEmploye();
                $pe->setEmploye($enseignant);
                $pe->setPaie($paie);
                $pe->setNbJours(22);
                $pe->setSalaireBase($base);
                $pe->setPrimes($primes);
                $pe->setDeductions($deductions);
                $pe->setTotal($total);
                $pe->setCreatedAt(new \DateTimeImmutable(sprintf('2026-%02d-28', $mois)));
                $manager->persist($pe);
            }

            $manager->flush();
        }
    }
}
