<?php

namespace App\Livewire\Properties;

use App\Application\Services\ReferenceGenerator;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
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
    public float $rent_amount = 150000;

    #[Validate('required')]
    public string $status = 'available';

    public function save(ReferenceGenerator $generator): void
    {
        $this->authorize('create', Property::class);
        $this->validate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $reference = $generator->generate(Property::class, $user->agency_id, 'BIE');

        $property = Property::create([
            'reference'        => $reference,
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

        session()->flash('success', "Le bien {$property->title} a été créé avec la référence {$property->reference}.");

        $this->redirect(route('properties.index'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        $owners        = Owner::where('status', 'active')->orderBy('last_name')->get();
        $propertyTypes = PropertyType::where('status', 'active')->orderBy('name')->get();
        $statusOptions = PropertyStatus::options();

        return view('livewire.properties.create', compact('owners', 'propertyTypes', 'statusOptions'));
    }
}
