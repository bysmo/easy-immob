<?php

namespace Tests\Feature;

use App\Domain\Agency\Models\Agency;
use App\Domain\Owner\Models\Owner;
use App\Livewire\Owners\Edit;
use App\Livewire\Owners\Index;
use App\Mail\OwnerInvitationMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class OwnerInvitationMailTest extends TestCase
{
    use RefreshDatabase;

    protected Agency $agency;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->agency = Agency::factory()->create(['name' => 'Immo Excellence']);
        $this->adminUser = User::factory()->create([
            'agency_id' => $this->agency->id,
        ]);
        $this->adminUser->assignRole('Administrateur');
    }

    public function test_can_send_invitation_mail_from_owner_index(): void
    {
        Mail::fake();

        $owner = Owner::factory()->create([
            'agency_id' => $this->agency->id,
            'email'     => 'bailleur.test@example.com',
            'user_id'   => null,
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(\App\Livewire\Owners\Index::class)
            ->call('sendInvitation', $owner->id)
            ->assertHasNoErrors()
            ->assertSee("L'invitation au portail bailleur a été envoyée à bailleur.test@example.com");

        Mail::assertSent(OwnerInvitationMail::class, function (OwnerInvitationMail $mail) use ($owner) {
            return $mail->hasTo('bailleur.test@example.com') &&
                $mail->owner->id === $owner->id &&
                str_contains($mail->signedUrl, 'owner-portal/activate');
        });

        $createdUser = User::withoutGlobalScopes()->where('email', 'bailleur.test@example.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertEquals($this->agency->id, $createdUser->agency_id);
        $this->assertTrue($createdUser->hasRole('Bailleur'));
    }

    public function test_can_resend_invitation_mail_from_owner_edit(): void
    {
        Mail::fake();

        $owner = Owner::factory()->create([
            'agency_id' => $this->agency->id,
            'email'     => 'bailleur.renvoi@example.com',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(Edit::class, ['ownerId' => $owner->id])
            ->call('sendInvitation')
            ->assertHasNoErrors()
            ->assertSee("Invitation envoyée à bailleur.renvoi@example.com");

        Mail::assertSent(OwnerInvitationMail::class, function (OwnerInvitationMail $mail) {
            return $mail->hasTo('bailleur.renvoi@example.com');
        });
    }

    public function test_owner_and_tenant_invitation_mails_render_correct_logo_and_french_footer(): void
    {
        $owner = Owner::factory()->create(['agency_id' => $this->agency->id]);
        $ownerMail = new OwnerInvitationMail($this->adminUser, $owner, 'http://localhost/activate', $this->agency->name);
        $renderedOwnerMail = $ownerMail->render();

        $this->assertStringContainsString('images/logo.png', $renderedOwnerMail);
        $this->assertStringContainsString('Tous droits réservés', $renderedOwnerMail);
        $this->assertStringContainsString('EasyImmob', $renderedOwnerMail);
        $this->assertStringNotContainsString('Laravel. All rights reserved', $renderedOwnerMail);

        $tenant = \App\Domain\Tenant\Models\Tenant::factory()->create(['agency_id' => $this->agency->id]);
        $tenantMail = new \App\Mail\TenantInvitationMail($this->adminUser, $tenant, 'http://localhost/tenant-activate', $this->agency->name);
        $renderedTenantMail = $tenantMail->render();

        $this->assertStringContainsString('images/logo.png', $renderedTenantMail);
        $this->assertStringContainsString('Tous droits réservés', $renderedTenantMail);
        $this->assertStringContainsString('EasyImmob', $renderedTenantMail);
        $this->assertStringNotContainsString('Laravel. All rights reserved', $renderedTenantMail);
    }

    public function test_password_reset_mail_is_in_french_with_logo_and_footer(): void
    {
        $user = User::factory()->create(['email' => 'user.reset@example.com']);
        $notification = new \Illuminate\Auth\Notifications\ResetPassword('token-123456');
        $mailMessage = $notification->toMail($user);
        $renderedMail = $mailMessage->render();

        $this->assertEquals('Réinitialisation de votre mot de passe - EasyImmob', $mailMessage->subject);
        $this->assertStringContainsString('Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation', $renderedMail);
        $this->assertStringContainsString('Réinitialiser mon mot de passe', $renderedMail);
        $this->assertStringContainsString('images/logo.png', $renderedMail);
        $this->assertStringContainsString('Tous droits réservés', $renderedMail);
    }
}
