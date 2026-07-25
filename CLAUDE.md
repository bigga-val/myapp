# CLAUDE.md — INSOFT MASOMO

Guidance pour Claude Code sur ce dépôt.

## Vue d'ensemble

**INSOFT MASOMO** est une application web de gestion scolaire privée construite avec **Symfony 6.1** et **Doctrine ORM** (PHP 8.1). Elle couvre la maternelle, le primaire, le secondaire et l'universitaire. L'application a été entièrement transformée depuis une ancienne app de gestion commerciale (Credol App) ; quelques anciens modules (Debit, Credit, Taux, Employe/Paie) coexistent encore avec les nouveaux modules scolaires.

**Base de données :** `db_masomo` (MySQL/MariaDB). Connexion dans `.env` (`DATABASE_URL`).

---

## Commandes fréquentes

### PHP / Symfony
```bash
composer install
php bin/console server:run
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:migrations:diff
php bin/console make:migration
php bin/console doctrine:fixtures:load --no-interaction
php bin/console cache:clear
php bin/console make:entity
php bin/console make:controller
php bin/console make:form
php bin/console debug:router
php bin/console doctrine:schema:validate   # vérifier que le schéma est en sync
```

### Frontend (Tailwind CSS)
```bash
npm install
npm run dev     # watch + recompile
npm run build   # production (minifié)
```

### Testing
```bash
php bin/phpunit
php bin/phpunit tests/path/to/TestFile.php
php bin/phpunit --filter testMethodName
```

---

## Architecture

### Cycle de vie d'une requête
```
HTTP Request → public/index.php → Symfony Kernel → Router →
  Controller → (Repository / Form) →
  EntityManager::flush() → Twig render / JsonResponse / RedirectResponse
```

