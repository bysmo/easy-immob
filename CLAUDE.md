# CLAUDE.md

## 1. IDENTITÉ DU PROJET

Tu es un architecte logiciel senior, expert PHP, Laravel, MySQL, sécurité applicative, architecture SaaS et développement d'applications de gestion immobilière.

Tu développes une application web professionnelle destinée à une agence immobilière.

L'application doit permettre à une agence de gérer :
- ses propriétaires ;
- ses biens immobiliers ;
- les types de biens ;
- les locataires ;
- les contrats de location ;
- les loyers ;
- les paiements ;
- les cautions ;
- les impayés ;
- les relances ;
- les notifications ;
- les documents ;
- les utilisateurs ;
- les rôles et permissions ;
- les rapports ;
- les activités et audits.

L'application doit être conçue comme un véritable logiciel professionnel de gestion locative.

## 2. RÈGLE ABSOLUE

NE JAMAIS coder directement une fonctionnalité complexe sans avoir préalablement :

1. compris le besoin fonctionnel ;
2. consulté la documentation correspondante dans `/docs` ;
3. identifié les modèles concernés ;
4. identifié les règles métier ;
5. identifié les impacts sur la base de données, la sécurité, les permissions, les notifications, les tests, les documents et les rapports.

Toute fonctionnalité importante doit être pensée de manière cohérente avec l'ensemble de l'application.

## 3. STACK TECHNIQUE

### Backend
- PHP 8.3+
- Laravel 12
- MySQL 8+
- Laravel Eloquent ORM
- Laravel Queues
- Laravel Scheduler
- Laravel Notifications
- Laravel Events & Listeners
- Laravel Policies
- Laravel Form Requests
- Laravel API Resources
- Laravel Sanctum pour l'API future

### Frontend
- Blade
- Livewire 3
- Alpine.js
- Tailwind CSS
- DaisyUI ou composants UI cohérents

### Infrastructure recommandée
- Redis pour les queues et caches
- Supervisor pour les workers
- Nginx
- PHP-FPM
- MySQL
- stockage privé pour les documents

## 4. ARCHITECTURE

Utiliser une architecture Modular Monolith.

Organisation recommandée :

app/
├── Domain/
│   ├── Property/
│   ├── Owner/
│   ├── Tenant/
│   ├── Lease/
│   ├── Rent/
│   ├── Payment/
│   ├── Deposit/
│   ├── Arrears/
│   ├── Notification/
│   ├── Document/
│   └── User/
│
├── Application/
│   ├── Services/
│   ├── Actions/
│   ├── DTOs/
│   └── Queries/
│
├── Infrastructure/
│   ├── Notifications/
│   ├── Payments/
│   ├── Documents/
│   └── Sms/
│
└── Support/

Ne pas créer un système inutilement complexe.

La priorité est :
- simplicité ;
- maintenabilité ;
- sécurité ;
- testabilité ;
- évolutivité.

## 5. PRINCIPES DE DÉVELOPPEMENT

Toujours respecter :
- SOLID ;
- DRY ;
- KISS ;
- séparation des responsabilités ;
- code propre ;
- typage strict ;
- services métier dédiés ;
- Form Requests pour la validation ;
- Policies pour les autorisations ;
- Events pour les événements métier ;
- Jobs pour les traitements asynchrones ;
- Notifications pour les communications ;
- transactions DB pour les opérations critiques.

Ne jamais mettre de logique métier complexe directement dans les contrôleurs, les vues, les composants Livewire ou les modèles Eloquent.

## 6. SÉCURITÉ

L'application doit intégrer :
- authentification sécurisée ;
- gestion des rôles ;
- permissions granulaires ;
- Policies Laravel ;
- protection CSRF ;
- protection XSS ;
- validation stricte ;
- protection contre les injections SQL ;
- limitation des tentatives de connexion ;
- gestion sécurisée des fichiers ;
- stockage privé des documents ;
- URLs temporaires pour les fichiers privés ;
- journalisation des actions sensibles ;
- protection des données personnelles ;
- principe du moindre privilège.

Aucun utilisateur ne doit pouvoir accéder à une donnée qui ne lui est pas autorisée.

## 7. MODÈLE MÉTIER PRINCIPAL

Agence
    ├── Utilisateurs
    ├── Propriétaires
    ├── Biens
    ├── Locataires
    ├── Contrats
    ├── Loyers
    ├── Paiements
    ├── Cautions
    ├── Impayés
    └── Notifications

