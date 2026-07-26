# Phase 1 — Fondations : design

Référence : `docs/20-ROADMAP.md` (Phase 1), `CLAUDE.md`, `docs/03-ROLES-ET-PERMISSIONS.md`, `docs/04-MODELE-DONNEES.md`, `docs/15-SECURITE.md`, `docs/17-UX-UI.md`.

## 1. Périmètre

Ce lot couvre uniquement :
- installation du socle Laravel (packages, config) ;
- multi-tenance SaaS (agences) ;
- authentification (inscription, connexion, déconnexion, mot de passe oublié) ;
- rôles & permissions (6 rôles globaux, assignables aux utilisateurs de chaque agence) ;
- layout applicatif (sidebar + topbar, thème clair/emerald, dark mode) ;
- dashboard en état vide (structure complète, valeurs à zéro) ;
- journalisation des événements d'authentification (audit_logs).

Hors périmètre (phases suivantes) : propriétaires, biens, locataires, contrats, loyers, cautions, impayés, notifications multi-canal, documents, rapports, portails propriétaire/locataire, API.

Les entrées de menu correspondant aux modules non construits (Biens, Propriétaires, Locataires, Contrats, Loyers, Cautions, Impayés, Notifications, Rapports) sont visibles dans la sidebar dès ce lot, mais pointent vers une page « Bientôt disponible » — cela permet de valider la navigation complète sans attendre que chaque module soit livré.

## 2. Base de données locale

