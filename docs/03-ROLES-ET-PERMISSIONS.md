# Rôles et permissions

## Administrateur

Accès complet.

## Gestionnaire immobilier

Peut gérer :
- propriétaires ;
- biens ;
- locataires ;
- contrats ;
- loyers ;
- impayés.

## Comptable

Peut gérer :
- paiements ;
- loyers ;
- cautions ;
- quittances ;
- rapports financiers.

## Agent immobilier

Peut gérer :
- biens ;
- propriétaires ;
- prospects ;
- visites.

## Propriétaire

Accès limité à :
- ses biens ;
- ses contrats ;
- ses revenus ;
- ses paiements ;
- ses documents.

## Locataire

Accès limité à :
- ses contrats ;
- ses loyers ;
- ses paiements ;
- ses quittances ;
- ses notifications.

Les permissions doivent être granulaires.

Exemples :
- properties.view
- properties.create
- properties.update
- properties.delete
- leases.view
- leases.create
- rents.record-payment
- arrears.manage
- reports.view
