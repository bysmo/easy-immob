<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="EasyImmob Logo">
</p>

<h1 align="center">🏢 EasyImmob - Plateforme SaaS de Gestion Immobilière & Locative Multi-Agences</h1>

<p align="center">
  <strong>Solution professionnelle de gestion immobilière moderne, puissante et hautement sécurisée pour agences et gestionnaires locatifs.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Livewire-3.x-4E5BA6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 3">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8+">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License MIT">
</p>

---

## 📌 Sommaire

- [À propos du projet](#-à-propos-du-projet)
- [Fonctionnalités Principales](#-fonctionnalités-principales)
- [Architecture Technique & Stack](#-architecture-technique--stack)
- [Structure du Code (Modular Monolith)](#-structure-du-code-modular-monolith)
- [Prérequis](#-prérequis)
- [Guide d'Installation](#-guide-dinstallation)
- [Comptes de Démonstration](#-comptes-de-démonstration)
- [Commandes Utiles & Tests](#-commandes-utiles--tests)
- [Sécurité & Rôles (RBAC)](#-sécurité--rôles-rbac)
- [Roadmap & Évolutions](#-roadmap--évolutions)
- [Licence](#-licence)

---

## 🚀 À propos du projet

**EasyImmob** est une application SaaS de gestion locative et immobilière conçue spécifiquement pour les agences immobilières, les administrateurs de biens et les propriétaires indépendants. 

L'application automatise et simplifie le cycle de vie complet de la gestion locative : de la mise en valeur des biens à la gestion des propriétaires, la contractualisation des baux, le suivi rigoureux des loyers et des encaissements, la détection automatisée des impayés, jusqu'à la résolution des incidents et l'édition des quittances et comptes de gestion.

### 🌟 Points forts

- **Multi-Agences (Multi-Tenancy)** : Isolation stricte des données et des ressources par agence immobilière.
- **Gestion Complète des Échéances** : Génération automatique des loyers, calcul des soldes et gestion des acomptes/paiements partiels.
- **Système Anti-Impayés Intelligent** : Détection automatique des retards, relances graduelles et suivi des dossiers de recouvrement.
- **Expérience Utilisateur Interactive** : Interfaces dynamiques sous Livewire 3 & Alpine.js sans rechargement de page.
- **Rapports & Quittances Pro** : Génération et impression d'états financiers pour propriétaires et quittances de loyer.
- **Signalement d'Incidents Enrichi** : Suivi des dégradations/réparations avec possibilité de joindre des notes vocales audio et des photos.
- **Vitrine & Messagerie intégrée** : Catalogue public des logements et système de messagerie (Chat) en direct avec les locataires.

---

## ⚡ Fonctionnalités Principales

### 📊 1. Tableau de Bord Analytique (Dashboard)
- Synthèse des indicateurs clés de performance (KPIs) : Taux d'occupation, total encaissements du mois, montant des impayés, baux à renouveler, incidents ouverts.
- Alertes dynamiques et accès rapide aux actions stratégiques.

### 🏢 2. Gestion du Parc Immobilier & Propriétaires
- **Propriétaires** : Répertoire complet, pièces d'identité, historique des biens, comptes de reversement et commissions.
- **Biens & Lots** : Appartements, villas, immeubles, locaux commerciaux, boutiques. Suivi de l'équipement, des loyers hors charges, charges, cautions et statut d'occupation.

### 📜 3. Contrats de Location & Modèles (Leases)
- Création de baux (habitation, professionnel, commercial).
- Modèles de contrat configurables (`LeaseTemplates`) avec injection automatique des variables dynamiques (Locataire, Bien, Loyers, Agence).
- Impression et export au format contrat standard.

### 💶 4. Loyers, Encaissements & Quittances
- Échéancier de loyer automatisé mensuel (`RentSchedule`).
- Modes de règlement multiples : Espèces, Virement, Chèque, Mobile Money (Orange Money, MTN MoMo, Wave).
- Génération et impression automatique des **quittances de loyer** avec statut de règlement.

### 🔒 5. Dépôts de Garantie & Cautions
- Enregistrement des cautions encaissées à l'entrée du locataire.
- Suivi du statut (Encaissé, Restitué, Retenu pour dégradations/impayés).

### 🚨 6. Recouvrement & Impayés (Arrears)
- Détection automatique dès dépassement de la date d'échéance non soldée.
- Dossiers d'impayés classés par niveau de sévérité (Faible, Moyenne, Élevée, Critique).
- Historique des relances (Rappel amiable, Mise en demeure, Procédure contentieuse).

### 🛠️ 7. Incidents & Maintenance
- Signalement par le locataire ou le gestionnaire avec détails, niveau d'urgence et pièces jointes (enregistrement audio vocal / photos).
- Suivi de la résolution, artisan assigné et coût des réparations.

### 💬 8. Catalogue Public & Messagerie (Chat)
- Consultation du catalogue de biens disponibles à la location.
- Espace de discussion / messagerie interactive en temps réel entre locataire et agence.

### 📈 9. Reporting Financier & Exports
- **Owner Statements** : États de reversement propriétaires paramétrables (déduction des frais de gestion et commissions agence) avec mise en page d'impression professionnelle.
- **Export CSV** : Exportation de l'historique complet des paiements et encaissements.

---

## 🛠️ Architecture Technique & Stack

| Composant | Technologie / Package | Rôle |
| :--- | :--- | :--- |
| **Langage** | PHP 8.3+ | Typage strict et performances modernes |
| **Framework Backend** | Laravel 12.x | Socle applicatif, ORM Eloquent, Queues, Scheduler |
| **Architecture** | Modular Monolith (`app/Domain/...`) | Découpage DDD clean et maintenable par domaine métier |
| **Frontend Reactive** | Livewire 3.x & Alpine.js | Interfaces interactives réactives côté serveur/client |
| **Styling & UI** | Tailwind CSS & DaisyUI | Design responsive, moderne et élégant |
| **Authentification** | Laravel Fortify | Authentification sécurisée, réinitialisation, profil |
| **Gestion des Droits** | Spatie Laravel Permission | Rôles et permissions granulaires (RBAC) |
| **Tests Automatisés** | PHPUnit 12 / Pest | Suite de tests unitaires et d'intégration |

---

## 📁 Structure du Code (Modular Monolith)

L'application suit une architecture **Modular Monolith (DDD)** claire et maintenable située dans `app/Domain/` :

```text
easy-immob/
├── app/
│   ├── Domain/                      # Domaines Métier Découpés
│   │   ├── Agency/                  # Gestion Multi-Agences
│   │   ├── Arrears/                 # Impayés & Relances
│   │   ├── Audit/                   # Logs d'activités & Audit
│   │   ├── Deposit/                 # Dépôts de Garantie / Cautions
│   │   ├── Incident/                # Signalement & Gestion d'Incidents
│   │   ├── Lease/                   # Baux & Modèles de Contrat
│   │   ├── Notification/            # Centre de Notifications Multi-Canal
│   │   ├── Owner/                   # Propriétaires & États de gestion
│   │   ├── Payment/                 # Modes de Paiement & Règlements
│   │   ├── Property/                # Parc Immobilier & Types de Biens
│   │   ├── Rent/                    # Échéanciers de Loyers & Quittances
│   │   ├── Report/                  # Reporting Financier & Exports CSV/PDF
│   │   └── Tenant/                  # Locataires & Dossiers
│   ├── Application/                 # Services applicatifs, DTOs & Actions
│   ├── Infrastructure/              # Implémentations techniques (Sms, PDF, Mail)
│   ├── Livewire/                    # Composants Frontend Livewire 3
│   └── Models/                      # Modèle Utilisateur principal & Trait AgencyScoped
├── database/
│   ├── migrations/                  # Migrations structurées
│   └── seeders/                     # Données de référence & Démonstration
├── docs/                            # Spécifications & Documentation fonctionnelle
├── resources/
│   ├── views/                       # Vues Blade, Layouts & Modèles d'impression
│   └── css/ js/                     # Styles Tailwind & Scripts Alpine
└── routes/
    └── web.php                      # Routes sécurisées et groupées par module
```

---

## 💻 Prérequis

Assurez-vous de disposer des éléments suivants sur votre environnement de développement ou serveur :

- **PHP** >= 8.3 avec les extensions requises : `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `fileinfo`, `ctype`, `xml`, `json`.
- **Composer** >= 2.6
- **Node.js** >= 18.x & **NPM** >= 9.x
- **MySQL** >= 8.0 (ou MariaDB 10.5+) / SQLite pour les tests rapide.
- **Redis** (recommandé en production pour les files d'attente et le cache).

---

## 📥 Guide d'Installation

### 1. Cloner le dépôt
```bash
git clone https://github.com/votre-org/easy-immob.git
cd easy-immob
```

### 2. Installer les dépendances PHP & JS
```bash
composer install
npm install
```

### 3. Configurer l'environnement
Copiez le fichier de configuration exemple et gérez la clé d'application :
```bash
cp .env.example .env
php artisan key:generate
```

Modifiez le fichier `.env` pour configurer l'accès à votre base de données MySQL :
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=easy_immob
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Exécuter les migrations & charger les données de démonstration
```bash
php artisan migrate:fresh --seed
```

### 5. Compiler les assets & lancer le serveur de développement

Vous pouvez lancer l'ensemble des services (Artisan Serve, Queue Worker, Log Pail, Vite) via la commande prédéfinie :
```bash
composer run dev
```

*Ou séparément :*
```bash
# Dans un terminal :
php artisan serve

# Dans un second terminal :
npm run dev
```

L'application sera accessible sur : **`http://localhost:8000`** (ou `http://127.0.0.1:8000`).

---

## 🔑 Comptes de Démonstration

Après l'exécution des seeders (`DemoDataSeeder`), plusieurs comptes pré-configurés sont immédiatement utilisables avec le mot de passe unique : **`password`**

| Rôle | Email | Agence | Périmètre d'Accès |
| :--- | :--- | :--- | :--- |
| **Administrateur** | `admin@easyimmob.com` | Horizon Immobilier | Accès complet & Paramétrages |
| **Gestionnaire** | `gestionnaire@easyimmob.com` | Horizon Immobilier | Gestion Biens, Baux, Incidents, Impayés |
| **Comptable** | `comptable@easyimmob.com` | Horizon Immobilier | Encaisser Loyers, Cautions, Rapports |
| **Agent** | `agent@easyimmob.com` | Horizon Immobilier | Consultation Biens & Incidents terrain |
| **Locataire** | `locataire@easyimmob.com` | - | Espace locataire, Mes Loyers, Signalement |
| **Admin Agence 2** | `admin.prestige@easyimmob.com` | Prestige Habitat | Démonstration Multi-Agence (Données isolées) |

---

## ⚡ Commandes Utiles & Tests

### Lancer la suite de tests automatisés
```bash
composer test
# ou
php artisan test
```

### Formater et nettoyer le code (Pint)
```bash
vendor/bin/pint
```

### Exécuter le Worker de Queue (Notification & Relances)
```bash
php artisan queue:work
```

### Planificateur de tâches (Détection automatique des impayés)
```bash
php artisan schedule:run
```

---

## 🛡️ Sécurité & Rôles (RBAC)

L'accès aux fonctionnalités est rigoureusement encadré par des **Policies Laravel** et la gestion des permissions Spatie (`Spatie\Permission`) :

- `users.*` : Administration des collaborateurs et gestion des rôles.
- `owners.*` : Consultation, création et modification des propriétaires.
- `properties.*` : Gestion du catalogue immobilier.
- `tenants.*` : Gestion des dossiers locataires.
- `leases.*` : Contractualisation des baux et modèles de contrats.
- `rents.*` : Gestion des échéanciers et enregistrement des réglements.
- `arrears.*` : Suivi et relances des dossiers d'impayés.
- `incidents.*` : Traitement des déclarations d'incidents.
- `reports.*` : Génération des états financiers et d'impression.

Toutes les données sont automatiquement scopées par `agency_id` afin d'empêcher toute fuite d'informations inter-agences.

---

## 🗺️ Roadmap & Évolutions

- [x] **Phase 1 : Fondations** (Multi-agences, Auth, Biens, Propriétaires, Locataires, Baux)
- [x] **Phase 2 : Loyers & Encaissements** (Échéanciers, Quittances, Modes de règlement)
- [x] **Phase 3 : Impayés & Incidents** (Détection auto, Relances, Signalement audio/photo)
- [x] **Phase 4 : Dashboard & Reporting** (États de versement propriétaires, Exports CSV)
- [ ] **Phase 5 : Intégrations Mobile Money (API Gateway)** (Paiement automatique via API Mobile Money)
- [ ] **Phase 6 : Signature Électronique des Baux** (Intégration Yousign / DocuSign)
- [ ] **Phase 7 : Application Mobile React Native / Flutter** (Espace Dédié Locataire & Agent)

---

## 📄 Licence

Ce projet est sous licence **MIT**. Vous êtes libre de l'utiliser, le modifier et le distribuer dans le cadre de vos projets commerciaux ou personnels.

<p align="center">
  Développé avec ❤️ par l'équipe <strong>EasyImmob</strong>.
</p>