- `DB_CONNECTION=mysql`, base `easy_immob` sur `127.0.0.1:3306`, utilisateur `root` (déjà configuré et vérifié fonctionnel) pour le développement.
- `phpunit.xml` reste sur SQLite en mémoire (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`) pour l'exécution des tests : c'est déjà configuré, rapide, et isolé par exécution. Ce choix ne contredit pas la demande MySQL de l'utilisateur, qui portait sur l'environnement de développement — la suite de tests garde un backend en mémoire pour rester rapide et ne nécessiter aucun service externe.

## 3. Multi-tenance

### 3.1 Modèle `Agency`

Nouveau domaine `app/Domain/Agency/` (module non listé explicitement dans le CLAUDE.md §4 mais nécessaire comme racine du SaaS — ajouté en cohérence avec l'architecture Modular Monolith).

Table `agencies` conforme à `docs/04-MODELE-DONNEES.md` : `id, name, legal_name, email, phone, address, status, timestamps`.

### 3.2 Isolation des données

- Trait `BelongsToAgency` + Eloquent Global Scope, dans `app/Support/Tenancy/`.
- Tout modèle métier futur (Owner, Property, Tenant, Lease...) utilisera ce trait ; il ajoute automatiquement une clause `agency_id = <agence de l'utilisateur connecté>` à toutes les requêtes, et remplit `agency_id` à la création.
- Résolution de l'agence courante : `auth()->user()->agency_id` (pas de sous-domaine par tenant, pas de connexion multi-base — un seul schéma MySQL partagé, isolation par colonne).
- Un test dédié doit vérifier qu'un utilisateur de l'agence A ne peut ni lire ni modifier une ressource de l'agence B (y compris par accès direct à une route avec un ID deviné).

### 3.3 Rôles & permissions — scoping

Spatie Laravel-Permission est utilisé **sans** son mode « teams ». Les permissions Spatie décrivent uniquement ce qu'un rôle *peut faire* (ex. `properties.view`) ; elles sont globales au sens Spatie mais n'ont aucune portée sur les *données* — celle-ci est entièrement gérée par le Global Scope `BelongsToAgency` du §3.2. Cela évite de superposer deux mécanismes de scoping (Spatie teams + global scope maison) pour un seul et même problème.

Conséquence pratique : les noms de rôles (`Administrateur`, `Gestionnaire`, ...) sont partagés entre toutes les agences, mais chaque utilisateur n'est rattaché qu'à une seule agence (`users.agency_id`) et ne voit que les données de celle-ci.

### 3.4 Inscription self-service

Action `Application/Actions/Auth/RegisterAgencyAction` (transaction DB) :
1. crée l'`Agency` (statut actif) ;
2. crée le `User` rattaché à cette agence ;
3. assigne le rôle `Administrateur` au nouvel utilisateur. Les 6 rôles et leurs permissions par défaut (`docs/03-ROLES-ET-PERMISSIONS.md`) sont globaux (non dupliqués par agence, cf. §3.3) et seedés une seule fois via `RolesAndPermissionsSeeder` — l'action d'inscription ne fait qu'assigner un rôle existant, elle ne recrée jamais les rôles/permissions.
4. connecte automatiquement l'utilisateur après inscription.

Formulaire d'inscription (Livewire) : nom agence, nom admin, email, mot de passe (+ confirmation).

## 4. Authentification

- **Laravel Fortify**, en mode headless (`Fortify::loginView`, `Fortify::registerView`, ... pointent vers des composants Livewire maison — pas les vues par défaut du package).
- Fonctionnalités activées : login, register (via `RegisterAgencyAction`), logout, reset password. Vérification d'email et 2FA **désactivées** pour ce lot (pas demandées par les docs), mais l'activation future ne doit pas nécessiter de réécriture (rester sur les points d'extension standard de Fortify).
- Rate limiting sur les tentatives de connexion (throttle Fortify par défaut : 5 tentatives / minute par email+IP).
- Règles de mot de passe : règles Laravel par défaut (`Password::min(8)->mixedCase()->numbers()`), `BCRYPT_ROUNDS=12` (déjà en config).
- `User` : ajout colonne `agency_id` (FK `agencies.id`, `restrict on delete`).

## 5. Rôles & permissions

- Package **Spatie Laravel-Permission**.
- 6 rôles globaux, seedés une fois via `RolesAndPermissionsSeeder` : `Administrateur, Gestionnaire, Comptable, Agent, Propriétaire, Locataire`.
- Permissions définies selon la liste de `docs/03-ROLES-ET-PERMISSIONS.md` (`properties.view`, `properties.create`, `properties.update`, `properties.delete`, `leases.view`, `leases.create`, `rents.record-payment`, `arrears.manage`, `reports.view`, etc.) — même si les modules correspondants n'existent pas encore, la table de permissions est posée dès ce lot pour éviter des migrations de permissions répétées à chaque phase.
- Middleware Spatie (`role:`, `permission:`) sur les routes protégées.
- Écrans Administration (Phase 1) : liste des utilisateurs de l'agence, invitation/création d'utilisateur, assignation de rôle. Pas de gestion fine des permissions par rôle dans l'UI pour ce lot (les permissions par rôle sont fixées par le seeder) — l'UI de personnalisation des permissions par agence est hors périmètre.

## 6. Journalisation (audit)

- Table `audit_logs` conforme à `docs/04-MODELE-DONNEES.md` (`agency_id, user_id, action, entity_type, entity_id, old_values, new_values, ip_address, user_agent, created_at`).
- Service `Support/Audit/AuditLogger` avec une méthode `log(string $action, ?Model $entity = null, array $old = [], array $new = [])`.
- Événements journalisés dans ce lot : connexion réussie, connexion échouée, déconnexion, création d'utilisateur, changement de rôle.
- Les futurs modules (paiement, suppression, téléchargement de document...) réutiliseront le même service — pas de nouvelle table à prévoir.

## 7. UI — layout, thème, dashboard

### 7.1 Stack front

- Livewire 3 + Alpine.js + Tailwind CSS.
- Pas de DaisyUI : un petit set de composants Blade réutilisables (`x-button`, `x-card`, `x-table`, `x-badge`, `x-input`) pour garder un contrôle total sur le rendu — validé avec l'utilisateur en remplacement de DaisyUI (CLAUDE.md laissait le choix : « DaisyUI ou composants UI cohérents »).

### 7.2 Layout

- Sidebar fixe (desktop/tablette) listant tous les modules du menu principal (`docs/02-ARCHITECTURE-FONCTIONNELLE.md`), collapsible en menu burger sur mobile.
- Topbar : recherche globale (input, non fonctionnelle tant que les entités recherchables n'existent pas — placeholder désactivé), notifications (icône, vide pour ce lot), menu utilisateur (profil, déconnexion).
- Entrées de sidebar pour les modules non construits redirigent vers une page « Bientôt disponible » plutôt que d'être masquées.

### 7.3 Thème

- Fond clair, accent vert émeraude (`emerald-600` / `emerald-500` Tailwind comme couleur primaire).
- Toggle clair/sombre (persisté en session ou cookie, via Alpine.js + classe `dark` sur `<html>`).

### 7.4 Dashboard

Cartes stats affichées dès ce lot (valeurs à `0` / `—` tant que les modules sous-jacents n'existent pas) :
- nombre total de biens ;
- biens occupés / vacants ;
- contrats actifs ;
- loyers attendus / encaissés (période courante) ;
- impayés (nombre + montant total) ;
- contrats expirant bientôt ;
- échéances à venir.

Ces cartes seront alimentées progressivement par les phases suivantes (aucun changement de layout attendu, seulement le remplacement des valeurs statiques par des requêtes réelles).

## 8. Tests

- **Inscription** : crée une agence + un utilisateur admin + assigne le rôle Administrateur ; les 6 rôles globaux existent après le premier seeding.
- **Connexion / déconnexion** : succès, échec (mauvais mot de passe), rate limiting après 5 tentatives.
- **Isolation multi-tenant** : agence A ne peut pas lire/modifier une ressource (ex. un autre `User`) de l'agence B, y compris par accès direct à une route avec un ID d'une autre agence (403/404 attendu).
- **Permissions** : un utilisateur sans la permission requise reçoit un 403 sur une route protégée ; un utilisateur avec la permission y accède.
- **Audit** : une connexion réussie/échouée crée bien une entrée `audit_logs`.
- **Dashboard** : rendu de toutes les cartes stats avec les valeurs par défaut.

## 9. Packages à ajouter

Composer :
- `laravel/fortify`
- `spatie/laravel-permission`
- `livewire/livewire`

Tailwind CSS 4 (`@tailwindcss/vite`) est déjà présent dans `package.json`. Alpine.js n'est pas ajouté séparément : Livewire 3 embarque son propre Alpine — un ajout npm dédié ne sera nécessaire que si des composants Alpine sont utilisés en dehors de tout composant Livewire.
