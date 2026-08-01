<?php

namespace App\Livewire\Owners;

use App\Application\Imports\OwnerImport;
use App\Application\Services\ReferenceGenerator;
use App\Domain\Owner\Models\Owner;
use App\Livewire\Traits\WithDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithDataTable, WithFileUploads;

    public string $statusFilter = '';

    // ----- Import state -----
    public bool $showImportModal = false;
    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $importFile = null;
    /** @var array<int, array<string, mixed>> */
    public array $importErrors = [];
    public ?int $importedCount = null;

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openImportModal(): void
    {
        $this->reset(['importFile', 'importErrors', 'importedCount']);
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->reset(['showImportModal', 'importFile', 'importErrors', 'importedCount']);
    }

    public function importOwners(ReferenceGenerator $generator): void
    {
        $this->authorize('create', Owner::class);

        $this->validate([
            'importFile' => 'required|file|mimes:csv,xlsx,xls|max:5120',
        ], [
            'importFile.required' => 'Veuillez sélectionner un fichier.',
            'importFile.mimes'    => 'Le fichier doit être au format CSV ou Excel (.xlsx, .xls).',
            'importFile.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        $import = new OwnerImport($generator);
        Excel::import($import, $this->importFile->getRealPath());

        $this->importErrors    = $import->errors;
        $this->importedCount   = $import->importedCount;
        $this->importFile      = null;
    }

    public function delete(int $ownerId): void
    {
        $owner = Owner::where('id', $ownerId)->firstOrFail();
        $this->authorize('delete', $owner);

        $owner->delete();

        session()->flash('success', "Le bailleur {$owner->full_name} a été supprimé.");
    }

    public function render(): \Illuminate\View\View
    {
        $query = Owner::query()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('company_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('reference', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            });

        $owners = $this->applySorting($query, 'created_at', 'desc')->paginate($this->perPage);

        return view('livewire.owners.index', compact('owners'));
    }
}
