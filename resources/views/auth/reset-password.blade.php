<x-layouts.guest title="Réinitialisation du mot de passe — EasyImmob">
    <livewire:auth.reset-password :token="$request->route('token')" />
</x-layouts.guest>