### Structure `src/`
- **Controller/** — un par domaine, attributs `#[Route]`. Renvoie Twig, JSON ou redirect.
- **Entity/** — entités Doctrine ORM. Toute modification nécessite une migration.
- **Repository/** — une classe par entité. Les requêtes custom vont ici, pas dans les contrôleurs.
- **Form/** — classes FormType liées aux entités.
- **Service/** — services métier (voir liste ci-dessous).
- **Enum/** — `Niveau` (backed enum PHP 8.1).
- **Security/** — `LoginFormAuthenticator` (login par formulaire).

### Services principaux
| Service | Rôle |
|---|---|
| `AnneeAcademiqueService` | `getCurrent(): ?AnneeAcademique` / `getCurrentOrFail()` |
| `BulletinService` | `genererBulletin()` — moyenne pondérée, rang, mention |
| `MatriculeService` | `generateEleveMatricule(int $seq)` → `ELV-2026-00001` / `generateNumeroRecu(int $seq)` → `REC-2026-00001` |
| `EmpruntRetardService` | `mettreAJourRetards()` — met les emprunts en retard à `en_retard` |

---

## Domaines métier (entités)

### Scolarité (cœur)
| Entité | Description |
|---|---|
| `AnneeAcademique` | Année scolaire avec flag `isCurrent`. Toujours récupérer via `AnneeAcademiqueService`. |
| `Classe` | Classe / section. Lié à `Niveau` (enum) + `AnneeAcademique`. |
| `Matiere` | Matière avec `coefficient` (float) et `Niveau`. |
| `Eleve` | Élève. Matricule auto-généré `ELV-AAAA-NNNNN`. Méthode `getNomComplet()`. |
| `Inscription` | Enrollment élève ↔ classe ↔ année. UniqueConstraint `(eleve_id, annee_academique_id)`. |

### Personnel
| Entité | Description |
|---|---|
| `Employe` | Enseignants (et anciens employés). Champs teacher : `specialite`, `diplome`, `telephone`, `photo`, `statut`, `niveauEnseignement`. **Ne pas renommer** — des FK `PaieEmploye` en dépendent. |

### Évaluation
| Entité | Description |
|---|---|
| `Examen` | Session d'examen : `type` (devoir/interro/examen_partiel/examen_final), `periode` (1/2/3). |
| `Note` | Note élève. UniqueConstraint `(eleve_id, matiere_id, examen_id)`. |
| `Bulletin` | Snapshot JSON calculé par `BulletinService`. UniqueConstraint `(eleve, classe, periode, anneeAcademique)`. Statuts : `brouillon` / `validé` / `distribué`. |

Mentions BulletinService : ≥18 Excellent · ≥16 Très Bien · ≥14 Bien · ≥12 Assez Bien · ≥10 Passable · <10 Échec.

### Présences & Emploi du temps
| Entité | Description |
|---|---|
| `Presence` | Une entrée par élève par jour. UniqueConstraint `(eleve_id, date)`. Statuts : `present` / `absent` / `retard` / `justifié`. |
| `EmploiDuTemps` | Créneau horaire avec détection de conflit enseignant (`hasConflitEnseignant()`). |

### Finances scolaires
| Entité | Description |
|---|---|
| `FraisScolaire` | Définition d'un frais (inscription/mensuel/examen/autre) par classe et année. |
| `PaiementFrais` | Paiement élève. `numeroRecu` unique auto-généré. Reçu PDF via dompdf. |

### Bibliothèque
| Entité | Description |
|---|---|
| `Livre` | Catalogue avec `nombreExemplaires`. |
| `EmpruntLivre` | Emprunt élève. `isEnRetard()` : dateRetourPrevue < today && statut != rendu. Statuts : `en_cours` / `rendu` / `en_retard`. |

### Anciens modules (hérités, ne pas supprimer)
`Credit`, `Debit`, `Taux`, `Employe`, `Paie`, `PaieEmploye`, `User` — toujours en service.

---

## Enum Niveau

```php
// src/Enum/Niveau.php — PHP 8.1 backed enum
enum Niveau: string {
    case MATERNELLE   = 'maternelle';
    case PRIMAIRE     = 'primaire';
    case SECONDAIRE   = 'secondaire';
    case UNIVERSITAIRE = 'universitaire';

    public function label(): string { ... }
    public function badgeClass(): string { ... }
}
```

Stocké comme `VARCHAR` via `#[ORM\Column(enumType: Niveau::class)]`. Pas de table DB dédiée.

---

## Authentification & Rôles

**Login :** `/login` (FormAuthenticator). Après login → `app_set_sessions` (setup sessions + taux).  
**Page d'accueil `/` :** redirige vers le dashboard si connecté, sinon vers login.

### Hiérarchie des rôles
```
ROLE_SECRETAIRE → ROLE_ENSEIGNANT → ROLE_DIRECTEUR → ROLE_ADMIN
```

| Rôle | Accès |
|---|---|
| `ROLE_USER` | Lecture seule (index, show, rapports) |
| `ROLE_SECRETAIRE` | Saisie (notes, présences, paiements, emprunts, emploi du temps) |
| `ROLE_DIRECTEUR` | Validation bulletins, gestion classes/matières |
| `ROLE_ADMIN` | Tout, y compris gestion utilisateurs |

**Mot de passe par défaut** pour nouveaux utilisateurs : `Credol@{année}` (ex: `Credol@2026`).

### Fixtures de test
- `admin` / `Admin@2026` → ROLE_ADMIN
- `secretaire` / `Credol@2026` → ROLE_SECRETAIRE

---

## Templates & Frontend

### Layout
- **`templates/components/layout/default.html.twig`** — layout principal (sidebar + header + contenu)
- **`templates/components/layout/auth.html.twig`** — layout login (sans sidebar)
- **`templates/components/common/sidebar.html.twig`** — menu de navigation par module
- **`templates/components/common/header.html.twig`** — barre supérieure + cloche notifications

### Bibliothèques JS actives
| Lib | Usage |
|---|---|
| Alpine.js | Réactivité UI (sidebar, header, thème) |
| Select2 | Dropdowns avec recherche — initialisé globalement sur `form select` |
| Tippy.js | Tooltips sur `[data-tip="..."]` et `[title="..."]` |
| SweetAlert2 | Confirmations de suppression (remplace `confirm()` natif) |
| Toastr | Flash messages (via `raiseToastr()`) |
| jQuery | Base pour Select2, DataTables, SweetAlert integration |
| DataTables | Tableaux paginés (certaines pages) |
| dompdf | Génération PDF (bulletins, reçus) |

### Conventions UI

**Tooltips :**
```html
<button data-tip="Modifier cet élève">Modifier</button>
```

**SweetAlert pour suppression :**
```html
<!-- Automatique : tout form avec un bouton .btn-danger est intercepté -->
<!-- Ou explicite : -->
<form method="post" data-confirm="Voulez-vous supprimer ce livre ?">...</form>
```

**Hints de formulaire :**
```html
<span class="field-hint">Texte d'aide affiché sous le champ</span>
```

**Flash messages :** auto-dismiss après 5 secondes avec barre de progression.

**Raccourcis clavier :** `Alt+N` → premier bouton Nouveau · `Alt+F` → premier champ de recherche.

### Tailwind
- Version 3.2, palette custom : `primary`, `secondary`, `success`, `danger`, `warning`, `info`, `dark`
- `tailwind.config.js` scanne `./templates/**/*.twig`
- **Ne pas utiliser** de classes Tailwind dans les templates PDF (dompdf ne charge pas les CSS externes) → inline CSS uniquement dans `*_pdf.html.twig`

---

## API interne

| Route | Contrôleur | Description |
|---|---|---|
| `GET /api/notifications` | `NotificationController` | JSON — emprunts en retard, frais en attente, présences manquantes |

---

## Génération PDF (dompdf)

Pattern standard dans un contrôleur :
```php
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
$html = $this->renderView('mon_template_pdf.html.twig', [...]);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait'); // ou 'A5'
$dompdf->render();
return new Response($dompdf->output(), 200, [
    'Content-Type'        => 'application/pdf',
    'Content-Disposition' => 'inline; filename="nom-du-fichier.pdf"',
]);
```

Templates PDF existants :
- `templates/bulletin/bulletin_pdf.html.twig` — bulletin de notes (A4 portrait)
- `templates/paiement_frais/recu_pdf.html.twig` — reçu de paiement (A5 portrait)

---

## Notifications (cloche header)

`NotificationController::index()` → `GET /api/notifications` retourne un tableau JSON :
```json
[
  { "id": "retard-livres",       "type": "danger",  "icon": "book",      "title": "...", "message": "...", "url": "...", "time": "..." },
  { "id": "frais-attente",       "type": "warning", "icon": "money",     "title": "...", "message": "...", "url": "...", "time": "..." },
  { "id": "presences-manquantes","type": "info",    "icon": "clipboard", "title": "...", "message": "...", "url": "...", "time": "..." }
]
```

Icônes supportées dans le header : `book`, `money`, `clipboard`.

---

## Pages d'erreur

Dans `templates/bundles/TwigBundle/Exception/` :
- `error403.html.twig`, `error404.html.twig`, `error500.html.twig`

Déclencher depuis un contrôleur : `throw $this->createAccessDeniedException()` (pas de redirection manuelle).

---

## Conventions à respecter

### PHP
- `#[IsGranted]` → `use Symfony\Component\Security\Http\Attribute\IsGranted`
- `getUserIdentifier()` et non `getUsername()`
- `\Exception` dans les blocs `catch` des contrôleurs (pas `Doctrine\DBAL\Driver\Exception`)
- Ne jamais importer `PHPUnit\*` en production
- Dates nullables : `$date = $raw ? new \DateTime($raw) : new \DateTime('today')` (le `??` ne fonctionne pas sur `new \DateTime()`)
- `findBy([], ['createdAt' => 'DESC'])` plutôt que `findAll()` quand un tri est souhaité
- Ne **pas** utiliser `renderForm()` (deprecated Symfony 6.4+) → utiliser `render()` avec `$form->createView()`

### Migrations
Sur une base vide, ne pas rejouer les anciennes migrations incrémentales :
```bash
php bin/console doctrine:schema:create
php bin/console doctrine:migrations:version --add --all
```

### Entités
- Le stock n'est jamais stocké en dur ; `Niveau` est un enum PHP 8.1 (pas une table DB)
- `Employe` ne doit **pas** être renommé en `Enseignant` (FK `PaieEmploye` en dépend)
- Les bulletins stockent un **snapshot JSON** calculé (`donneesJson`) — ne pas recalculer à la volée en production

### Twig
- `annee.id ~ ''` pour convertir un int en string (le filtre `|string` n'existe pas en Twig)
- Les templates PDF (`*_pdf.html.twig`) **n'étendent pas** le layout principal et n'utilisent que du CSS inline
