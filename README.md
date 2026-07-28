<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="EasyImmob Logo">
</p>

<h1 align="center">🏢 EasyImmob - Plateforme SaaS de Gestion Immobilière & Locative Multi-Agences</h1>

<p align="center">
  <strong>Solution professionnelle de gestion immobilière moderne, puissante et hautement sécurisée pour agences, gestionnaires locatifs et administrateurs SaaS.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Livewire-3.x-4E5BA6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 3">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8+">
  <img src="https://img.shields.io/badge/Tests-98%20Passed-emerald?style=for-the-badge&logo=phpunit&logoColor=white" alt="98 Tests Passed">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License MIT">
</p>

---

## 📌 Sommaire

- [À propos du projet](#-à-propos-du-projet)
- [Espace Admin SaaS & Abonnements Agences](#-espace-admin-saas--abonnements-agences)
- [Fonctionnalités Principales](#-fonctionnalités-principales)
- [Architecture Technique & Stack](#-architecture-technique--stack)
- [Structure du Code (Modular Monolith)](#-structure-du-code-modular-monolith)
- [Prérequis](#-prérequis)
- [Guide d'Installation](#-guide-dinstallation)
- [Comptes de Démonstration](#-comptes-de-démonstration)
- [Commandes Utiles & Tests](#-commandes-utiles--tests)
- [Sécurité & Isolation des Rôles (RBAC)](#-sécurité--isolation-des-rôles-rbac)
- [Roadmap & Évolutions](#-roadmap--évolutions)
- [Licence](#-licence)

---

## 🚀 À propos du projet

**EasyImmob** est une solution **SaaS Multi-Agences** de gestion locative et immobilière conçue pour les agences immobilières, les administrateurs de biens et les propriétaires indépendants. 

L'application automatise l'ensemble du cycle de vie immobilier : de la gestion du parc immobilier et des baux à la facturation des loyers, le quittancement automatique, la gestion des impayés et des incidents, jusqu'à la gestion des abonnements SaaS des agences et l'administration générale de la plateforme.

### 🌟 Points forts

- **Architecture SaaS & Isolation Strict (Multi-Tenancy)** : Isolation hermétique des données inter-agences avec droits réservés au **Super Admin SaaS**.
- **Gestion des Formules d'Abonnement SaaS** : Essai gratuit 3 mois offert à l'inscription, forfaits évolutifs selon le nombre de biens à louer (*Starter*, *Pro Business*, *Enterprise*).
- **Contrôle Strict des Quotas & Modales Tailwind CSS** : Modales interactives modernes pour la confirmation de changement de forfait et validation stricte des capacités de biens gérés à la rétrogradation.
- **Notification Instantanée Administrateur SaaS** : Alertes automatiques transmises au Super Admin à chaque inscription d'une nouvelle agence.
- **Vitrine Locataire & Catalogue Réservé** : Recherche de biens accessible exclusivement aux locataires, messagerie instantanée agence-locataire.
- **Signalement d'Incidents Enrichi** : Déclaration d'incidents avec enregistrement audio vocal et captures photos.
- **Reporting Financier & Export CSV** : Relevés de gestion propriétaires, factures SaaS imprimables et rapports d'impayés.

---

## 💎 Espace Admin SaaS & Abonnements Agences

### 👑 1. Espace Admin SaaS (Super Admin)
L'Espace Admin SaaS est réservé **exclusivement au Super Admin de la plateforme** (`saasadmin@easyimmob.com`). Les utilisateurs d'agences immobilières n'ont aucun accès à cet espace.

- **Dashboard Global SaaS** : Statistiques consolidées de la plateforme (Revenu mensuel récurrent MRR, nombre d'agences actives/suspendues, total de biens gérés, facturation globale).
- **Gestion des Agences Clients** : Consultation du catalogue des agences, statut des abonnements, quota de biens consommés et actions de gestion (activation/suspension).
- **Factures SaaS Agences** : Vue et génération des factures d'abonnement SaaS d'agences, avec option d'impression PDF grand format.
- **Forfaits & Offres SaaS** : Paramétrage des formules d'abonnement (nom, tarif mensuel/annuel, limite de biens, avantages).

### 📦 2. Offres & Formules d'Abonnement Agences
- **Essai Gratuit (3 mois)** : Offre d'essai de 3 mois attribuée automatiquement à l'inscription initiale d'une agence (`0 FCFA`, jusqu'à 10 biens).
- **Starter** : Pour petites agences (10 biens max, facturation mensuelle ou annuelle avec 2 mois offerts).
- **Pro Business** : Solution complète pour agences en croissance (50 biens max).
- **Enterprise** : Capacité illimitée pour réseaux et grandes agences.

### 🔒 3. Rôles de Rétrogradation & Contrôle de Quota
- **Interdiction de rétrograder vers l'Essai Gratuit** : Une fois sur un plan payant, une agence ne peut plus souscrire à l'essai gratuit initial.
- **Vérification de la Capacité de Biens** : Toute tentative de passer à une formule inférieure avec une capacité inférieure au nombre actuel de biens gérés est bloquée par une modale d'erreur explicite.

---

## ⚡ Fonctionnalités Principales

### 📊 1. Tableaux de Bord Analytiques
- **Vue Agence** : Taux d'occupation, encaissements du mois, impayés, baux à renouveler, incidents en cours.
- **Vue Super Admin** : KPIs financiers globaux, répartition des packages SaaS et suivi des dernières agences inscrites.

### 🏢 2. Gestion du Parc Immobilier & Propriétaires
- **Propriétaires** : Répertoire complet, comptes de reversement et commissions.
- **Biens & Lots** : Appartements, villas, immeubles, locaux commerciaux. Suivi des loyers, charges, cautions et statut d'occupation.

### 📜 3. Contrats de Location & Modèles (Leases)
- Modèles de contrat configurables (`LeaseTemplates`) avec injection automatique des variables dynamiques.
- Quittancement automatisé à l'échéance.

### 💶 4. Loyers, Encaissements & Impayés
- Échéanciers de loyers mensuels automatisés (`RentSchedule`).
- Modes de paiement : Espèces, Virement, Chèque, Mobile Money (Orange Money, MTN MoMo, Wave).
- Dossiers d'impayés classés par sévérité (Faible, Moyenne, Élevée, Critique) avec historique de relances.

### 🛠️ 5. Incidents & Signalements Vocaulx/Photos
- Interface de déclaration d'incidents par le locataire avec enregistrement audio vocal et photos jointes.

### 💬 6. Recherche de Biens & Messagerie Locataire
- **Catalogue de Biens** : Accessible exclusivement aux locataires pour rechercher un logement disponible.
- **Messagerie en Direct** : Fil de discussion interactif entre locataires et agences.

---

## 🛠️ Architecture Technique & Stack

| Composant | Technologie / Package | Rôle |
| :--- | :--- | :--- |
| **Langage** | PHP 8.3+ | Typage strict et performances modernes |
| **Framework Backend** | Laravel 12.x | Socle applicatif, ORM Eloquent, Queues, Scheduler |
| **Architecture** | Modular Monolith (`app/Domain/...`) | Découpage DDD clean et maintenable par domaine métier |
| **Frontend Reactive** | Livewire 3.x & Alpine.js | Interfaces interactives réactives et modales Tailwind |
| **Styling & UI** | Tailwind CSS & DaisyUI | Design responsive, mode sombre, glassmorphism |
| **Authentification** | Laravel Fortify | Authentification sécurisée, profil & réinitialisation |
| **Gestion des Droits** | Spatie Laravel Permission | Rôles et permissions granulaires (RBAC) |
| **Tests Automatisés** | PHPUnit 12 (98 Tests Passed) | Suite de tests d'intégration et unitaires complète |

---

## 📁 Structure du Code (Modular Monolith)

L'application suit une architecture **Modular Monolith (DDD)** située dans `app/Domain/` :

```text
easy-immob/
├── app/
│   ├── Domain/                      # Domaines Métier Découpés
│   │   ├── Agency/                  # Gestion Multi-Agences & Paramètres
│   │   ├── Arrears/                 # Impayés & Relances
│   │   ├── Audit/                   # Logs d'activités & Audit
│   │   ├── Deposit/                 # Dépôts de Garantie / Cautions
│   │   ├── Incident/                # Signalement & Gestion d'Incidents
│   │   ├── Lease/                   # Baux & Modèles de Contrat
│   │   ├── Notification/            # Centre de Notifications & Alertes Super Admin
│   │   ├── Owner/                   # Propriétaires & États de gestion
│   │   ├── Payment/                 # Modes de Paiement & Règlements
│   │   ├── Property/                # Parc Immobilier & Types de Biens
│   │   ├── Rent/                    # Échéanciers de Loyers & Quittances
│   │   ├── Report/                  # Reporting Financier & Exports CSV/PDF
│   │   ├── Subscription/            # Forfaits SaaS, Factures SaaS & Abonnements Agences
│   │   └── Tenant/                  # Locataires & Dossiers
│   ├── Application/                 # Services applicatifs, DTOs & Actions (RegisterAgencyAction)
│   ├── Infrastructure/              # Implémentations techniques (Sms, PDF, Mail)
│   ├── Livewire/                    # Composants Frontend Livewire 3 (Admin SaaS & Agence)
│   ├── Models/                      # Modèle Utilisateur principal & Trait AgencyScoped
│   └── Support/                     # Menu latéral dynamique (SidebarMenu)
├── database/
│   ├── migrations/                  # Migrations structurées
│   └── seeders/                     # Données de référence & Démonstration
├── docs/                            # Spécifications & Documentation fonctionnelle
└── routes/
    └── web.php                      # Routes sécurisées et groupées par middleware/rôle
```

---

## 💻 Prérequis

- **PHP** >= 8.3 (`pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `fileinfo`, `xml`, `json`).
- **Composer** >= 2.6
- **Node.js** >= 18.x & **NPM** >= 9.x
- **MySQL** >= 8.0 (ou SQLite pour le développement/test).

---

## 📥 Guide d'Installation

### 1. Cloner le dépôt
```bash
git clone https://github.com/votre-org/easy-immob.git
cd easy-immob
```

### 2. Installer les dépendances
```bash
composer install
npm install
```

### 3. Configurer l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

Définissez la connexion dans votre fichier `.env` :
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=easy_immob
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrer et alimenter la base de données
```bash
php artisan migrate:fresh --seed
```

### 5. Lancer l'application
```bash
composer run dev
# ou séparément : php artisan serve & npm run dev
```

L'application est disponible sur : **`http://127.0.0.1:8000`**

---

## 🔑 Comptes de Démonstration

Après l'exécution des seeders, tous les comptes sont prêts à l'emploi avec le mot de passe unique : **`password`**

| Rôle | Email | Périmètre & Droits d'Accès |
| :--- | :--- | :--- |
| **Super Admin SaaS** | `saasadmin@easyimmob.com` | **Administrateur Plateforme SaaS** (Stats globales, Agences, Factures SaaS, Plans) |
| **Administrateur Agence** | `admin@easyimmob.com` | **Directeur Agence (Horizon Immobilier)** - Gestion globale de l'agence |
| **Gestionnaire** | `gestionnaire@easyimmob.com` | Gestion des Biens, Baux, Locataires, Incidents et Impayés |
| **Comptable** | `comptable@easyimmob.com` | Encaissements des loyers, Cautions, Reporting financier |
| **Agent** | `agent@easyimmob.com` | Visites, consultation du parc et suivi des incidents |
| **Locataire** | `locataire@easyimmob.com` | Espace locataire, Quittances, Recherche de biens, Déclaration d'incidents |
| **Admin Agence 2** | `admin.prestige@easyimmob.com` | **Directeur Agence (Prestige Habitat)** - Démonstration multi-agences isolées |

---

## ⚡ Commandes Utiles & Tests

### Lancer la suite de tests PHPUnit (98 Tests Passed)
```bash
vendor/bin/phpunit
# ou
php artisan test
```

### Nettoyage et formatage du code (Pint)
```bash
vendor/bin/pint
```

### Worker de Queue (Notifications & Alerte Super Admin)
```bash
php artisan queue:work
```

---

## 🛡️ Sécurité & Isolation des Rôles (RBAC)

- `can:saas.admin` : Middleware protégeant l'Espace Admin SaaS pour le rôle `Super Admin`.
- `agency_id` : Scoping automatique de toutes les entités agence via le trait `AgencyScoped`.
- **Catalogue & Recherche de biens** : Réservé aux utilisateurs ayant le rôle `Locataire`.

---

## 📄 Licence

Ce projet est sous licence **MIT**.

<p align="center">
  Développé avec ❤️ par l'équipe <strong>EasyImmob</strong>.
</p>
