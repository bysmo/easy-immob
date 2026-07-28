<?php

namespace App\Livewire\Leases;

use App\Domain\Agency\Models\Agency;
use App\Domain\Lease\Actions\ActivateLeaseAction;
use App\Domain\Lease\Actions\TerminateLeaseAction;
use App\Domain\Lease\Enums\LeaseStatus;
use App\Domain\Lease\Models\Lease;
use App\Domain\Notification\Models\SystemNotification;
use App\Domain\Rent\Models\RentHistory;
use App\Domain\Tenant\Models\Tenant;
use App\Livewire\Traits\WithDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public string $statusFilter = '';

    // Renewal Modal State
    public bool $showRenewModal = false;
    public ?int $selectedLeaseId = null;
    public ?string $new_end_date = null;
    public ?float $new_rent_amount = null;
    public string $renewal_notes = '';

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function activate(int $leaseId, ActivateLeaseAction $action): void
    {
        $lease = Lease::where('id', $leaseId)->firstOrFail();
        $this->authorize('update', $lease);

        try {
            $action->execute($lease);
            session()->flash('success', "Le contrat {$lease->reference} a été activé avec succès.");
        } catch (\InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function terminate(int $leaseId, TerminateLeaseAction $action): void
    {
        $lease = Lease::where('id', $leaseId)->firstOrFail();
        $this->authorize('update', $lease);

        $action->execute($lease);
        session()->flash('success', "Le contrat {$lease->reference} a été résilié.");
    }

    public function notifyTenant(int $leaseId): void
    {
        $lease = Lease::with(['tenant', 'property'])->findOrFail($leaseId);
        $this->authorize('view', $lease);

        $agencyId = $lease->agency_id ?? 1;

        if ($lease->tenant_id) {
            $endDateFormatted = $lease->end_date ? $lease->end_date->format('d/m/Y') : 'inconnue';
            SystemNotification::create([
                'agency_id'      => $agencyId,
                'recipient_type' => Tenant::class,
                'recipient_id'   => $lease->tenant_id,
                'type'           => 'lease_expiration_reminder',
                'channel'        => 'database',
                'subject'        => "Rappel d'échéance de votre bail : {$lease->reference}",
                'content'        => "Bonjour " . ($lease->tenant?->full_name ?? 'Locataire') . ", votre contrat de bail pour le bien " . ($lease->property?->title ?? 'Bien') . " arrive à échéance le {$endDateFormatted}. Merci de contacter l'agence pour convenir du renouvellement.",
                'sent_at'        => now(),
                'status'         => 'unread',
            ]);

            session()->flash('success', "La notification de relance d'échéance a été envoyée au locataire {$lease->tenant?->full_name}.");
        } else {
            session()->flash('error', "Aucun locataire n'est associé à ce contrat.");
        }
    }

    public function notifyAgency(int $leaseId): void
    {
        $lease = Lease::with(['tenant', 'property'])->findOrFail($leaseId);
        $this->authorize('view', $lease);

        $agencyId = $lease->agency_id ?? 1;
        $endDateFormatted = $lease->end_date ? $lease->end_date->format('d/m/Y') : 'inconnue';

        SystemNotification::create([
            'agency_id'      => $agencyId,
            'recipient_type' => Agency::class,
            'recipient_id'   => $agencyId,
            'type'           => 'lease_expiration_agency_alert',
            'channel'        => 'database',
            'subject'        => "Alerte Échéance de bail : {$lease->reference}",
            'content'        => "Le contrat de bail {$lease->reference} du locataire " . ($lease->tenant?->full_name ?? 'Locataire') . " pour le bien " . ($lease->property?->title ?? 'Bien') . " arrive à échéance le {$endDateFormatted}. Action requise pour renouvellement ou départ.",
            'sent_at'        => now(),
            'status'         => 'unread',
        ]);

        session()->flash('success', "Alerte d'échéance de bail transmise à l'agence pour le contrat {$lease->reference}.");
    }

    public function openRenewModal(int $leaseId): void
    {
        $lease = Lease::findOrFail($leaseId);
        $this->authorize('update', $lease);

        $this->selectedLeaseId = $lease->id;
        $this->new_end_date    = $lease->end_date ? $lease->end_date->copy()->addYear()->format('Y-m-d') : now()->addYear()->format('Y-m-d');
        $this->new_rent_amount = (float) $lease->rent_amount;
        $this->renewal_notes   = '';
        $this->showRenewModal  = true;
    }

    public function closeRenewModal(): void
    {
        $this->showRenewModal = false;
    }

    public function renewLease(): void
    {
        if (! $this->selectedLeaseId) {
            return;
        }

        $lease = Lease::with(['tenant', 'property'])->findOrFail($this->selectedLeaseId);
        $this->authorize('update', $lease);

        $this->validate([
            'new_end_date'    => 'required|date|after:today',
            'new_rent_amount' => 'required|numeric|gt:0',
        ], [
            'new_end_date.required'    => 'La nouvelle date de fin de bail est requise.',
            'new_end_date.after'       => 'La date de fin doit être dans le futur.',
            'new_rent_amount.required' => 'Le montant du loyer est requis.',
            'new_rent_amount.gt'       => 'Le montant du loyer doit être supérieur à 0.',
        ]);

        $agencyId = $lease->agency_id ?? 1;
        $oldRent  = (float) $lease->rent_amount;
        $newRent  = (float) $this->new_rent_amount;

        // Si le loyer change lors du renouvellement, enregistrer l'historique de loyer
        if (abs($newRent - $oldRent) > 0.01) {
            RentHistory::create([
                'agency_id'       => $agencyId,
                'property_id'     => $lease->property_id,
                'lease_id'        => $lease->id,
                'old_rent_amount' => $oldRent,
                'new_rent_amount' => $newRent,
                'change_amount'   => $newRent - $oldRent,
                'reason'          => 'Renouvellement de contrat de bail' . ($this->renewal_notes ? " — {$this->renewal_notes}" : ''),
                'user_id'         => Auth::id(),
                'effective_date'  => now()->format('Y-m-d'),
            ]);

            // Mettre à jour également le bien immobilier
            if ($lease->property) {
                $lease->property->update(['rent_amount' => $newRent]);
            }
        }

        // Renouveler et réactiver le contrat
        $lease->update([
            'end_date'    => $this->new_end_date,
            'rent_amount' => $newRent,
            'status'      => 'active',
        ]);

        $newEndDateFormatted = $lease->end_date->format('d/m/Y');

        // Notifier le locataire
        if ($lease->tenant_id) {
            SystemNotification::create([
                'agency_id'      => $agencyId,
                'recipient_type' => Tenant::class,
                'recipient_id'   => $lease->tenant_id,
                'type'           => 'lease_renewed',
                'channel'        => 'database',
                'subject'        => "Contrat de bail renouvelé : {$lease->reference}",
                'content'        => "Félicitations ! Votre contrat de bail pour le bien " . ($lease->property?->title ?? 'Bien') . " a été renouvelé avec succès jusqu'au {$newEndDateFormatted} (Loyer : " . number_format($newRent, 0, ',', ' ') . " FCFA).",
                'sent_at'        => now(),
                'status'         => 'unread',
            ]);
        }

        // Notifier l'agence
        SystemNotification::create([
            'agency_id'      => $agencyId,
            'recipient_type' => Agency::class,
            'recipient_id'   => $agencyId,
            'type'           => 'lease_renewed_agency',
            'channel'        => 'database',
            'subject'        => "Bail renouvelé : {$lease->reference}",
            'content'        => "Le contrat de bail {$lease->reference} (" . ($lease->tenant?->full_name ?? 'Locataire') . ") a été prolongé jusqu'au {$newEndDateFormatted}.",
            'sent_at'        => now(),
            'status'         => 'unread',
        ]);

        $this->showRenewModal = false;
        session()->flash('success', "Le contrat de bail {$lease->reference} a été renouvelé avec succès jusqu'au {$newEndDateFormatted}.");
    }

    public function render(): \Illuminate\View\View
    {
        $query = Lease::with(['property', 'tenant'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('reference', 'like', '%' . $this->search . '%')
                        ->orWhereHas('tenant', function ($tenantQ) {
                            $tenantQ->where('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('property', function ($propQ) {
                            $propQ->where('title', 'like', '%' . $this->search . '%')
                                ->orWhere('reference', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function ($q) {
                if ($this->statusFilter === 'expiring_soon') {
                    $q->where('status', 'active')
                      ->whereNotNull('end_date')
                      ->where('end_date', '<=', now()->addDays(60));
                } else {
                    $q->where('status', $this->statusFilter);
                }
            });

        $leases = $this->applySorting($query, 'created_at', 'desc')->paginate($this->perPage);

        return view('livewire.leases.index', [
            'leases'        => $leases,
            'statusOptions' => LeaseStatus::options(),
        ]);
    }
}
