<?php

namespace App\Livewire\Admin\PropertyTypes;

use App\Domain\Property\Models\PropertyType;
use App\Livewire\Traits\WithDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public bool $showModal = false;
    public ?int $editingTypeId = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string')]
    public ?string $description = null;

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function openCreateModal(): void
    {
        $this->reset(['editingTypeId', 'name', 'description', 'status']);
        $this->status = 'active';
        $this->showModal = true;
    }

    public function openEditModal(int $typeId): void
    {
        $type = PropertyType::where('id', $typeId)->firstOrFail();

        $this->editingTypeId = $type->id;
        $this->name          = $type->name;
        $this->description   = $type->description;
        $this->status        = $type->status;
        $this->showModal     = true;
    }

    public function save(): void
    {
        $this->validate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($this->editingTypeId) {
            $type = PropertyType::where('id', $this->editingTypeId)->firstOrFail();
            $type->update([
                'name'        => $this->name,
                'description' => $this->description,
                'status'      => $this->status,
            ]);
            session()->flash('success', "Le type de bien {$type->name} a été mis à jour.");
        } else {
            $type = PropertyType::create([
                'name'        => $this->name,
                'description' => $this->description,
                'status'      => $this->status,
            ]);
            session()->flash('success', "Le type de bien {$type->name} a été créé.");
        }

        $this->showModal = false;
        $this->reset(['editingTypeId', 'name', 'description', 'status']);
    }

    public function render(): \Illuminate\View\View
    {
        $query = PropertyType::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'));

        $propertyTypes = $this->applySorting($query, 'name', 'asc')->paginate($this->perPage);

        return view('livewire.admin.property-types.index', compact('propertyTypes'));
    }
}
