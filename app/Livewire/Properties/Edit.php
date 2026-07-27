<?php

namespace App\Livewire\Properties;

use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
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

    public function mount(int $propertyId): void
    {
        $this->property = Property::where('id', $propertyId)->first() ?? abort(404);
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
