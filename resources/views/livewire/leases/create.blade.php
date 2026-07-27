<div class="max-w-3xl space-y-5">
    <div class="flex items-center gap-3">
        <a href="{{ route('leases.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">← Retour</a>
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Nouveau contrat de location</h1>
    </div>

    <x-card>
        <form wire:submit="save" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-label for="property_id">Bien immobilier à louer</x-label>
                    <select wire:model.live="property_id" id="property_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">— Choisir un bien disponible —</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->title }} ({{ $property->reference }}) - {{ number_format((float)$property->rent_amount, 0, ',', ' ') }} FCFA</option>
                        @endforeach
                    </select>
                    @error('property_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-label for="tenant_id">Locataire</x-label>
                    <select wire:model="tenant_id" id="tenant_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">— Choisir un locataire —</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->full_name }} ({{ $tenant->reference }})</option>
                        @endforeach
                    </select>
                    @error('tenant_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <x-label for="template_id">Modèle de contrat (facultatif)</x-label>
                <select wire:model="template_id" id="template_id"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">— Aucun modèle —</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                    @endforeach
                </select>
                @error('template_id') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-label for="start_date">Date de prise d'effet (début)</x-label>
                    <x-input wire:model="start_date" type="date" id="start_date" />
                    @error('start_date') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-label for="end_date">Date de fin de bail</x-label>
                    <x-input wire:model="end_date" type="date" id="end_date" />
                    @error('end_date') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <x-label for="rent_amount">Loyer mensuel (FCFA)</x-label>
                    <x-input wire:model="rent_amount" type="number" step="1000" id="rent_amount" />
                    @error('rent_amount') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-label for="charges_amount">Charges mensuelles (FCFA)</x-label>
                    <x-input wire:model="charges_amount" type="number" step="1000" id="charges_amount" />
                    @error('charges_amount') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-label for="payment_due_day">Jour d'échéance (1-31)</x-label>
                    <x-input wire:model="payment_due_day" type="number" min="1" max="31" id="payment_due_day" />
                    @error('payment_due_day') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <x-label for="deposit_amount">Dépôt de garantie / Caution (FCFA)</x-label>
                <x-input wire:model="deposit_amount" type="number" step="1000" id="deposit_amount" />
                @error('deposit_amount') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>Créer le contrat (Brouillon)</span>
                    <span wire:loading>Création...</span>
                </x-button>
                <a href="{{ route('leases.index') }}">
                    <x-button type="button" variant="secondary">Annuler</x-button>
                </a>
            </div>
        </form>
    </x-card>
</div>
