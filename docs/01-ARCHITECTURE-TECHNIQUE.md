# Architecture technique

## Stack

- Laravel 12
- PHP 8.3+
- MySQL 8+
- Blade
- Livewire 3
- Alpine.js
- Tailwind CSS
- Redis recommandé

## Architecture

Utiliser une architecture Modular Monolith.

Les modules fonctionnels sont :
- Owner
- Property
- Tenant
- Lease
- Rent
- Payment
- Deposit
- Arrears
- Notification
- Document
- User
- Reporting

## Organisation

Chaque module peut contenir :
- Models
- Services
- Actions
- DTOs
- Events
- Listeners
- Jobs
- Notifications
- Policies
- Queries

## Règle

Le domaine métier ne doit pas dépendre directement de l'interface utilisateur.
