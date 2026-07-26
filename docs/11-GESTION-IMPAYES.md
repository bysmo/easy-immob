# Gestion des impayés

## Détection

Une échéance est en impayé lorsque :

date actuelle > date d'échéance
ET
montant payé < montant attendu.

## Niveaux

- warning ;
- serious ;
- critical.

Exemple :
- J+1 : première notification ;
- J+7 : première relance ;
- J+15 : relance renforcée ;
- J+30 : escalade vers l'agence et le propriétaire.

Les délais doivent être configurables.

## Dossier d'impayé

Le système doit conserver :
- montant initial ;
- montant payé ;
- montant restant ;
- historique ;
- relances ;
- notifications ;
- commentaires ;
- actions effectuées.