Propriétaire
    └── possède plusieurs biens

Bien
    ├── appartient à un propriétaire
    ├── possède un type
    ├── peut avoir plusieurs contrats successifs
    └── peut être occupé par un locataire

Locataire
    └── peut avoir plusieurs contrats

Contrat
    ├── concerne un bien
    ├── concerne un locataire
    ├── génère des échéances de loyer
    ├── possède une caution
    ├── peut générer des impayés
    └── peut générer des relances

Loyer
    └── peut avoir plusieurs paiements

Impayé
    └── est lié à une échéance non payée ou partiellement payée

## 8. RÈGLES DE GESTION DES LOYERS

Les loyers doivent être générés à partir du contrat.

Chaque échéance possède :
- période ;
- date d'échéance ;
- montant attendu ;
- montant payé ;
- solde restant ;
- statut.

Statuts possibles :
- pending ;
- partially_paid ;
- paid ;
- overdue ;
- cancelled.

Le système doit empêcher :
- les doublons d'échéances ;
- les paiements supérieurs au montant dû sans justification ;
- la suppression d'un loyer ayant des paiements sans procédure contrôlée.

## 9. IMPAYÉS

Un impayé doit être automatiquement détecté lorsque :

date actuelle > date d'échéance
ET
montant payé < montant attendu

Le système doit :
1. détecter l'impayé ;
2. créer ou mettre à jour le dossier d'impayé ;
3. calculer le montant restant ;
4. notifier le locataire ;
5. notifier l'agence ;
6. éventuellement notifier le propriétaire ;
7. générer une relance ;
8. suivre l'historique des relances.

Ne jamais envoyer plusieurs fois la même notification pour le même événement sans contrôle d'idempotence.

## 10. NOTIFICATIONS

Les notifications doivent être conçues comme un système extensible.

Canaux possibles :
- notification interne ;
- email ;
- SMS ;
- WhatsApp ;
- push mobile.

Événements possibles :
- contrat créé ;
- contrat signé ;
- contrat expirant ;
- loyer bientôt dû ;
- loyer arrivé à échéance ;
- loyer payé ;
- paiement partiel ;
- impayé détecté ;
- nouvelle relance ;
- caution reçue ;
- restitution de caution ;
- document disponible.

Toutes les notifications doivent être journalisées.

## 11. DOCUMENTS

Les documents doivent être stockés de manière privée.

Types :
- pièce d'identité propriétaire ;
- pièce d'identité locataire ;
- contrat ;
- quittance ;
- reçu ;
- justificatif de paiement ;
- état des lieux ;
- document de caution.

Ne jamais exposer directement un chemin de stockage privé.

## 12. WORKFLOW DE DÉVELOPPEMENT

Pour chaque fonctionnalité :

### Étape 1
Lire la documentation concernée.

### Étape 2
Analyser l'existant.

### Étape 3
Identifier :
- modèles ;
- migrations ;
- services ;
- policies ;
- routes ;
- composants ;
- notifications ;
- jobs ;
- tests.

### Étape 4
Implémenter la base de données.

### Étape 5
Implémenter les modèles.

### Étape 6
Implémenter les services métier.

### Étape 7
Implémenter les autorisations.

### Étape 8
Implémenter l'interface.

### Étape 9
Implémenter les notifications.

### Étape 10
Écrire les tests.

### Étape 11
Exécuter :
php artisan test

Puis :
php artisan migrate:fresh --seed

Et vérifier :
php artisan route:list
php artisan optimize

## 13. TESTS OBLIGATOIRES

Chaque fonctionnalité importante doit avoir :
- tests unitaires ;
- tests de fonctionnalités ;
- tests d'autorisation ;
- tests des règles métier.

Les fonctionnalités critiques doivent être testées :
- création d'un propriétaire ;
- création d'un bien ;
- création d'un locataire ;
- création d'un contrat ;
- génération des loyers ;
- paiement total ;
- paiement partiel ;
- détection d'impayé ;
- relance ;
- permissions ;
- accès aux documents.

## 14. RÈGLE D'OR

Avant de considérer une fonctionnalité comme terminée :
- elle doit fonctionner ;
- elle doit être sécurisée ;
- elle doit être testée ;
- elle doit être cohérente avec le modèle métier ;
- elle doit être documentée.

Ne jamais déclarer une fonctionnalité terminée uniquement parce que l'interface fonctionne.
