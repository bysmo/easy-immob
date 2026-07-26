# Déploiement

## Environnement

- local ;
- staging ;
- production.

## Production

Prévoir :
- Nginx ;
- PHP-FPM ;
- MySQL ;
- Redis ;
- Supervisor ;
- SSL ;
- sauvegardes automatiques.

## Commandes

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart

Les migrations de production doivent être exécutées avec précaution.

Les sauvegardes de base de données doivent être automatisées.
