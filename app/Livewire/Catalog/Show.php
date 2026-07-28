<?php

namespace App\Livewire\Catalog;

use App\Domain\Lease\Models\Lease;
use App\Domain\Notification\Models\SystemNotification;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyChatMessage;
use App\Domain\Property\Models\PropertyInquiry;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Property $property;

    // Active photo index in gallery modal
    public int $activePhotoIndex = 0;

    // Modal pour lancer un chat / message initial
    public bool $showChatModal = false;
    public string $initialMessage = '';

    // Modal pour créer un brouillon de bail ("conclure un brouillon de bail")
    public bool $showDraftLeaseModal = false;
    public ?string $start_date = null;
    public int $duration_months = 12;
    public float $rent_amount = 0;
    public float $deposit_amount = 0;
    public ?int $selected_tenant_id = null;
    public string $draft_notes = '';

    public function mount(int $propertyId): void
    {
        $this->property = Property::with(['propertyType', 'owner', 'agency'])->where('id', $propertyId)->firstOrFail();
        $this->rent_amount    = (float) $this->property->rent_amount;
        $this->deposit_amount = (float) $this->property->rent_amount * 2;
        $this->start_date     = now()->addDays(7)->format('Y-m-d');
    }

    public function openChatModal(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user) {
            $existingInquiry = PropertyInquiry::where('property_id', $this->property->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingInquiry) {
                $this->redirect(route('inquiries.chat', $existingInquiry->id), navigate: false);
                return;
            }
        }

        $this->initialMessage = "Bonjour, je suis intéressé(e) par le bien '{$this->property->title}' ({$this->property->reference}). Merci de m'apporter plus de précisions sur les conditions d'entrée.";
        $this->showChatModal = true;
    }

    public function startChat(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->validate([
            'initialMessage' => 'required|string|min:3',
        ]);

        // Retrouver ou créer l'inquiry
        $tenant = Tenant::where('user_id', $user->id)->first();

        $inquiry = PropertyInquiry::firstOrCreate([
            'agency_id'   => $this->property->agency_id,
            'property_id' => $this->property->id,
            'user_id'     => $user->id,
        ], [
            'tenant_id' => $tenant?->id,
            'subject'   => "Demande d'information — {$this->property->title}",
            'status'    => 'open',
        ]);

        // Message initial
        PropertyChatMessage::create([
            'inquiry_id' => $inquiry->id,
            'user_id'    => $user->id,
            'message'    => $this->initialMessage,
            'is_agency'  => false,
        ]);

        // Notification pour l'agence
        SystemNotification::create([
            'agency_id'      => $this->property->agency_id,
            'recipient_type' => \App\Domain\Agency\Models\Agency::class,
            'recipient_id'   => $this->property->agency_id,
            'type'           => 'chat_message_received',
            'channel'        => 'database',
            'subject'        => "Nouveau message locataire — Bien {$this->property->title}",
            'content'        => "{$user->name} : \"{$this->initialMessage}\"",
            'sent_at'        => now(),
            'status'         => 'unread',
        ]);

        $this->showChatModal = false;
        session()->flash('success', "Votre message a été transmis à l'agence immobilière.");

        $this->redirect(route('inquiries.chat', $inquiry->id), navigate: false);
    }

    public function openDraftLeaseModal(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tenant = Tenant::where('user_id', $user->id)->first();
        if ($tenant) {
            $this->selected_tenant_id = $tenant->id;
        }

        $this->showDraftLeaseModal = true;
    }

    public function createDraftLease(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->validate([
            'start_date'         => 'required|date',
            'duration_months'    => 'required|integer|min:1',
            'rent_amount'        => 'required|numeric|gt:0',
            'deposit_amount'     => 'required|numeric|min:0',
            'selected_tenant_id' => 'required|exists:tenants,id',
        ], [
            'selected_tenant_id.required' => 'Un profil de locataire est nécessaire pour générer le contrat.',
        ]);

        $startDate = \Carbon\Carbon::parse($this->start_date);
        $endDate   = (clone $startDate)->addMonths($this->duration_months)->subDay();

        $reference = 'DFT-' . strtoupper(substr(uniqid(), -6));

        $lease = Lease::create([
            'agency_id'       => $this->property->agency_id,
            'reference'       => $reference,
            'property_id'     => $this->property->id,
            'tenant_id'       => $this->selected_tenant_id,
            'start_date'      => $startDate->format('Y-m-d'),
            'end_date'        => $endDate->format('Y-m-d'),
            'rent_amount'     => $this->rent_amount,
            'deposit_amount' => $this->deposit_amount,
            'payment_due_day' => 5,
            'status'          => 'draft',
        ]);

        // Mettre à jour les inquiries ouvertes s'il y en a
        PropertyInquiry::where('property_id', $this->property->id)
            ->where('user_id', $user->id)
            ->update(['status' => 'draft_lease_created']);

        // Notification pour l'agence
        SystemNotification::create([
            'agency_id'      => $this->property->agency_id,
            'recipient_type' => \App\Domain\Agency\Models\Agency::class,
            'recipient_id'   => $this->property->agency_id,
            'type'           => 'lease_draft_created',
            'channel'        => 'database',
            'subject'        => "Nouveau brouillon de contrat de bail — {$this->property->title}",
            'content'        => "Un brouillon de contrat de bail ({$lease->reference}) a été initié pour le bien '{$this->property->title}'.",
            'sent_at'        => now(),
            'status'         => 'unread',
        ]);

        $this->showDraftLeaseModal = false;
        session()->flash('success', "Le brouillon de contrat de bail ({$lease->reference}) a été créé avec succès.");

        $this->redirect(route('leases.show', $lease->id), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        $tenants = Tenant::where('agency_id', $this->property->agency_id)->orderBy('last_name')->get();

        return view('livewire.catalog.show', compact('tenants'));
    }
}
