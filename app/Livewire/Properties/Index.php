<?php

namespace App\Livewire\Properties;

use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Property\Models\Property;
use App\Livewire\Traits\WithDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public string $statusFilter = '';

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function delete(int $propertyId): void
    {
        $property = Property::where('id', $propertyId)->firstOrFail();
        $this->authorize('delete', $property);

        $property->delete();

        session()->flash('success', "Le bien {$property->title} a été supprimé.");
    }

    public function render(): \Illuminate\View\View
    {
        $query = Property::with(['owner', 'propertyType'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('reference', 'like', '%' . $this->search . '%')
                        ->orWhere('city', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            });

        $properties = $this->applySorting($query, 'created_at', 'desc')->paginate($this->perPage);

        return view('livewire.properties.index', [
            'properties'    => $properties,
            'statusOptions' => PropertyStatus::options(),
        ]);
    }
}
