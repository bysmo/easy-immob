<?php

namespace App\Livewire\Admin\LeaseTemplates;

use App\Domain\Lease\Models\LeaseTemplate;
use App\Domain\Lease\Services\DocxImporter;
use App\Domain\Lease\Services\TemplateVariableReplacer;
use App\Livewire\Traits\WithDataTable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithDataTable, WithFileUploads;

    public bool $showModal = false;
    public ?int $editingTemplateId = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|in:lease,management')]
    public string $type = 'lease';

    #[Validate('nullable|string')]
    public ?string $description = null;

    #[Validate('required|string')]
    public string $content = '';

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public string $typeFilter = '';

    // Fichier Word pour importation
    public $wordFile = null;

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingTemplateId', 'name', 'type', 'description', 'content', 'status', 'wordFile']);
        $this->type = 'lease';
        $this->status = 'active';
        $this->content = "<h1 class=\"text-xl font-bold\">CONTRAT DE LOCATION BIEN IMMOBILIER</h1><p>Entre <strong>{proprietaire_nom_complet}</strong> (Bailleur) et <strong>{locataire_nom_complet}</strong> (Locataire).</p><p>Adresse du bien : {bien_adresse}, {bien_ville}</p><p>Loyer mensuel : {loyer_montant} | Dépôt de garantie : {caution_montant}</p><p>Prise d'effet du bail : {date_debut} jusqu'à {date_fin}.</p>";
        $this->showModal = true;
    }

    public function openEditModal(int $templateId): void
    {
        $template = LeaseTemplate::where('id', $templateId)->firstOrFail();

        $this->editingTemplateId = $template->id;
        $this->name              = $template->name;
        $this->type              = $template->type ?? 'lease';
        $this->description       = $template->description;
        $this->content           = $template->content;
        $this->status            = $template->status;
        $this->wordFile          = null;
        $this->showModal         = true;
    }

    public function updatedWordFile(): void
    {
        $this->validate([
            'wordFile' => 'required|file|mimes:docx|max:10240', // Max 10MB docx
        ]);

        try {
            $importer = new DocxImporter();
            $path = $this->wordFile->getRealPath();
            $extractedHtml = $importer->convertDocxToHtml($path);

            $this->content = $extractedHtml;
            session()->flash('success_docx', 'Le fichier Word (.docx) a été importé avec succès. Les mises en forme et tableaux ont été convertis.');
        } catch (\Exception $e) {
            $this->addError('wordFile', "Erreur lors de l'importation du fichier Word : " . $e->getMessage());
        }
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingTemplateId) {
            $template = LeaseTemplate::where('id', $this->editingTemplateId)->firstOrFail();
            $template->update([
                'name'        => $this->name,
                'type'        => $this->type,
                'description' => $this->description,
                'content'     => $this->content,
                'status'      => $this->status,
                'version'     => $template->version + 1,
            ]);
            session()->flash('success', "Le modèle de contrat {$template->name} a été mis à jour.");
        } else {
            $template = LeaseTemplate::create([
                'name'        => $this->name,
                'type'        => $this->type,
                'description' => $this->description,
                'content'     => $this->content,
                'status'      => $this->status,
                'version'     => 1,
            ]);
            session()->flash('success', "Le modèle de contrat {$template->name} a été créé.");
        }

        $this->showModal = false;
        $this->reset(['editingTemplateId', 'name', 'type', 'description', 'content', 'status', 'wordFile']);
    }

    public function render(): \Illuminate\View\View
    {
        $query = LeaseTemplate::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter));

        $templates = $this->applySorting($query, 'name', 'asc')->paginate($this->perPage);

        return view('livewire.admin.lease-templates.index', [
            'templates' => $templates,
            'availableVariables' => TemplateVariableReplacer::getAvailableVariables(),
        ]);
    }
}
