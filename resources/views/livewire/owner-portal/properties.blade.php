<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Mes Biens Immobiliers</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Statut de vos biens par agence</p>
        </div>

        {{-- Filtres --}}
        <div class="flex gap-3 flex-wrap">
            <select wire:model.live="statusFilter"
                    class="text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Tous les statuts</option>
                <option value="available">Disponible</option>
                <option value="rented">En location</option>
                <option value="maintenance">En travaux</option>
            </select>

            <select wire:model.live="agencyFilter"
                    class="text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Toutes les agences</option>
                @foreach ($agencies as $agency)
                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if ($grouped->isEmpty())
        <div class="text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800">
            <x-icon name="building" class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
            <p class="text-slate-500">Aucun bien ne correspond à vos filtres.</p>
        </div>
    @else
        @foreach ($grouped as $agencyName => $properties)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-xs">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex items-center gap-3">
                    <x-icon name="building" class="w-5 h-5 text-emerald-600" />
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">{{ $agencyName }}</h2>
                    <span class="ml-auto text-xs text-slate-500 dark:text-slate-400">{{ $properties->count() }} bien(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50/50 dark:bg-slate-800/30">
                                <th class="py-3 px-4">Référence</th>
                                <th class="py-3 px-4">Bien</th>
                                <th class="py-3 px-4">Type</th>
                                <th class="py-3 px-4">Statut</th>
                                <th class="py-3 px-4 text-right">Loyer HC</th>
                                <th class="py-3 px-4 text-right">IRF</th>
                                <th class="py-3 px-4 text-right">Commission</th>
                                <th class="py-3 px-4 text-right">Net Bailleur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                            @foreach ($properties as $property)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ $property->reference }}</td>
                                    <td class="py-3 px-4">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $property->title }}</p>
                                        <p class="text-xs text-slate-400">{{ $property->city }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-xs text-slate-500">{{ $property->propertyType?->name ?? '—' }}</td>
                                    <td class="py-3 px-4">
                                        @php
                                            $statusClasses = [
                                                'available'   => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                'rented'      => 'bg-blue-100 text-blue-700 border-blue-200',
                                                'maintenance' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            ];
                                            $statusLabels = [
                                                'available'   => 'Disponible',
                                                'rented'      => 'En location',
                                                'maintenance' => 'En travaux',
                                            ];
                                            $statusVal = $property->status instanceof \BackedEnum ? $property->status->value : (string)$property->status;
                                        @endphp
                                        <span class="text-xs px-2 py-0.5 rounded-full border {{ $statusClasses[$statusVal] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                            {{ $statusLabels[$statusVal] ?? $statusVal }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-semibold text-slate-900 dark:text-white">
                                        {{ number_format((float)$property->rent_amount, 0, ',', ' ') }} F
                                    </td>
                                    <td class="py-3 px-4 text-right text-rose-600 text-xs">
                                        {{ $property->is_subject_to_irf ? '− ' . number_format($property->irf_amount, 0, ',', ' ') . ' F' : '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-right text-amber-600 text-xs">
                                        − {{ number_format($property->agency_fee_amount, 0, ',', ' ') }} F
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-emerald-700 dark:text-emerald-400">
                                        {{ number_format($property->net_owner_income, 0, ',', ' ') }} F
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif
</div>
