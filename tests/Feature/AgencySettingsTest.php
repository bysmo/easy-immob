<?php

namespace Tests\Feature;

use App\Domain\Agency\Models\Agency;
use App\Livewire\Agency\Settings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AgencySettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_agency_admin_can_view_settings_page(): void
    {
        $agency = Agency::factory()->create([
            'name' => 'Agence Immobilière Horizon',
        ]);

        $user = User::factory()->create([
            'agency_id' => $agency->id,
        ]);

        $response = $this->actingAs($user)->get(route('agency.settings'));

        $response->assertStatus(200);
        $response->assertSee('Informations');
        $response->assertSee('Agence Immobilière Horizon');
    }

    public function test_can_update_agency_information_tva_and_commission(): void
    {
        $agency = Agency::factory()->create([
            'name' => 'Ancien Nom',
            'legal_name' => 'Ancienne Sté',
            'commission_rate' => 10.0,
            'is_subject_to_tva' => false,
            'tva_rate' => 0.0,
        ]);

        $user = User::factory()->create([
            'agency_id' => $agency->id,
        ]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('name', 'Nouvel Horizon Immo')
            ->set('legal_name', 'Nouvel Horizon SARL')
            ->set('email', 'contact@horizon.ci')
            ->set('phone', '+225 27 22 00 00 00')
            ->set('address', 'Cocody Les Deux Plateaux')
            ->set('nif_rccm', 'RCCM CI-ABJ-2026-B-1234')
            ->set('commission_rate', 12.5)
            ->set('is_subject_to_tva', true)
            ->set('tva_rate', 18.0)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Les informations et paramètres de votre agence ont été enregistrés avec succès !');

        $agency->refresh();

        $this->assertEquals('Nouvel Horizon Immo', $agency->name);
        $this->assertEquals('Nouvel Horizon SARL', $agency->legal_name);
        $this->assertEquals('contact@horizon.ci', $agency->email);
        $this->assertEquals('RCCM CI-ABJ-2026-B-1234', $agency->nif_rccm);
        $this->assertEquals(12.5, $agency->commission_rate);
        $this->assertTrue($agency->is_subject_to_tva);
        $this->assertEquals(18.0, $agency->tva_rate);
    }

    public function test_can_upload_and_remove_agency_logo(): void
    {
        Storage::fake('public');

        $agency = Agency::factory()->create();

        $user = User::factory()->create([
            'agency_id' => $agency->id,
        ]);

        $file = UploadedFile::fake()->image('logo.png', 300, 300);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('logo', $file)
            ->call('save')
            ->assertHasNoErrors();

        $agency->refresh();

        $this->assertNotNull($agency->logo_path);
        Storage::disk('public')->assertExists($agency->logo_path);
        $this->assertNotNull($agency->logo_url);

        // Test de suppression du logo
        Livewire::actingAs($user)
            ->test(Settings::class)
            ->call('removeLogo')
            ->assertHasNoErrors();

        $agency->refresh();

        $this->assertNull($agency->logo_path);
        $this->assertNull($agency->logo_url);
    }
}
