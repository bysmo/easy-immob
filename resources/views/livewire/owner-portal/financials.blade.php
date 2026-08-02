<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Récap Financier</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Loyers et reversements par période</p>
        </div>
        <div>
            <select wire:model.live="period"
                    class="text-sm rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Toutes les périodes</option>
                @foreach ($availablePeriods as $p)
                    <option value="{{ $p }}">{{ $p }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Totaux --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @php
            $cards = [
                ['label' => 'Loyers attendus', 'value' => $totals['gross'], 'color' => 'blue'],
                ['label' => 'Loyers encaissés', 'value' => $totals['collected'], 'color' => 'emerald'],
                ['label' => 'Net reversé', 'value' => $totals['net_payout'], 'color' => 'teal'],
                ['label' => 'Déjà payé', 'value' => $totals['paid_out'], 'color' => 'indigo'],
                ['label' => 'Reste à percevoir', 'value' => max(0, $totals['pending']), 'color' => 'amber'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 shadow-xs text-center">
                <p class="text-lg font-bold text-slate-900 dark:text-white">
                    {{ number_format($card['value'], 0, ',', ' ') }}
                </p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">{{ $card['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Reversements (Payouts) --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3">
            <x-icon name="rents" class="w-5 h-5 text-emerald-600" />
            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Reversements Bailleur</h2>
        </div>

        @if ($payouts->isEmpty())
            <div class="text-center py-10">
                <p class="text-sm text-slate-400">Aucun reversement enregistré pour cette période.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200/80 dark:border-slate-800 text-[11px] font-bold uppercase tracking-wider text-slate-500 bg-slate-50/50 dark:bg-slate-800/30">
                            <th class="py-3 px-4">Référence</th>
                            <th class="py-3 px-4">Période</th>
                            <th class="py-3 px-4 text-right">Brut</th>
                            <th class="py-3 px-4 text-right">Commission</th>
                            <th class="py-3 px-4 text-right">IRF</th>
                            <th class="py-3 px-4 text-right">Réparations</th>
                            <th class="py-3 px-4 text-right">Net Bailleur</th>
                            <th class="py-3 px-4 text-right">Payé</th>
                            <th class="py-3 px-4">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                        @foreach ($payouts as $payout)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-4 font-mono text-xs text-slate-500">{{ $payout->reference }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-900 dark:text-white">{{ $payout->period }}</td>
                                <td class="py-3 px-4 text-right">{{ number_format((float)$payout->gross_amount, 0, ',', ' ') }}</td>
                                <td class="py-3 px-4 text-right text-amber-600">− {{ number_format((float)$payout->commission_amount, 0, ',', ' ') }}</td>
                                <td class="py-3 px-4 text-right text-rose-600">− {{ number_format((float)$payout->irf_amount, 0, ',', ' ') }}</td>
                                <td class="py-3 px-4 text-right text-orange-600">− {{ number_format((float)$payout->repair_amount, 0, ',', ' ') }}</td>
                                <td class="py-3 px-4 text-right font-bold text-emerald-700 dark:text-emerald-400">
                                    {{ number_format((float)$payout->net_amount, 0, ',', ' ') }}
                                </td>
                                <td class="py-3 px-4 text-right text-teal-700 dark:text-teal-400">
                                    {{ number_format((float)$payout->paid_amount, 0, ',', ' ') }}
                                </td>
                                <td class="py-3 px-4">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            'partial' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'paid'    => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        ];
                                        $statusLabels = ['pending' => 'En attente', 'partial' => 'Partiel', 'paid' => 'Payé'];
                                        $st = $payout->status instanceof \BackedEnum ? $payout->status->value : (string)$payout->status;
                                    @endphp
                                    <span class="text-xs px-2 py-0.5 rounded-full border {{ $statusClasses[$st] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        {{ $statusLabels[$st] ?? $st }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
