<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.saas-dashboard') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">&larr; Admin SaaS</a>
                <span class="text-xs text-slate-400">/ Factures Agences</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white mt-1">Factures d'Abonnement SaaS</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Historique des factures et encaissements des abonnements d'agences immobilières.</p>
        </div>

        <div>
            <button wire:click="openCreateModal" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                <x-icon name="rents" class="w-4 h-4" />
                Générer une Facture SaaS
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters & Search -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:w-80">
            <x-input type="text" wire:model.live.debounce.300ms="search" placeholder="N° facture ou nom agence..." class="w-full pl-10" />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <x-icon name="rents" class="w-4 h-4" />
            </div>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <select wire:model.live="statusFilter" class="rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-xs font-medium focus:ring-emerald-500">
                <option value="">Tous les statuts</option>
                <option value="paid">Payée</option>
                <option value="unpaid">En attente</option>
                <option value="overdue">En retard</option>
            </select>
        </div>
    </div>

    <!-- Table of Invoices -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-semibold uppercase text-xs">
                    <tr>
                        <th class="p-4">N° Facture</th>
                        <th class="p-4">Agence Immobilière</th>
                        <th class="p-4">Forfait / Période</th>
                        <th class="p-4">Date Émission</th>
                        <th class="p-4">Montant Total</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                            <td class="p-4 font-mono font-bold text-slate-900 dark:text-white">
                                {{ $invoice->number }}
                            </td>
                            <td class="p-4 font-semibold text-slate-900 dark:text-white">
                                {{ $invoice->agency?->name }}
                                <span class="block text-xs font-normal text-slate-400">{{ $invoice->agency?->email }}</span>
                            </td>
                            <td class="p-4">
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $invoice->subscriptionPlan?->name }}</span>
                                <span class="block text-[11px] text-slate-400 uppercase font-semibold">
                                    {{ $invoice->billing_cycle === 'yearly' ? 'Abonnement Annuel' : 'Abonnement Mensuel' }}
                                </span>
                            </td>
                            <td class="p-4 text-xs">
                                {{ $invoice->invoice_date?->format('d/m/Y') }}
                                <span class="block text-[11px] text-slate-400">Échéance : {{ $invoice->due_date?->format('d/m/Y') }}</span>
                            </td>
                            <td class="p-4 font-bold text-slate-900 dark:text-white text-base">
                                {{ $invoice->formatted_total }}
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $invoice->status_badge_class }}">
                                    {{ $invoice->status_label }}
                                </span>
                                @if($invoice->payment_method)
                                    <span class="block text-[10px] text-slate-400 mt-1">{{ $invoice->payment_method }}</span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-2">
                                @if($invoice->status !== 'paid')
                                    <button wire:click="openMarkPaidModal({{ $invoice->id }})" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 hover:bg-emerald-100 rounded-xl text-xs font-semibold transition">
                                        Encaisser
                                    </button>
                                @endif
                                <a href="{{ route('admin.saas-invoices.print', $invoice->id) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition inline-flex items-center gap-1">
                                    <x-icon name="reports" class="w-3 h-3" />
                                    Imprimer
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">Aucune facture SaaS trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Création de Facture -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl max-w-lg w-full border border-slate-200/80 dark:border-slate-800 shadow-2xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Générer une Facture SaaS</h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-label value="Agence Immobilière Client" />
                        <select wire:model="agency_id" class="w-full mt-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-emerald-500">
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->id }}">{{ $agency->name }} ({{ $agency->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-label value="Forfait SaaS" />
                            <select wire:model.live="subscription_plan_id" class="w-full mt-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-emerald-500">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-label value="Cycle" />
                            <select wire:model.live="billing_cycle" class="w-full mt-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-emerald-500">
                                <option value="monthly">Mensuel</option>
                                <option value="yearly">Annuel</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-label value="Montant Facturé (FCFA)" />
                            <x-input type="number" wire:model="amount" class="w-full mt-1" />
                        </div>
                        <div>
                            <x-label value="Statut Paiement" />
                            <select wire:model.live="status" class="w-full mt-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-emerald-500">
                                <option value="paid">Payée</option>
                                <option value="unpaid">En attente</option>
                                <option value="overdue">En retard</option>
                            </select>
                        </div>
                    </div>

                    @if($status === 'paid')
                        <div>
                            <x-label value="Moyen de Règlement" />
                            <select wire:model="payment_method" class="w-full mt-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-emerald-500">
                                <option value="Mobile Money (Orange Money)">Mobile Money (Orange Money)</option>
                                <option value="Mobile Money (MTN MoMo)">Mobile Money (MTN MoMo)</option>
                                <option value="Mobile Money (Wave)">Mobile Money (Wave)</option>
                                <option value="Virement Bancaire">Virement Bancaire</option>
                                <option value="Carte Bancaire">Carte Bancaire</option>
                                <option value="Espèces">Espèces</option>
                            </select>
                        </div>
                    @endif

                    <div>
                        <x-label value="Notes & Observations" />
                        <x-input type="text" wire:model="notes" placeholder="Règlement abonnement SaaS..." class="w-full mt-1" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button wire:click="$set('showCreateModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Annuler</button>
                    <button wire:click="createInvoice" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md">Générer la Facture</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Marquage comme Payée -->
    @if($showMarkPaidModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl max-w-md w-full border border-slate-200/80 dark:border-slate-800 shadow-2xl space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Confirmer le Règlement de la Facture</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Sélectionnez le moyen de paiement reçu pour valider l'encaissement.</p>

                <div>
                    <x-label value="Moyen de Règlement" />
                    <select wire:model="markPaidMethod" class="w-full mt-1 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-800 text-sm focus:ring-emerald-500">
                        <option value="Mobile Money (Orange Money)">Mobile Money (Orange Money)</option>
                        <option value="Mobile Money (MTN MoMo)">Mobile Money (MTN MoMo)</option>
                        <option value="Mobile Money (Wave)">Mobile Money (Wave)</option>
                        <option value="Virement Bancaire">Virement Bancaire</option>
                        <option value="Carte Bancaire">Carte Bancaire</option>
                        <option value="Espèces">Espèces</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button wire:click="$set('showMarkPaidModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">Annuler</button>
                    <button wire:click="markAsPaid" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md">Valider le Paiement</button>
                </div>
            </div>
        </div>
    @endif
</div>
