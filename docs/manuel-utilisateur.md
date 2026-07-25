# Manuel Utilisateur — INSOFT MASOMO

> **Version :** 2.0  
> **Date :** Juillet 2026  
> **Application :** INSOFT MASOMO — Système de gestion scolaire  
> **Développé par :** InSoftware SARL

---

## Table des matières

1. [Introduction](#1-introduction)
2. [Connexion et accès](#2-connexion-et-accès)
3. [Tableau de bord](#3-tableau-de-bord)
4. [Années académiques](#4-années-académiques)
5. [Classes et niveaux](#5-classes-et-niveaux)
6. [Matières](#6-matières)
7. [Gestion des élèves](#7-gestion-des-élèves)
8. [Inscriptions](#8-inscriptions)
9. [Gestion des enseignants](#9-gestion-des-enseignants)
10. [Examens](#10-examens)
11. [Notes et saisie des notes](#11-notes-et-saisie-des-notes)
12. [Bulletins scolaires](#12-bulletins-scolaires)
13. [Présences](#13-présences)
14. [Emploi du temps](#14-emploi-du-temps)
15. [Frais scolaires](#15-frais-scolaires)
16. [Paiements des frais](#16-paiements-des-frais)
17. [Bibliothèque](#17-bibliothèque)
18. [Notifications](#18-notifications)
19. [Gestion des utilisateurs](#19-gestion-des-utilisateurs)
20. [Rôles et permissions](#20-rôles-et-permissions)
21. [Questions fréquentes](#21-questions-fréquentes)

---

## 1. Introduction

**INSOFT MASOMO** est un système de gestion scolaire complet destiné aux établissements privés couvrant les niveaux **maternelle, primaire, secondaire et universitaire**.

L'application permet de gérer :

- Les **élèves** et leurs inscriptions
- Les **enseignants** et leurs affectations
- Les **classes**, **matières** et **années académiques**
- Les **examens**, **notes** et **bulletins** (avec génération PDF)
- Les **présences** quotidiennes
- L'**emploi du temps** hebdomadaire
- Les **frais scolaires** et les **paiements** (avec reçu PDF)
- La **bibliothèque** (emprunts et retours de livres)
- Un système de **notifications** en temps réel (retards, impayés, absences)

---

## 2. Connexion et accès

### 2.1 Se connecter

1. Ouvrez l'application dans votre navigateur.
2. Sur la page de connexion, saisissez votre **nom d'utilisateur** et votre **mot de passe**.
3. Cliquez sur **Se connecter →**.

> Si vous n'avez pas de compte, contactez votre administrateur.

### 2.2 Première connexion

Le mot de passe par défaut attribué par l'administrateur est de la forme `Credol@{année}` (ex : `Credol@2026`). Changez-le dès votre première connexion depuis votre profil utilisateur.

### 2.3 Se déconnecter

Cliquez sur votre nom en haut à droite → **Se déconnecter**.

---

## 3. Tableau de bord

**Accessible à :** tous les utilisateurs connectés

La page d'accueil affiche une synthèse en temps réel de l'établissement.

### 3.1 Indicateurs clés (KPI)

| Indicateur | Description |
|---|---|
| **Élèves inscrits** | Nombre total d'élèves pour l'année courante |
| **Enseignants** | Nombre d'enseignants actifs |
| **Classes** | Nombre de classes de l'année courante |
| **Frais perçus ce mois** | Total des paiements de frais reçus dans le mois |
| **Paiements en attente** | Nombre de frais scolaires non encore réglés |
| **Livres empruntés** | Nombre d'emprunts en cours à la bibliothèque |
| **Masse salariale** | Total des salaires du mois courant |
| **Solde** | Crédits − Débits (trésorerie générale) |

### 3.2 Activité récente

En bas du tableau de bord, le **journal d'audit** affiche les 8 dernières actions effectuées dans le système (création, modification, suppression d'enregistrements).

### 3.3 Cloche de notifications

L'icône cloche en haut à droite affiche les alertes actives :
- **Rouge — Emprunts en retard :** livres non retournés après la date prévue.
- **Orange — Frais en attente :** frais scolaires définis mais non encore payés.
- **Bleu — Présences manquantes :** classes pour lesquelles les présences du jour n'ont pas encore été saisies.

Cliquez sur une alerte pour accéder directement à la section concernée.

---

## 4. Années académiques

**Accessible à :** ROLE_SECRETAIRE et supérieur

L'année académique est la base de toute l'organisation : classes, inscriptions, notes et frais y sont rattachés.

### 4.1 Voir les années académiques

Menu : **Configuration** → **Années académiques**

### 4.2 Créer une nouvelle année académique

1. Cliquez sur **Nouvelle année**.
2. Remplissez :

| Champ | Description |
|---|---|
| **Libellé** | Ex : "2025-2026" |
| **Date de début** | Début de l'année scolaire |
| **Date de fin** | Fin de l'année scolaire |
| **Année courante** | Cochez pour en faire l'année active |

3. Cliquez sur **Enregistrer**.

> Une seule année peut être marquée comme **courante** à la fois. C'est celle qui est utilisée par défaut dans tous les modules.

---

## 5. Classes et niveaux

**Accessible à :** ROLE_SECRETAIRE et supérieur

### 5.1 Niveaux disponibles

| Niveau | Description |
|---|---|
| **Maternelle** | Classes de la petite enfance |
| **Primaire** | Classes 1ère à 6ème |
| **Secondaire** | Classes de 7ème à terminale |
| **Universitaire** | Années de faculté / grande école |

### 5.2 Créer une classe

1. Menu : **Classes** → **Nouvelle classe**.
2. Remplissez :

| Champ | Description |
|---|---|
| **Nom** | Ex : "6ème A", "Terminale Sciences" |
| **Niveau** | Maternelle / Primaire / Secondaire / Universitaire |
| **Année académique** | Année à laquelle appartient la classe |

3. Cliquez sur **Enregistrer**.

### 5.3 Voir la liste des classes

Menu : **Classes** → **Liste des classes**. Les classes sont regroupées par niveau et filtrables par année académique.

---

## 6. Matières

**Accessible à :** ROLE_SECRETAIRE et supérieur

Les matières définissent les cours enseignés et leur poids dans le calcul des moyennes.

### 6.1 Créer une matière

1. Menu : **Matières** → **Nouvelle matière**.
2. Remplissez :

| Champ | Description |
|---|---|
| **Nom** | Ex : "Mathématiques", "Français" |
| **Coefficient** | Poids de la matière dans la moyenne (ex : 3) |
| **Niveau** | Niveau scolaire concerné |

3. Cliquez sur **Enregistrer**.

> Le coefficient est utilisé par le système pour calculer automatiquement la **moyenne pondérée** des bulletins.

---

## 7. Gestion des élèves

**Accessible à :** ROLE_SECRETAIRE et supérieur pour la création ; ROLE_USER pour la consultation

### 7.1 Voir la liste des élèves

Menu : **Élèves** → **Liste des élèves**

### 7.2 Ajouter un élève

1. Menu : **Élèves** → **Nouvel élève**.
2. Remplissez le formulaire :

| Champ | Description |
|---|---|
| **Nom** | Nom de famille |
| **Postnom** | Postnom (deuxième nom) |
| **Prénom** | Prénom(s) |
| **Sexe** | Masculin / Féminin |
| **Date de naissance** | Format JJ/MM/AAAA |
| **Lieu de naissance** | Ville ou localité |
| **Adresse** | Adresse de résidence |
| **Photo** | Photo de profil (optionnel) |
| **Nom du tuteur** | Parent ou tuteur légal |
| **Téléphone du tuteur** | Numéro de contact |
| **Email du tuteur** | Adresse e-mail (optionnel) |
| **Relation** | Père / Mère / Tuteur / Autre |

3. Cliquez sur **Enregistrer**.

> Le **matricule** est généré automatiquement au format `ELV-AAAA-NNNNN` (ex : `ELV-2026-00042`).

### 7.3 Voir le profil d'un élève

Cliquez sur **Voir** en face de l'élève pour accéder à sa fiche complète (informations personnelles, inscriptions, historique de présence).

### 7.4 Modifier les informations d'un élève

1. Ouvrez la fiche de l'élève.
2. Cliquez sur **Modifier**.
3. Effectuez les changements et cliquez sur **Enregistrer**.

---

## 8. Inscriptions

**Accessible à :** ROLE_SECRETAIRE et supérieur

L'inscription lie un élève à une classe pour une année académique donnée.

### 8.1 Inscrire un élève

1. Menu : **Inscriptions** → **Nouvelle inscription** (ou depuis la fiche de l'élève).
2. Remplissez :

| Champ | Description |
|---|---|
| **Élève** | Sélectionnez l'élève dans la liste |
| **Classe** | Classe de destination |
| **Année académique** | Année concernée |
| **Statut** | Actif / Transféré / Exclu / Diplômé |

3. Cliquez sur **Enregistrer**.

> Un élève ne peut être inscrit qu'une seule fois par année académique. Le système bloque les doublons.

### 8.2 Changer le statut d'une inscription

Pour marquer un élève comme transféré, exclu ou diplômé, modifiez son inscription et changez le **statut**.

---

## 9. Gestion des enseignants

**Accessible à :** ROLE_SECRETAIRE et supérieur pour la création ; ROLE_USER pour la consultation

### 9.1 Voir la liste des enseignants

Menu : **Enseignants** → **Liste des enseignants**

### 9.2 Ajouter un enseignant

1. Menu : **Enseignants** → **Nouvel enseignant**.
2. Remplissez :

| Champ | Description |
|---|---|
| **Nom / Prénom** | Identité de l'enseignant |
| **Spécialité** | Matière(s) enseignée(s) |
| **Diplôme** | Niveau académique |
| **Téléphone** | Numéro de contact |
| **Adresse** | Adresse de résidence |
| **Niveau d'enseignement** | Maternelle / Primaire / Secondaire / Universitaire |
| **Statut** | Actif / Inactif |
| **Photo** | Photo de profil (optionnel) |

3. Cliquez sur **Enregistrer**.

### 9.3 Payer le salaire d'un enseignant

Depuis la fiche de l'enseignant, cliquez sur **Payer le salaire**, sélectionnez la période et confirmez. Le système empêche les doublons de paiement pour une même période.

---

## 10. Examens

**Accessible à :** ROLE_SECRETAIRE et supérieur

Les examens servent de cadre de référence pour la saisie des notes.

### 10.1 Créer un examen

1. Menu : **Examens** → **Nouvel examen**.
2. Remplissez :

| Champ | Description |
|---|---|
| **Libellé** | Ex : "Examen partiel 1er trimestre" |
| **Type** | Devoir / Interrogation / Examen partiel / Examen final |
| **Période** | 1er, 2e ou 3e trimestre/semestre |
| **Date de début** | Début de la session |
| **Date de fin** | Fin de la session |
| **Année académique** | Année concernée |
| **Classe** | Laissez vide pour un examen transversal |

3. Cliquez sur **Enregistrer**.

---

## 11. Notes et saisie des notes

**Accessible à :** ROLE_SECRETAIRE et supérieur pour la saisie ; ROLE_USER pour la consultation

### 11.1 Saisir les notes par classe

La saisie en lot permet d'entrer les notes de tous les élèves d'une classe pour un examen donné en une seule opération.

1. Menu : **Notes** → **Saisir par classe**.
2. Sélectionnez la **classe**, la **matière** et l'**examen**.
3. Un tableau s'affiche avec une ligne par élève. Renseignez la note (sur 20 par défaut).
4. Cliquez sur **Enregistrer**.

> Les notes déjà saisies sont pré-remplies. Vous pouvez les corriger directement.

### 11.2 Voir la liste des notes

Menu : **Notes** → **Liste des notes**. Filtrable par classe, matière et examen.

---

## 12. Bulletins scolaires

**Accessible à :** ROLE_DIRECTEUR et supérieur pour la génération et validation ; ROLE_USER pour la consultation

### 12.1 Générer un bulletin

1. Menu : **Bulletins** → **Générer**.
2. Sélectionnez la **classe** et la **période**.
3. Cliquez sur **Générer pour toute la classe**.

Le système calcule automatiquement pour chaque élève :
- La **moyenne par matière** (note × coefficient)
- La **moyenne générale pondérée**
- Le **rang dans la classe**
- La **mention** selon le barème :

| Moyenne | Mention |
|---|---|
| ≥ 18 / 20 | Excellent |
| ≥ 16 / 20 | Très Bien |
| ≥ 14 / 20 | Bien |
| ≥ 12 / 20 | Assez Bien |
| ≥ 10 / 20 | Passable |
| < 10 / 20 | Échec |

### 12.2 Valider un bulletin

Un bulletin en statut **Brouillon** doit être validé avant distribution :

1. Ouvrez le bulletin depuis la liste.
2. Cliquez sur **Valider**.

> Un bulletin validé ne peut plus être modifié. Si une correction est nécessaire, régénérez le bulletin.

### 12.3 Distribuer et imprimer un bulletin en PDF

1. Depuis la liste des bulletins, cliquez sur **PDF** en face du bulletin souhaité.
2. Le bulletin s'ouvre dans votre navigateur au format PDF imprimable.
3. Utilisez la fonction d'impression de votre navigateur (`Ctrl+P`) pour imprimer.

---

## 13. Présences

**Accessible à :** ROLE_SECRETAIRE et supérieur pour la saisie ; ROLE_USER pour la consultation

### 13.1 Saisir les présences du jour

1. Menu : **Présences** → **Saisir les présences**.
2. Sélectionnez la **classe** et la **date** (par défaut : aujourd'hui).
3. Pour chaque élève, cochez le statut approprié :

| Statut | Description |
|---|---|
| **Présent** | L'élève est en cours |
| **Absent** | L'élève est absent |
| **Retard** | L'élève est arrivé en retard |
| **Justifié** | Absence justifiée (avec motif) |

4. Ajoutez un **motif** si nécessaire (visible pour absent, retard, justifié).
5. Cliquez sur **Enregistrer**.

> Si les présences ont déjà été saisies pour cette classe et cette date, elles sont pré-remplies et modifiables.

### 13.2 Consulter les présences

Menu : **Présences** → **Liste des présences**. Filtrez par classe et par date pour voir les enregistrements.

### 13.3 Rapport de présence d'un élève

Depuis la liste des présences, cliquez sur **Rapport** en face d'un élève pour voir son historique complet avec statistiques (nombre de présences, absences, retards, justifiés) sur une période donnée.

---

## 14. Emploi du temps

**Accessible à :** ROLE_SECRETAIRE et supérieur pour la gestion ; ROLE_USER pour la consultation

### 14.1 Voir l'emploi du temps d'une classe

1. Menu : **Emploi du temps** → **Vue semaine**.
2. Sélectionnez la classe.
3. La grille hebdomadaire s'affiche avec les créneaux colorés par matière.

### 14.2 Ajouter un créneau

1. Menu : **Emploi du temps** → **Ajouter un créneau**.
2. Remplissez :

| Champ | Description |
|---|---|
| **Classe** | Classe concernée |
| **Matière** | Matière enseignée |
| **Enseignant** | Enseignant affecté |
| **Jour** | Lundi à Samedi |
| **Heure de début** | Heure de début du cours |
| **Heure de fin** | Heure de fin du cours |
| **Salle** | Salle ou local (optionnel) |
| **Année académique** | Année concernée |

3. Cliquez sur **Enregistrer**.

> Le système détecte automatiquement les **conflits** : si l'enseignant est déjà programmé à ce créneau pour une autre classe, un message d'erreur s'affiche.

---

## 15. Frais scolaires

**Accessible à :** ROLE_SECRETAIRE et supérieur

Les frais scolaires définissent les montants à payer par les élèves. Ils sont ensuite utilisés pour enregistrer les paiements.

### 15.1 Créer un frais scolaire

1. Menu : **Frais scolaires** → **Nouveau frais**.
2. Remplissez :

| Champ | Description |
|---|---|
| **Libellé** | Ex : "Frais d'inscription 2026", "Minerval octobre" |
| **Montant** | Montant en FC |
| **Type** | Inscription / Mensuel / Examen / Autre |
| **Mois** | Si type = Mensuel, précisez le mois (1–12) |
| **Classe** | Laissez vide pour appliquer à toutes les classes |
| **Année académique** | Année concernée |
| **Actif** | Décochez pour désactiver sans supprimer |

3. Cliquez sur **Enregistrer**.

### 15.2 Voir la liste des frais

Menu : **Frais scolaires** → **Liste des frais**. Filtrable par année académique.

---

## 16. Paiements des frais

**Accessible à :** ROLE_SECRETAIRE et supérieur pour l'enregistrement ; ROLE_USER pour la consultation

### 16.1 Enregistrer un paiement

1. Menu : **Paiements** → **Nouveau paiement**.
2. Remplissez :

| Champ | Description |
|---|---|
| **Élève** | Sélectionnez l'élève |
| **Frais scolaire** | Type de frais payé |
| **Montant payé** | Montant effectivement versé (en FC) |
| **Date de paiement** | Date du paiement |
| **Mode de paiement** | Espèces / Mobile Money / Virement / Chèque |
| **Observations** | Remarque éventuelle (optionnel) |

3. Cliquez sur **Enregistrer**.

> Le **numéro de reçu** est généré automatiquement au format `REC-AAAA-NNNNN` (ex : `REC-2026-00015`).

### 16.2 Voir et imprimer un reçu PDF

1. Depuis la liste des paiements, cliquez sur **PDF** en face du paiement.
2. Le reçu s'affiche dans le navigateur — prêt à imprimer.

### 16.3 Filtrer les paiements

Depuis la liste des paiements, filtrez par :
- **Élève** : pour voir tous les paiements d'un élève
- **Mois** : pour voir les paiements d'un mois donné

---

## 17. Bibliothèque

**Accessible à :** ROLE_SECRETAIRE et supérieur pour la gestion ; ROLE_USER pour la consultation

### 17.1 Catalogue des livres

Menu : **Bibliothèque** → **Livres**

La liste affiche tous les livres du catalogue avec le nombre d'exemplaires disponibles.

### 17.2 Ajouter un livre au catalogue

1. Menu : **Bibliothèque** → **Livres** → **Ajouter un livre**.
2. Remplissez :

| Champ | Description |
|---|---|
| **Titre** | Titre du livre |
| **Auteur** | Nom de l'auteur |
| **ISBN** | Numéro ISBN (optionnel) |
| **Catégorie** | Ex : Roman, Sciences, Histoire |
| **Année de publication** | Année d'édition |
| **Nombre d'exemplaires** | Quantité disponible dans l'établissement |
| **Localisation** | Ex : "Rayon A, Étagère 3" |
| **Description** | Résumé ou description (optionnel) |

3. Cliquez sur **Enregistrer**.

### 17.3 Enregistrer un emprunt

1. Menu : **Bibliothèque** → **Emprunts** → **Nouvel emprunt**.
2. Remplissez :

| Champ | Description |
|---|---|
| **Livre** | Livre emprunté |
| **Emprunteur** | Élève qui emprunte |
| **Date d'emprunt** | Date du jour (ou date effective) |
| **Date de retour prévue** | Durée recommandée : 14 jours |
| **Observations** | Remarque éventuelle |

3. Cliquez sur **Enregistrer**.

> Le système vérifie qu'il reste des **exemplaires disponibles** avant d'autoriser l'emprunt. Si tous les exemplaires sont déjà empruntés, un message d'erreur s'affiche.

### 17.4 Enregistrer un retour

1. Menu : **Bibliothèque** → **Emprunts**.
2. Filtrez par statut **En cours**.
3. Cliquez sur **Marquer rendu** en face de l'emprunt concerné.

La date de retour effective est enregistrée automatiquement.

### 17.5 Emprunts en retard

Le système détecte automatiquement les emprunts dont la date de retour prévue est dépassée et affiche leur statut en **EN RETARD** (badge rouge). Une alerte apparaît également dans la cloche de notifications.

---

## 18. Notifications

Le système génère automatiquement des alertes visibles dans la **cloche** en haut à droite de l'écran.

| Type | Couleur | Déclenchement |
|---|---|---|
| Emprunts en retard | 🔴 Rouge | Un ou plusieurs livres non retournés après la date prévue |
| Frais en attente | 🟠 Orange | Frais scolaires actifs sans paiement correspondant |
| Présences manquantes | 🔵 Bleu | Classes sans saisie de présence pour la journée en cours |

Cliquez sur une notification pour accéder directement à la section concernée. Vous pouvez fermer une alerte individuellement avec le bouton **×**.

---

## 19. Gestion des utilisateurs

**Accessible à :** ROLE_ADMIN uniquement

### 19.1 Voir la liste des utilisateurs

Menu : **Utilisateurs** → **Liste des utilisateurs**

### 19.2 Créer un nouvel utilisateur

1. Menu : **Utilisateurs** → **Nouvel utilisateur**.
2. Remplissez :

| Champ | Description |
|---|---|
| **Nom d'utilisateur** | Identifiant de connexion |
| **Email** | Adresse email |
| **Rôle** | Niveau d'accès (voir section 20) |

3. Cliquez sur **Enregistrer**.

> Le mot de passe par défaut est affiché dans le message de confirmation (`Credol@{année}`). Communiquez-le à l'utilisateur et demandez-lui de le changer dès sa première connexion.

### 19.3 Modifier un utilisateur

Un administrateur peut modifier n'importe quel compte. Un utilisateur standard peut uniquement modifier **son propre profil**.

### 19.4 Réinitialiser un mot de passe

1. Dans la liste des utilisateurs, cliquez sur **Réinit. MDP**.
2. Saisissez et confirmez le nouveau mot de passe.
3. Cliquez sur **Enregistrer**.

---

## 20. Rôles et permissions

L'application dispose de 4 niveaux d'accès, en plus du rôle utilisateur de base.

### 20.1 Hiérarchie des rôles

```
ROLE_USER (base) < ROLE_SECRETAIRE < ROLE_ENSEIGNANT < ROLE_DIRECTEUR < ROLE_ADMIN
```

Chaque rôle hérite de tous les droits des rôles inférieurs.

### 20.2 Tableau des permissions

| Fonctionnalité | Utilisateur | Secrétaire | Directeur | Admin |
|---|:---:|:---:|:---:|:---:|
| Tableau de bord | ✅ | ✅ | ✅ | ✅ |
| Voir élèves / enseignants | ✅ | ✅ | ✅ | ✅ |
| Voir notes / présences | ✅ | ✅ | ✅ | ✅ |
| Voir emploi du temps | ✅ | ✅ | ✅ | ✅ |
| Voir paiements / reçus PDF | ✅ | ✅ | ✅ | ✅ |
| Voir emprunts bibliothèque | ✅ | ✅ | ✅ | ✅ |
| Saisir présences | ❌ | ✅ | ✅ | ✅ |
| Saisir notes | ❌ | ✅ | ✅ | ✅ |
| Créer élèves / inscriptions | ❌ | ✅ | ✅ | ✅ |
| Gérer frais & paiements | ❌ | ✅ | ✅ | ✅ |
| Gérer emprunts | ❌ | ✅ | ✅ | ✅ |
| Gérer emploi du temps | ❌ | ✅ | ✅ | ✅ |
| Valider bulletins | ❌ | ❌ | ✅ | ✅ |
| Gérer classes / matières | ❌ | ❌ | ✅ | ✅ |
| Gérer enseignants | ❌ | ❌ | ✅ | ✅ |
| Gestion des utilisateurs | ❌ | ❌ | ❌ | ✅ |
| Gestion des années académiques | ❌ | ❌ | ❌ | ✅ |
| Caisse (débit / crédit) | ❌ | ❌ | ❌ | ✅ |
| Journal d'audit | ❌ | ❌ | ❌ | ✅ |

### 20.3 Description des rôles

**Utilisateur (ROLE_USER)**
Accès en lecture seule. Peut consulter les données mais ne peut rien créer ni modifier. Peut modifier son propre profil.

**Secrétaire (ROLE_SECRETAIRE)**
Opérations courantes : saisie des présences et des notes, inscriptions, paiements, gestion de la bibliothèque et de l'emploi du temps.

**Directeur (ROLE_DIRECTEUR)**
Tout ce que fait le secrétaire, plus la validation des bulletins et la gestion pédagogique (classes, matières, enseignants).

**Administrateur (ROLE_ADMIN)**
Accès complet. Gère les utilisateurs, les paramètres système, la trésorerie et l'audit.

---

## 21. Questions fréquentes

**Q : Comment générer les bulletins de toute une classe en une seule fois ?**  
R : Menu **Bulletins** → **Générer**, sélectionnez la classe et la période, puis cliquez sur **Générer pour toute la classe**. Le système calcule toutes les moyennes et les rangs automatiquement.

**Q : Un élève est inscrit deux fois par erreur — que faire ?**  
R : Le système empêche les doublons d'inscription (un élève = une inscription par année). Si une inscription erronée existe, modifiez son statut à **Exclu** ou supprimez-la depuis la liste des inscriptions.

**Q : Je ne vois pas le bouton "Saisir les présences".**  
R : Ce bouton est réservé au rôle **Secrétaire** et supérieur. Contactez votre administrateur pour obtenir les droits nécessaires.

**Q : Comment corriger une note déjà saisie ?**  
R : Retournez dans **Notes** → **Saisir par classe**, sélectionnez la même classe, matière et examen. Les notes existantes sont pré-remplies et modifiables.

**Q : Le système refuse l'emprunt d'un livre.**  
R : Tous les exemplaires sont probablement déjà empruntés. Vérifiez les emprunts en cours pour ce livre dans **Bibliothèque** → **Emprunts** → filtrer par statut **En cours**.

**Q : Comment imprimer le reçu d'un paiement ?**  
R : Dans **Paiements** → liste, cliquez sur **PDF** en face du paiement. Le reçu s'affiche dans le navigateur — utilisez `Ctrl+P` pour imprimer.

**Q : La cloche de notifications affiche "présences manquantes" mais nous n'avons pas cours ce jour.**  
R : La détection est basée sur toutes les classes actives. Si une classe n'a pas cours un jour donné, il suffit d'ignorer l'alerte. Une fonctionnalité de calendrier scolaire (jours fériés, congés) pourra être ajoutée dans une future version.

**Q : Comment changer le taux de change FC/USD ?**  
R : Menu **Taux** → **Nouveau taux**. Saisissez le nouveau taux et marquez-le comme actif. L'ancien taux est automatiquement désactivé.

**Q : Puis-je avoir plusieurs années académiques actives simultanément ?**  
R : Non. Une seule année peut être marquée comme **courante**. C'est cette année qui est utilisée par défaut dans tous les modules.

**Q : Quel est le mot de passe par défaut d'un nouvel utilisateur ?**  
R : Il est affiché dans le message de confirmation lors de la création du compte. Il est de la forme `Credol@{année}` (ex : `Credol@2026`). Demandez à l'utilisateur de le changer dès sa première connexion.

**Q : Comment voir qui a fait quoi dans le système ?**  
R : Le **journal d'audit** (menu **Journal d'audit**, accessible aux administrateurs) enregistre toutes les actions effectuées : création, modification et suppression, avec l'utilisateur, la date et l'adresse IP.

---

*Document rédigé pour INSOFT MASOMO v2.0 — Juillet 2026*  
*Développé par [InSoftware SARL](https://insoftware.tech)*  
*Toute reproduction doit être autorisée par l'administrateur système.*
