<?php

namespace App\Livewire\Admin\LeaseTemplates;

use App\Domain\Lease\Models\LeaseTemplate;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    public bool $showModal = false;
    public ?int $editingTemplateId = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string')]
    public ?string $description = null;

    #[Validate('required|string')]
    public string $content = '';

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    public function openCreateModal(): void
    {
        $this->reset(['editingTemplateId', 'name', 'description', 'content', 'status']);
        $this->status  = 'active';
        $this->content = "CONTRAT DE LOCATION\n\nEntre {{owner_name}} (Bailleur) et {{tenant_name}} (Locataire).\nAdresse : {{property_address}}\nLoyer : {{rent_amount}} + Charges : {{charges_amount}}\nTotal : {{total_amount}}\nCaution : {{deposit_amount}}\nDu {{start_date}} au {{end_date}} (Paiement le {{payment_due_day}} du mois).";
        $this->showModal = true;
    }

    public function openEditModal(int $templateId): void
    {
        $template = LeaseTemplate::where('id', $templateId)->firstOrFail();

        $this->editingTemplateId = $template->id;
        $this->name              = $template->name;
        $this->description       = $template->description;
        $this->content           = $template->content;
        $this->status            = $template->status;
        $this->showModal         = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingTemplateId) {
            $template = LeaseTemplate::where('id', $this->editingTemplateId)->firstOrFail();
            $template->update([
                'name'        => $this->name,
                'description' => $this->description,
                'content'     => $this->content,
                'status'      => $this->status,
                'version'     => $template->version + 1,
            ]);
            session()->flash('success', "Le modèle de contrat {$template->name} a été mis à jour.");
        } else {
            $template = LeaseTemplate::create([
                'name'        => $this->name,
                'description' => $this->description,
                'content'     => $this->content,
                'status'      => $this->status,
                'version'     => 1,
            ]);
            session()->flash('success', "Le modèle de contrat {$template->name} a été créé.");
        }

        $this->showModal = false;
        $this->reset(['editingTemplateId', 'name', 'description', 'content', 'status']);
    }

    public function render(): \Illuminate\View\View
    {
        $templates = LeaseTemplate::latest()->get();

        return view('livewire.admin.lease-templates.index', compact('templates'));
    }
}
