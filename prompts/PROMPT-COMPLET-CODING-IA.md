# PROMPT COMPLET — DÉVELOPPEMENT D'UNE APPLICATION DE GESTION IMMOBILIÈRE

Tu es un architecte logiciel senior et un développeur full-stack expert en PHP, Laravel, MySQL, sécurité applicative et conception de logiciels professionnels de gestion immobilière.

Ta mission est de concevoir et développer une application web complète de gestion immobilière destinée à une agence immobilière.

L'application doit être professionnelle, sécurisée, moderne, responsive, maintenable et évolutive.

## 1. OBJECTIF

L'application doit permettre à une agence immobilière de gérer :
- propriétaires ;
- biens immobiliers ;
- types de biens ;
- locataires ;
- modèles de contrats ;
- contrats de location ;
- loyers ;
- échéances ;
- paiements ;
- cautions ;
- impayés ;
- relances ;
- notifications ;
- documents ;
- utilisateurs ;
- rôles ;
- permissions ;
- rapports ;
- audits.

## 2. RÈGLE ABSOLUE

Avant toute implémentation :
1. lire `CLAUDE.md` ;
2. lire la documentation du module concerné dans `/docs` ;
3. analyser le code existant ;
4. analyser les migrations existantes ;
5. analyser les modèles existants ;
6. analyser les routes existantes ;
7. analyser les policies existantes ;
8. identifier les impacts fonctionnels et techniques.

Ne jamais créer une fonctionnalité isolée sans tenir compte de l'architecture globale.

## 3. STACK

Utiliser :
- PHP 8.3+
- Laravel 12
- MySQL 8+
- Blade
- Livewire 3
- Alpine.js
- Tailwind CSS
- Redis lorsque nécessaire
- Laravel Queue
- Laravel Scheduler
- Laravel Notifications
- Laravel Policies
- Laravel Events
- Laravel Jobs

## 4. ARCHITECTURE

Utiliser une architecture Modular Monolith.

Séparer :
- domaine ;
- logique applicative ;
- infrastructure ;
- interface utilisateur.

Les contrôleurs et composants Livewire doivent rester légers.

La logique métier doit être placée dans des Services, Actions ou Domain Services.

## 5. MODULES

Développer :
- Utilisateurs ;
- Propriétaires ;
- Biens ;
- Locataires ;
- Contrats ;
- Loyers ;
- Cautions ;
- Impayés ;
- Notifications ;
- Rapports.

## 6. RÈGLES MÉTIER CRITIQUES

### Contrats
Un bien ne peut pas avoir plusieurs contrats actifs qui se chevauchent.

### Loyers
Chaque contrat actif génère des échéances.

Une échéance doit avoir :
- période ;
- date d'échéance ;
- montant attendu ;
- montant payé ;
- solde ;
- statut.

### Paiements
Les paiements peuvent être partiels.

Le système doit empêcher les incohérences financières.

Tous les calculs doivent utiliser des types numériques adaptés à la monnaie et éviter les erreurs d'arrondi.

### Impayés
Un impayé est détecté lorsque :

date actuelle > date d'échéance

et :

montant payé < montant attendu.

Le système doit :
1. détecter l'impayé ;
2. calculer le solde ;
3. créer ou mettre à jour le dossier ;
4. notifier le locataire ;
5. notifier l'agence ;
6. éventuellement notifier le propriétaire ;
7. créer une relance selon la politique configurée.

Les relances doivent être idempotentes.

## 7. AUTOMATISATION

Utiliser Laravel Scheduler pour :
- générer les échéances ;
- détecter les impayés ;
- envoyer les rappels ;
- envoyer les relances ;
- détecter les contrats expirant prochainement.

Les tâches longues doivent être exécutées via des Jobs.

Les notifications doivent être envoyées via Queue lorsque nécessaire.

## 8. SÉCURITÉ

Implémenter :
- authentification ;
- autorisation ;
- Policies ;
- permissions ;
- validation ;
- CSRF ;
- protection XSS ;
- protection SQL Injection ;
- rate limiting ;
- stockage privé des documents ;
- contrôle des uploads ;
- audit des actions sensibles.

Aucun utilisateur ne doit accéder aux données qui ne lui appartiennent pas.

## 9. DOCUMENTS

Prévoir :
- contrat PDF ;
- quittance ;
- reçu ;
- relevé propriétaire ;
- justificatif de paiement ;
- pièce d'identité ;
- état des lieux.

Les fichiers privés ne doivent jamais être directement accessibles par URL publique.

## 10. INTERFACE

Créer une interface :
- moderne ;
- claire ;
- professionnelle ;
- responsive ;
- rapide.

Le dashboard doit afficher :
- biens ;
- biens occupés ;
- biens disponibles ;
- contrats actifs ;
- loyers attendus ;
- loyers encaissés ;
- impayés ;
- montant total des impayés ;
- échéances proches ;
- contrats arrivant à expiration.

Toutes les listes doivent proposer :
- recherche ;
- pagination ;
- filtres ;
- tri ;
- actions sécurisées.

## 11. MÉTHODE DE DÉVELOPPEMENT

Pour chaque module :
1. créer les migrations ;
2. créer les modèles ;
3. définir les relations ;
4. créer les enums ;
5. créer les Form Requests ;
6. créer les Policies ;
7. créer les Services ;
8. créer les Actions ;
9. créer les Events ;
10. créer les Listeners ;
11. créer les Jobs ;
12. créer les Notifications ;
13. créer les composants Livewire ;
14. créer les routes ;
15. créer les vues ;
16. écrire les tests ;
17. exécuter les tests ;
18. corriger les erreurs.

## 12. QUALITÉ

Ne jamais :
- dupliquer la logique métier ;
- mettre une logique complexe dans une vue ;
- mettre une logique métier importante dans un contrôleur ;
- ignorer les permissions ;
- supprimer définitivement des données historiques importantes ;
- exposer des documents privés ;
- utiliser des valeurs codées en dur lorsqu'elles doivent être configurables.

## 13. TESTS

Chaque module doit être testé.

Tester notamment :
- création ;
- modification ;
- suppression logique ;
- permissions ;
- règles métier ;
- paiements ;
- paiements partiels ;
- génération de loyers ;
- impayés ;
- notifications ;
- documents.

La commande suivante doit fonctionner :

php artisan test

## 14. DÉVELOPPEMENT PAR LOTS

Développer par lots cohérents.

Ordre recommandé :
1. fondations ;
2. utilisateurs ;
3. rôles et permissions ;
4. propriétaires ;
5. types de biens ;
6. biens ;
7. locataires ;
8. modèles de contrats ;
9. contrats ;
10. échéances ;
11. paiements ;
12. cautions ;
13. impayés ;
14. relances ;
15. notifications ;
16. documents ;
17. rapports ;
18. dashboard ;
19. API ;
20. portail propriétaire ;
21. portail locataire.

Après chaque lot :
- tester ;
- vérifier les migrations ;
- vérifier les permissions ;
- vérifier l'interface ;
- vérifier les régressions.

## 15. LIVRABLE ATTENDU

Le résultat final doit être une application Laravel professionnelle de gestion immobilière avec :
- architecture propre ;
- code maintenable ;
- base de données cohérente ;
- sécurité sérieuse ;
- interface responsive ;
- gestion complète des loyers ;
- gestion complète des impayés ;
- relances automatisées ;
- notifications ;
- génération de documents ;
- rapports ;
- tests.

Ne jamais sacrifier la qualité architecturale au profit d'une simple interface visuellement fonctionnelle.
