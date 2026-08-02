<x-mail::message>
# Bienvenue sur votre Espace Locataire

Bonjour **{{ $tenantName }}**,

L'agence **{{ $agencyName }}** vous a créé un compte sur la plateforme **EasyImmob** pour accéder à votre espace locataire en ligne.

Depuis votre espace, vous pourrez :
- 🏠 Consulter vos quittances de loyer et contrats de bail
- 🛠️ Déclarer et suivre vos demandes d'incident et réparations
- 💬 Échanger directement avec votre agence immobilière
- 🔍 Parcourir le catalogue et gérer vos demandes

<x-mail::button :url="$url" color="success">
Activer mon compte locataire et créer mon mot de passe
</x-mail::button>

> ⚠️ Ce lien est valable **72 heures**. Au-delà, contactez votre agence pour obtenir un nouveau lien.

Si vous avez des questions, n'hésitez pas à contacter directement votre agence **{{ $agencyName }}**.

Cordialement,  
**L'équipe EasyImmob**
</x-mail::message>
