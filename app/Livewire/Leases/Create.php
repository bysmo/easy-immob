<?php

namespace App\Livewire\Leases;

use App\Application\Services\ReferenceGenerator;
use App\Domain\Lease\Enums\LeaseStatus;
use App\Domain\Lease\Models\Lease;
use App\Domain\Lease\Models\LeaseTemplate;
use App\Domain\Property\Models\Property;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    #[Validate('required|exists:properties,id')]
    public ?int $property_id = null;

    #[Validate('required|exists:tenants,id')]
    public ?int $tenant_id = null;

    #[Validate('nullable|exists:lease_templates,id')]
    public ?int $template_id = null;

    #[Validate('required|date')]
    public string $start_date = '';

    #[Validate('required|date|after:start_date')]
    public string $end_date = '';

    #[Validate('required|numeric|min:0')]
    public float $rent_amount = 0;

    #[Validate('required|numeric|min:0')]
    public float $charges_amount = 0;

    #[Validate('required|integer|min:1|max:31')]
    public int $payment_due_day = 5;

    #[Validate('required|numeric|min:0')]
    public float $deposit_amount = 0;

    public function updatedPropertyId(): void
    {
        if ($this->property_id) {
            $property = Property::find($this->property_id);
            if ($property) {
                $this->rent_amount = (float) $property->rent_amount;
            }
        }
    }

    public function save(ReferenceGenerator $generator): void
    {
        $this->authorize('create', Lease::class);
        $this->validate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $reference = $generator->generate(Lease::class, $user->agency_id, 'CON');

        $lease = Lease::create([
            'reference'       => $reference,
            'property_id'     => $this->property_id,
            'tenant_id'       => $this->tenant_id,
            'template_id'     => $this->template_id,
            'start_date'      => $this->start_date,
            'end_date'        => $this->end_date,
            'rent_amount'     => $this->rent_amount,
            'charges_amount'  => $this->charges_amount,
            'payment_due_day' => $this->payment_due_day,
            'deposit_amount'  => $this->deposit_amount,
            'status'          => LeaseStatus::Draft,
        ]);

        session()->flash('success', "Le contrat {$lease->reference} a été créé au statut Brouillon.");

        $this->redirect(route('leases.show', $lease->id), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        $properties = Property::where('status', 'available')->orderBy('title')->get();
        $tenants    = Tenant::where('status', 'active')->orderBy('last_name')->get();
        $templates  = LeaseTemplate::where('status', 'active')->orderBy('name')->get();

        return view('livewire.leases.create', compact('properties', 'tenants', 'templates'));
    }
}
