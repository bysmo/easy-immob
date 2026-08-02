<?php

namespace App\Mail;

use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email d'invitation envoyé au locataire après enregistrement de son compte.
 * Contient une Signed URL valable 72 h vers la page d'activation du portail locataire.
 */
class TenantInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [60, 300, 900];

    public function __construct(
        public User $user,
        public Tenant $tenant,
        public string $signedUrl,
        public string $agencyName,
    ) {}

    public function envelope(): Envelope
    {
        $agency = $this->tenant->agency_id
            ? \App\Domain\Agency\Models\Agency::find($this->tenant->agency_id)
            : null;

        \App\Application\Services\DynamicMailConfigurator::apply($agency);

        return new Envelope(
            subject: "Activez votre espace locataire — {$this->agencyName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tenants.invitation',
            with: [
                'tenantName' => $this->tenant->full_name,
                'agencyName' => $this->agencyName,
                'url'        => $this->signedUrl,
            ],
        );
    }
}
