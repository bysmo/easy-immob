<?php

namespace App\Livewire\ManagementContracts;

use App\Domain\Owner\Enums\ManagementContractStatus;
use App\Domain\Owner\Models\ManagementContract;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public ?int $owner_id = null;
    public string $reference = '';
    public string $title = 'Mandat de Gestion Immobilière';
    public string $start_date = '';
    public ?string $end_date = null;
    public int $duration_months = 12;
    public string $commission_type = 'percentage';
    public float $commission_value = 10.0;
    public ?float $agreed_rent_amount = null;
    public bool $irf_paid_by_owner = true;
    public bool $caution_kept_by_agency = true;
    public int $notice_period_months = 3;
    public ?string $payment_bank_details = null;
    public ?string $terms_and_conditions = null;
    public array $selectedProperties = [];

    public function mount(?int $ownerId = null): void
    {
        $this->start_date = now()->format('Y-m-d');
        $this->generateReference();

        if ($ownerId) {
            $this->owner_id = $ownerId;
        }
    }

    public function updatedOwnerId(): void
    {
        $this->selectedProperties = [];
    }

    public function generateReference(): void
    {
        $agencyId = Auth::user()?->agency_id;
        $count = ManagementContract::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->count() + 1;

        $this->reference = 'MAN-' . date('Y') . '-' . str_pad((string)$count, 4, '0', STR_PAD_LEFT);
    }

    public function save()
    {
        $this->validate([
            'owner_id'              => 'required|exists:owners,id',
            'reference'             => 'required|string|max:50',
            'title'                 => 'required|string|max:255',
            'start_date'            => 'required|date',
            'end_date'              => 'nullable|date|after_or_equal:start_date',
            'duration_months'       => 'required|integer|min:1',
            'commission_type'       => 'required|in:percentage,fixed',
            'commission_value'      => 'required|numeric|min:0',
            'agreed_rent_amount'    => 'nullable|numeric|min:0',
            'irf_paid_by_owner'     => 'boolean',
            'caution_kept_by_agency'=> 'boolean',
            'notice_period_months'  => 'required|integer|min:1',
            'payment_bank_details'  => 'nullable|string',
            'terms_and_conditions'  => 'nullable|string',
            'selectedProperties'    => 'nullable|array',
            'selectedProperties.*'  => 'exists:properties,id',
        ]);

        $contract = ManagementContract::create([
            'agency_id'             => Auth::user()->agency_id,
            'owner_id'              => $this->owner_id,
            'reference'             => $this->reference,
            'title'                 => $this->title,
            'start_date'            => $this->start_date,
            'end_date'              => $this->end_date,
            'duration_months'       => $this->duration_months,
            'commission_type'       => $this->commission_type,
            'commission_value'      => $this->commission_value,
            'agreed_rent_amount'    => $this->agreed_rent_amount,
            'irf_paid_by_owner'     => $this->irf_paid_by_owner,
            'caution_kept_by_agency'=> $this->caution_kept_by_agency,
            'notice_period_months'  => $this->notice_period_months,
            'payment_bank_details'  => $this->payment_bank_details,
            'terms_and_conditions'  => $this->terms_and_conditions,
            'status'                => ManagementContractStatus::Active,
            'signed_at'             => now(),
        ]);

        if (!empty($this->selectedProperties)) {
            Property::whereIn('id', $this->selectedProperties)
                ->where('owner_id', $this->owner_id)
                ->update(['management_contract_id' => $contract->id]);
        }

        session()->flash('success', 'Le mandat de gestion ' . $contract->reference . ' a été créé avec succès.');

        return redirect()->route('management-contracts.show', $contract->id);
    }

    public function render()
    {
        $owners = Owner::orderBy('last_name')->get();
        $ownerProperties = $this->owner_id
            ? Property::where('owner_id', $this->owner_id)->get()
            : collect();

        return view('livewire.management-contracts.create', [
            'owners'          => $owners,
            'ownerProperties' => $ownerProperties,
        ])->layout('components.layouts.app');
    }
}
