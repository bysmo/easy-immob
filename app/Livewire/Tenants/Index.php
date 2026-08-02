<?php

namespace App\Livewire\Tenants;

use App\Application\Imports\TenantImport;
use App\Application\Services\DynamicMailConfigurator;
use App\Application\Services\ReferenceGenerator;
use App\Domain\Tenant\Models\Tenant;
use App\Livewire\Traits\WithDataTable;
use App\Mail\TenantInvitationMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
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

    public function importTenants(ReferenceGenerator $generator): void
    {
        $this->authorize('create', Tenant::class);

        $this->validate([
            'importFile' => 'required|file|mimes:csv,xlsx,xls|max:5120',
        ], [
            'importFile.required' => 'Veuillez sélectionner un fichier.',
            'importFile.mimes'    => 'Le fichier doit être au format CSV ou Excel (.xlsx, .xls).',
            'importFile.max'      => 'Le fichier ne doit pas dépasser 5 Mo.',
        ]);

        $import = new TenantImport($generator);
        Excel::import($import, $this->importFile->getRealPath());

        $this->importErrors  = $import->errors;
        $this->importedCount = $import->importedCount;
        $this->importFile    = null;
    }

    public function delete(int $tenantId): void
    {
        $tenant = Tenant::where('id', $tenantId)->firstOrFail();

        if ($tenant->hasPortalAccess()) {
            session()->flash('error', "Un locataire possédant un compte portail ne peut pas être supprimé par l'agence.");
            return;
        }

        $this->authorize('delete', $tenant);

        $tenant->delete();

        session()->flash('success', "Le locataire {$tenant->full_name} a été supprimé.");
    }

    public function sendInvitation(int $tenantId): void
    {
        $tenant = Tenant::where('id', $tenantId)->firstOrFail();
        $this->authorize('update', $tenant);

        if (! $tenant->email) {
            session()->flash('error', "Ce locataire n'a pas d'adresse email.");
            return;
        }

        /** @var User $authUser */
        $authUser = Auth::user();

        $user = User::withoutGlobalScopes()
            ->where('email', mb_strtolower($tenant->email))
            ->first();

        if (! $user) {
            $user = User::create([
                'agency_id' => $tenant->agency_id,
                'name'      => $tenant->full_name,
                'email'     => mb_strtolower($tenant->email),
                'password'  => Hash::make(Str::random(32)),
            ]);
        }

        $user->assignRole('Locataire');
        $tenant->update(['user_id' => $user->id]);

        $signedUrl = URL::temporarySignedRoute(
            'tenant-portal.activate',
            now()->addHours(72),
            ['user' => $user->id],
        );

        DynamicMailConfigurator::apply($authUser?->agency);

        try {
            Mail::to($user->email)->send(
                new TenantInvitationMail($user, $tenant, $signedUrl, $authUser?->agency?->name ?? 'EasyImmob')
            );
            session()->flash('success', "L'invitation au portail locataire a été envoyée à {$tenant->email}.");
        } catch (\Throwable $e) {
            Log::error("Erreur lors de l'envoi du mail d'invitation locataire : " . $e->getMessage());
            session()->flash('error', "Impossible d'envoyer l'email d'invitation : " . $e->getMessage());
        }
    }

    public function render(): \Illuminate\View\View
    {
        $query = Tenant::with('user')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('reference', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            });

        $tenants = $this->applySorting($query, 'created_at', 'desc')->paginate($this->perPage);

        return view('livewire.tenants.index', compact('tenants'));
    }
}
