<?php

namespace App\Livewire\Catalog;

use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $property_type_id = null;
    public string $city = '';
    public ?float $min_price = null;
    public ?float $max_price = null;
    public ?float $min_surface = null;
    public ?float $max_surface = null;
    public ?int $min_bedrooms = null;
    public ?int $min_bathrooms = null;
    public string $status = 'available';

    protected $queryString = [
        'search'           => ['except' => ''],
        'property_type_id' => ['except' => null],
        'city'             => ['except' => ''],
        'min_price'        => ['except' => null],
        'max_price'        => ['except' => null],
        'min_bedrooms'     => ['except' => null],
        'status'           => ['except' => 'available'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'property_type_id',
            'city',
            'min_price',
            'max_price',
            'min_surface',
            'max_surface',
            'min_bedrooms',
            'min_bathrooms',
        ]);
        $this->status = 'available';
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $query = Property::with(['propertyType', 'owner'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%')
                        ->orWhere('city', 'like', '%' . $this->search . '%')
                        ->orWhere('neighborhood', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->property_type_id, fn ($q) => $q->where('property_type_id', $this->property_type_id))
            ->when($this->city, fn ($q) => $q->where('city', 'like', '%' . $this->city . '%'))
            ->when($this->min_price, fn ($q) => $q->where('rent_amount', '>=', $this->min_price))
            ->when($this->max_price, fn ($q) => $q->where('rent_amount', '<=', $this->max_price))
            ->when($this->min_surface, fn ($q) => $q->where('surface_area', '>=', $this->min_surface))
            ->when($this->max_surface, fn ($q) => $q->where('surface_area', '<=', $this->max_surface))
            ->when($this->min_bedrooms, fn ($q) => $q->where('bedrooms', '>=', $this->min_bedrooms))
            ->when($this->min_bathrooms, fn ($q) => $q->where('bathrooms', '>=', $this->min_bathrooms))
            ->when($this->status !== 'all', fn ($q) => $q->where('status', $this->status));

        $properties    = $query->latest()->paginate(9);
        $propertyTypes = PropertyType::orderBy('name')->get();
        $cities        = Property::select('city')->distinct()->pluck('city')->filter()->values();

        return view('livewire.catalog.index', compact('properties', 'propertyTypes', 'cities'));
    }
}
