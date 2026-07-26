# Gestion des contrats

## Modèles

Les modèles de contrat doivent être versionnés.

Un modèle peut contenir des variables :
- {{tenant_name}}
- {{owner_name}}
- {{property_address}}
- {{rent_amount}}
- {{deposit_amount}}
- {{start_date}}
- {{end_date}}

## Contrat

Un contrat doit permettre :
- sélection du modèle ;
- association au bien ;
- association au locataire ;
- définition des dates ;
- définition du loyer ;
- définition des charges ;
- définition de la caution ;
- définition du jour d'échéance ;
- génération PDF ;
- signature ;
- renouvellement ;
- résiliation.

## Statuts

- draft ;
- pending_signature ;
- active ;
- expired ;
- terminated ;
- cancelled.

Un contrat actif doit automatiquement générer les échéances de loyer.
