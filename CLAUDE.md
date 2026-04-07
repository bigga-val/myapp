# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Credol App is a full-stack enterprise business management web application built with **Symfony 6.1** and **Doctrine ORM**. It manages orders, inventory, employees, payroll, sales, and financial tracking. The app appears to serve a retail/restaurant/supply chain business.

## Common Commands

### PHP / Symfony
```bash
composer install                          # Install PHP dependencies
php bin/console server:run                # Start development server
php bin/console doctrine:migrations:migrate  # Run database migrations
php bin/console doctrine:migrations:diff     # Generate migration from entity changes
php bin/console doctrine:fixtures:load    # Load test data fixtures
php bin/console cache:clear               # Clear Symfony cache
php bin/console make:entity               # Scaffold new entity
php bin/console make:controller           # Scaffold new controller
php bin/console make:form                 # Scaffold new form type
php bin/console make:migration            # Generate migration file
```

### Frontend (Tailwind CSS)
```bash
npm install          # Install Node dependencies
npm run dev          # Watch and recompile Tailwind CSS
npm run build        # Build Tailwind CSS for production (minified)
```

### Testing
```bash
php bin/phpunit                           # Run all tests
php bin/phpunit tests/path/to/TestFile.php  # Run a single test file
php bin/phpunit --filter testMethodName   # Run a single test by name
```

## Architecture

### Request Lifecycle
```
HTTP Request → public/index.php → Symfony Kernel → Router →
  Controller → (Repository query / Form handling) →
  EntityManager::flush() → Twig render / JsonResponse / RedirectResponse
```

### Source Structure (`src/`)
- **Controller/** — 26 controllers, one per domain. Use `#[Route]` attributes for routing. Controllers render Twig templates or return JSON/redirects.
- **Entity/** — 17 Doctrine ORM entities. Changes to entities require generating and running a migration.
- **Repository/** — One repository per entity. Custom queries go here, not in controllers.
- **Form/** — 18 Symfony FormType classes bound to entities. Named with a suffix like `Commande1Type`.
- **Service/** — `PdfService` and `FPdfGenerator` for PDF generation (invoices/reports).
- **Security/** — `LoginFormAuthenticator` (form-based login) and `EmailVerifier` (registration confirmation).

### Key Domain Entities
- **Order flow:** `Commande` → `CommandeApprobateur` (approval) → `CommandeReception` (receiving) → `CommandeProduit` (line items)
- **Inventory:** `Produits`, `CategorieProduit`, `Approvisionnement`
- **Sales:** `Vente`, `ProduitVendu`
- **Finance:** `Credit`, `Debit`, `Taux` (exchange rates)
- **HR/Payroll:** `Employe`, `Paie`, `PaieEmploye`
- **Other:** `Table` (restaurant table management), `User` (authentication)

### Templates (`templates/`)
Organized by domain, mirroring the Controller structure. Shared layout components are in `templates/components/layout/`. Twig templates pull in Tailwind CSS utility classes.

### Frontend
Tailwind CSS 3.2 with a custom color palette (`primary`, `secondary`, `success`, `danger`, `warning`, `info`, `dark`) and the Nunito font. Also includes Bootstrap and Toastr (notifications) served from `public/assets/`. The `tailwind.config.js` scans `./templates/**/*.twig` for class names.

### Authentication
Form-based login via `LoginFormAuthenticator`. After login, users are redirected to `app_set_sessions` (a custom session setup route). Registration includes email verification via `EmailVerifier`.

### Database
MySQL/MariaDB database named `db_credol`. Connection configured in `.env` (`DATABASE_URL`). Doctrine handles schema via migrations in `migrations/`.

### Async / Messaging
Symfony Messenger configured with a Doctrine transport (see `config/packages/messenger.yaml`).
