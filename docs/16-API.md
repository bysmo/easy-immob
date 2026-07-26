# API

L'application doit être conçue pour exposer ultérieurement une API.

## API future

Prévoir Laravel Sanctum.

Endpoints futurs :
- GET /api/properties
- GET /api/tenants
- GET /api/leases
- GET /api/rents
- GET /api/payments
- GET /api/notifications

Les réponses API doivent utiliser :
- API Resources ;
- pagination ;
- validation ;
- gestion uniforme des erreurs.

La logique métier ne doit jamais être dupliquée entre l'interface web et l'API.
