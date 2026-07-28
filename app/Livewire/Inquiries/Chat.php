<?php

namespace App\Livewire\Inquiries;

use App\Domain\Lease\Models\Lease;
use App\Domain\Notification\Models\SystemNotification;
use App\Domain\Property\Models\PropertyChatMessage;
use App\Domain\Property\Models\PropertyInquiry;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public PropertyInquiry $inquiry;
    public string $messageText = '';

    // Modal pour conclure le brouillon de bail depuis le chat
    public bool $showDraftLeaseModal = false;
    public ?string $start_date = null;
    public int $duration_months = 12;
    public float $rent_amount = 0;
    public float $deposit_amount = 0;
    public ?int $selected_tenant_id = null;

    public function mount(int $inquiryId): void
    {
        $this->inquiry = PropertyInquiry::with(['property', 'tenant', 'user', 'messages.user', 'agency'])
            ->where('id', $inquiryId)
            ->firstOrFail();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Sécurité : Vérifier que l'utilisateur est soit de l'agence, soit l'auteur de la demande
        if (!$user->isAgencyAdmin() && !$user->isSuperAdmin() && $this->inquiry->user_id !== $user->id) {
            abort(403, "Accès non autorisé à cette discussion.");
        }

        $this->rent_amount    = (float) $this->inquiry->property->rent_amount;
        $this->deposit_amount = (float) $this->inquiry->property->rent_amount * 2;
        $this->start_date     = now()->addDays(7)->format('Y-m-d');
        $this->selected_tenant_id = $this->inquiry->tenant_id;
    }

    public function sendMessage(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->validate([
            'messageText' => 'required|string|min:1',
        ]);

        $isAgency = $user->isAgencyAdmin() || $user->isSuperAdmin();

        PropertyChatMessage::create([
            'inquiry_id' => $this->inquiry->id,
            'user_id'    => $user->id,
            'message'    => $this->messageText,
            'is_agency'  => $isAgency,
        ]);

        $this->inquiry->touch();

        // Notification réciproque à chaque message
        if ($isAgency) {
            if ($this->inquiry->user_id) {
                SystemNotification::create([
                    'agency_id'      => $this->inquiry->agency_id,
                    'recipient_type' => \App\Models\User::class,
                    'recipient_id'   => $this->inquiry->user_id,
                    'type'           => 'chat_message_received',
                    'channel'        => 'database',
                    'subject'        => "Nouveau message de l'agence — Bien {$this->inquiry->property->title}",
                    'content'        => "L'agence vous a envoyé un message : \"{$this->messageText}\"",
                    'sent_at'        => now(),
                    'status'         => 'unread',
                ]);
            }

            if ($this->inquiry->tenant_id) {
                SystemNotification::create([
                    'agency_id'      => $this->inquiry->agency_id,
                    'recipient_type' => \App\Domain\Tenant\Models\Tenant::class,
                    'recipient_id'   => $this->inquiry->tenant_id,
                    'type'           => 'chat_message_received',
                    'channel'        => 'database',
                    'subject'        => "Nouveau message de l'agence — Bien {$this->inquiry->property->title}",
                    'content'        => "L'agence vous a envoyé un message : \"{$this->messageText}\"",
                    'sent_at'        => now(),
                    'status'         => 'unread',
                ]);
            }
        } else {
            SystemNotification::create([
                'agency_id'      => $this->inquiry->agency_id,
                'recipient_type' => \App\Domain\Agency\Models\Agency::class,
                'recipient_id'   => $this->inquiry->agency_id,
                'type'           => 'chat_message_received',
                'channel'        => 'database',
                'subject'        => "Nouveau message du locataire {$user->name} — Bien {$this->inquiry->property->title}",
                'content'        => "{$user->name} : \"{$this->messageText}\"",
                'sent_at'        => now(),
                'status'         => 'unread',
            ]);
        }

        $this->messageText = '';
        $this->inquiry->load('messages.user');
    }

    public function openDraftLeaseModal(): void
    {
        if (!$this->selected_tenant_id && $this->inquiry->user_id) {
            $t = Tenant::where('user_id', $this->inquiry->user_id)->first();
            if ($t) {
                $this->selected_tenant_id = $t->id;
            }
        }
        $this->showDraftLeaseModal = true;
    }

    public function createDraftLease(): void
    {
        $this->validate([
            'start_date'         => 'required|date',
            'duration_months'    => 'required|integer|min:1',
            'rent_amount'        => 'required|numeric|gt:0',
            'deposit_amount'     => 'required|numeric|min:0',
            'selected_tenant_id' => 'required|exists:tenants,id',
        ]);

        $startDate = \Carbon\Carbon::parse($this->start_date);
        $endDate   = (clone $startDate)->addMonths($this->duration_months)->subDay();

        $reference = 'DFT-' . strtoupper(substr(uniqid(), -6));

        $lease = Lease::create([
            'agency_id'       => $this->inquiry->agency_id,
            'reference'       => $reference,
            'property_id'     => $this->inquiry->property_id,
            'tenant_id'       => $this->selected_tenant_id,
            'start_date'      => $startDate->format('Y-m-d'),
            'end_date'        => $endDate->format('Y-m-d'),
            'rent_amount'     => $this->rent_amount,
            'deposit_amount' => $this->deposit_amount,
            'payment_due_day' => 5,
            'status'          => 'draft',
        ]);

        $this->inquiry->update(['status' => 'draft_lease_created']);

        // Message automatique dans le chat
        PropertyChatMessage::create([
            'inquiry_id' => $this->inquiry->id,
            'user_id'    => Auth::id(),
            'message'    => "📄 Un brouillon de contrat de bail ({$lease->reference}) a été établi pour ce bien.",
            'is_agency'  => true,
        ]);

        $this->showDraftLeaseModal = false;
        session()->flash('success', "Le brouillon de contrat de bail ({$lease->reference}) a été généré.");

        $this->redirect(route('leases.show', $lease->id), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        $this->inquiry->load('messages.user');
        $tenants = Tenant::where('agency_id', $this->inquiry->agency_id)->orderBy('last_name')->get();

        return view('livewire.inquiries.chat', compact('tenants'));
    }
}
