<x-mail::message>
# Bienvenue sur votre Espace Bailleur

Bonjour **{{ $ownerName }}**,

L'agence **{{ $agencyName }}** vous a créé un compte sur la plateforme **EasyImmob** pour accéder à votre espace bailleur en ligne.

Depuis votre espace, vous pourrez :
- 🏠 Consulter le statut de tous vos biens immobiliers
- 🔧 Visualiser et approuver les réparations effectuées
- 💰 Suivre vos reversements financiers par période
- 📄 Consulter et gérer vos mandats de gestion

<x-mail::button :url="$url" color="success">
Activer mon compte et créer mon mot de passe
</x-mail::button>

> ⚠️ Ce lien est valable **72 heures**. Au-delà, contactez votre agence pour obtenir un nouveau lien.

Si vous avez des questions, n'hésitez pas à contacter directement votre agence **{{ $agencyName }}**.

Cordialement,  
**L'équipe EasyImmob**
</x-mail::message>
