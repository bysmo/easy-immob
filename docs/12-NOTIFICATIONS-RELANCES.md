# Notifications et relances

## Événements

- contrat créé ;
- contrat bientôt expiré ;
- loyer bientôt dû ;
- loyer arrivé à échéance ;
- paiement reçu ;
- paiement partiel ;
- impayé détecté ;
- relance ;
- contrat résilié.

## Canaux

Le système doit être extensible :
- database ;
- email ;
- SMS ;
- WhatsApp ;
- push.

## Automatisation

Laravel Scheduler doit exécuter les tâches :
- détection des échéances ;
- détection des impayés ;
- envoi des rappels ;
- relances ;
- notifications d'expiration.

Exemples :

Chaque jour à 06:00 :
- identifier les loyers arrivant à échéance ;
- identifier les impayés ;
- envoyer les notifications.

Chaque heure :
- traiter la file des notifications.

Les jobs doivent être idempotents.
