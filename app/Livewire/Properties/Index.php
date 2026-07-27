<?php

namespace App\Livewire\Properties;

use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Property\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

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
        $properties = Property::with(['owner', 'propertyType'])
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
            })
            ->latest()
            ->paginate(15);

        return view('livewire.properties.index', [
            'properties'    => $properties,
            'statusOptions' => PropertyStatus::options(),
        ]);
    }
}
