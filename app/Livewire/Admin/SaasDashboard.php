<?php

namespace App\Livewire\Admin;

use App\Domain\Agency\Models\Agency;
use App\Domain\Property\Models\Property;
use App\Domain\Subscription\Models\SaasInvoice;
use App\Domain\Subscription\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SaasDashboard extends Component
{
    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Accès réservé exclusivement au Super Admin SaaS.');
        }

        // Total agences et statut
        $totalAgencies = Agency::count();
        $activeAgencies = Agency::where('status', 'active')->count();
        $suspendedAgencies = Agency::where('status', 'suspended')->count();

        // Total biens dans tout le SaaS
        $totalSaasProperties = Property::withoutGlobalScopes()->count();

        // Calculs de Revenus (MRR & ARR)
        $mrr = Agency::where('status', 'active')
            ->with('subscriptionPlan')
            ->get()
            ->sum(function ($agency) {
                if (!$agency->subscriptionPlan) return 0;
                return $agency->billing_cycle === 'yearly'
                    ? round($agency->subscriptionPlan->price_yearly / 12, 2)
                    : $agency->subscriptionPlan->price_monthly;
            });

        $arr = $mrr * 12;

        // Statistique Factures SaaS
        $totalPaidInvoicesAmount = SaasInvoice::where('status', 'paid')->sum('total_amount');
        $totalPendingInvoicesAmount = SaasInvoice::where('status', 'unpaid')->sum('total_amount');

        // Distribution des Forfaits
        $plansStats = SubscriptionPlan::withCount('agencies')->get();

        // Dernières agences inscrites
        $recentAgencies = Agency::with('subscriptionPlan')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Dernières factures générées
        $recentInvoices = SaasInvoice::with(['agency', 'subscriptionPlan'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('livewire.admin.saas-dashboard', compact(
            'totalAgencies',
            'activeAgencies',
            'suspendedAgencies',
            'totalSaasProperties',
            'mrr',
            'arr',
            'totalPaidInvoicesAmount',
            'totalPendingInvoicesAmount',
            'plansStats',
            'recentAgencies',
            'recentInvoices'
        ));
    }
}
