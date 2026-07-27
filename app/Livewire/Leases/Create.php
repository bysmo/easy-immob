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

    public string $tenant_code_input = '';
    public ?string $tenant_code_message = null;
    public ?string $tenant_code_error = null;

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

    public function searchTenantByCode(): void
    {
        $this->tenant_code_message = null;
        $this->tenant_code_error = null;

        $code = strtoupper(trim($this->tenant_code_input));

        if (empty($code)) {
            $this->tenant_code_error = 'Veuillez saisir un code locataire.';
            return;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $tenant = Tenant::withoutGlobalScopes()
            ->where('reference', $code)
            ->first();

        if (! $tenant) {
            $this->tenant_code_error = "Aucun locataire trouvé avec le code {$code}.";
            return;
        }

        // Attach tenant to this agency if not assigned
        if (! $tenant->agency_id) {
            $tenant->agency_id = $user->agency_id;
            $tenant->save();
        }

        $this->tenant_id = $tenant->id;
        $this->tenant_code_message = "Locataire rattaché : {$tenant->full_name} ({$tenant->reference})";
    }

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
