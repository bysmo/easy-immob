<?php

namespace App\Livewire\Properties;

use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use App\Domain\Rent\Models\RentHistory;
use App\Domain\Lease\Models\Lease;
use App\Domain\Notification\Models\SystemNotification;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public Property $property;

    #[Validate('required|exists:owners,id')]
    public ?int $owner_id = null;

    #[Validate('required|exists:property_types,id')]
    public ?int $property_type_id = null;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string')]
    public ?string $description = null;

    #[Validate('required|string|max:255')]
    public string $address = '';

    #[Validate('required|string|max:255')]
    public string $city = '';

    #[Validate('nullable|string|max:255')]
    public ?string $neighborhood = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $surface_area = null;

    #[Validate('nullable|integer|min:0')]
    public ?int $bedrooms = null;

    #[Validate('nullable|integer|min:0')]
    public ?int $bathrooms = null;

    #[Validate('required|numeric|min:1000')]
    public float $rent_amount = 0;

    #[Validate('required')]
    public string $status = 'available';

    // Propriétés pour la révision / augmentation du loyer
    public bool $showIncreaseModal = false;
    public float $new_rent_amount = 0;
    public string $increase_reason = '';
    public ?string $effective_date = null;
    public bool $update_active_lease = true;

    public function mount(int $propertyId): void
    {
        $this->property = Property::with(['rentHistories.user'])->where('id', $propertyId)->first() ?? abort(404);
        $this->authorize('update', $this->property);

        $this->owner_id         = $this->property->owner_id;
        $this->property_type_id = $this->property->property_type_id;
        $this->title            = $this->property->title;
        $this->description      = $this->property->description;
        $this->address          = $this->property->address;
        $this->city             = $this->property->city;
        $this->neighborhood     = $this->property->neighborhood;
        $this->surface_area     = $this->property->surface_area ? (float) $this->property->surface_area : null;
        $this->bedrooms         = $this->property->bedrooms;
        $this->bathrooms        = $this->property->bathrooms;
        $this->rent_amount      = (float) $this->property->rent_amount;
        $this->status           = $this->property->status->value;
    }

    public function openIncreaseModal(): void
    {
        $this->new_rent_amount   = (float) $this->property->rent_amount;
        $this->increase_reason   = '';
        $this->effective_date    = now()->format('Y-m-d');
        $this->update_active_lease = true;
        $this->showIncreaseModal = true;
    }

    public function closeIncreaseModal(): void
    {
        $this->showIncreaseModal = false;
    }

    public function increaseRent(): void
    {
        $this->authorize('update', $this->property);

        $this->validate([
            'new_rent_amount' => 'required|numeric|gt:0',
            'increase_reason' => 'required|string|min:3',
            'effective_date'  => 'required|date',
        ], [
            'new_rent_amount.required' => 'Le nouveau loyer est requis.',
            'new_rent_amount.gt'       => 'Le nouveau loyer doit être supérieur à 0.',
            'increase_reason.required' => 'Le motif de la révision/augmentation est obligatoire.',
            'increase_reason.min'      => 'Le motif doit comporter au moins 3 caractères.',
        ]);

        $oldRent = (float) $this->property->rent_amount;
        $newRent = (float) $this->new_rent_amount;
        $changeAmount = $newRent - $oldRent;

        $activeLease = Lease::where('property_id', $this->property->id)
            ->where('status', 'active')
            ->first();

        // Enregistrer l'historique
        RentHistory::create([
            'agency_id'       => $this->property->agency_id,
            'property_id'     => $this->property->id,
            'lease_id'        => $activeLease?->id,
            'old_rent_amount' => $oldRent,
            'new_rent_amount' => $newRent,
            'change_amount'   => $changeAmount,
            'reason'          => $this->increase_reason,
            'user_id'         => Auth::id(),
            'effective_date'  => $this->effective_date,
        ]);

        // Mettre à jour le loyer du bien
        $this->property->update([
            'rent_amount' => $newRent,
        ]);
        $this->rent_amount = $newRent;

        // Mettre à jour le bail actif le cas échéant
        if ($this->update_active_lease && $activeLease) {
            $activeLease->update([
                'rent_amount' => $newRent,
            ]);

            // Notification pour le locataire
            if ($activeLease->tenant_id) {
                SystemNotification::create([
                    'agency_id'      => $this->property->agency_id,
                    'recipient_type' => Tenant::class,
                    'recipient_id'   => $activeLease->tenant_id,
                    'type'           => 'rent_adjustment',
                    'channel'        => 'database',
                    'subject'        => "Révision du loyer — Bien {$this->property->title}",
                    'content'        => "Le loyer pour votre bien '{$this->property->title}' a été révisé à " . number_format($newRent, 0, ',', ' ') . " FCFA (Motif : {$this->increase_reason}). Date d'effet : {$this->effective_date}.",
                    'sent_at'        => now(),
                    'status'         => 'unread',
                ]);
            }
        }

        $this->showIncreaseModal = false;
        $this->property->load('rentHistories.user');

        session()->flash('success', "Le loyer du bien a été révisé avec succès (" . number_format($newRent, 0, ',', ' ') . " FCFA). Historique enregistré.");
    }

    public function save(): void
    {
        $this->authorize('update', $this->property);
        $this->validate();

        $this->property->update([
            'owner_id'         => $this->owner_id,
            'property_type_id' => $this->property_type_id,
            'title'            => $this->title,
            'description'      => $this->description,
            'address'          => $this->address,
            'city'             => $this->city,
            'neighborhood'     => $this->neighborhood,
            'surface_area'     => $this->surface_area,
            'bedrooms'         => $this->bedrooms,
            'bathrooms'        => $this->bathrooms,
            'rent_amount'      => $this->rent_amount,
            'status'           => $this->status,
        ]);

        session()->flash('success', "Le bien {$this->property->title} a été mis à jour.");

        $this->redirect(route('properties.index'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        $owners        = Owner::where('status', 'active')->orWhere('id', $this->owner_id)->orderBy('last_name')->get();
        $propertyTypes = PropertyType::where('status', 'active')->orWhere('id', $this->property_type_id)->orderBy('name')->get();
        $statusOptions = PropertyStatus::options();

        return view('livewire.properties.edit', compact('owners', 'propertyTypes', 'statusOptions'));
    }
}
