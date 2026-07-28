<?php

namespace Tests\Feature;

use App\Domain\Agency\Models\Agency;
use App\Domain\Lease\Models\Lease;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyChatMessage;
use App\Domain\Property\Models\PropertyInquiry;
use App\Domain\Property\Models\PropertyType;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyCatalogAndChatTest extends TestCase
{
    use RefreshDatabase;

    protected Agency $agency;
    protected User $adminUser;
    protected User $tenantUser;
    protected Tenant $tenant;
    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->agency = Agency::create([
            'name'  => 'Agence Immobilière Test',
            'email' => 'contact@agency.com',
        ]);

        $this->adminUser = User::create([
            'agency_id' => $this->agency->id,
            'name'      => 'Gestionnaire Agence',
            'email'     => 'admin@agency.com',
            'password'  => bcrypt('password'),
        ]);
        $this->adminUser->assignRole('Administrateur');

        $this->tenantUser = User::create([
            'agency_id' => $this->agency->id,
            'name'      => 'Locataire Jean',
            'email'     => 'tenant@agency.com',
            'password'  => bcrypt('password'),
        ]);
        $this->tenantUser->assignRole('Locataire');

        $this->tenant = Tenant::create([
            'agency_id'  => $this->agency->id,
            'user_id'    => $this->tenantUser->id,
            'reference'  => 'TEN-0001',
            'first_name' => 'Jean',
            'last_name'  => 'Kouassi',
            'phone'      => '+225070000000',
        ]);

        $owner = Owner::create([
            'agency_id'  => $this->agency->id,
            'reference'  => 'OWN-0001',
            'first_name' => 'Paul',
            'last_name'  => 'Diallo',
        ]);

        $propertyType = PropertyType::create([
            'agency_id' => $this->agency->id,
            'name'      => 'Maison Duplex',
        ]);

        $this->property = Property::create([
            'agency_id'        => $this->agency->id,
            'owner_id'         => $owner->id,
            'property_type_id' => $propertyType->id,
            'reference'        => 'BIE-0001',
            'title'            => 'Superbe Villa avec Jardin',
            'address'          => 'Rue des Alizés',
            'city'             => 'Abidjan',
            'neighborhood'     => 'Cocody',
            'latitude'         => 5.359951,
            'longitude'        => -4.008256,
            'google_maps_url'  => 'https://maps.google.com/?q=5.359951,-4.008256',
            'surface_area'     => 200,
            'bedrooms'         => 4,
            'bathrooms'        => 3,
            'rent_amount'      => 350000,
            'photos'           => ['https://example.com/photo1.jpg', 'https://example.com/photo2.jpg'],
            'videos'           => ['https://youtube.com/watch?v=12345'],
            'status'           => 'available',
        ]);
    }

    public function test_tenant_can_search_properties_with_adjustable_criteria(): void
    {
        $this->actingAs($this->tenantUser);

        Livewire::test(\App\Livewire\Catalog\Index::class)
            ->set('search', 'Superbe')
            ->set('min_price', 200000)
            ->set('max_price', 500000)
            ->set('min_bedrooms', 3)
            ->assertSee('Superbe Villa avec Jardin')
            ->assertSee('350 000');
    }

    public function test_agency_cannot_add_more_than_3_videos_per_property(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(\App\Livewire\Properties\Create::class)
            ->set('videos', [
                'https://youtube.com/v1',
                'https://youtube.com/v2',
                'https://youtube.com/v3',
            ])
            ->set('new_video_url', 'https://youtube.com/v4')
            ->call('addVideoUrl')
            ->assertHasErrors(['new_video_url']);
    }

    public function test_agency_cannot_add_more_than_10_photos_per_property(): void
    {
        $this->actingAs($this->adminUser);

        Livewire::test(\App\Livewire\Properties\Create::class)
            ->set('photos', array_fill(0, 10, 'https://example.com/photo.jpg'))
            ->set('new_photo_url', 'https://example.com/photo11.jpg')
            ->call('addPhotoUrl')
            ->assertHasErrors(['new_photo_url']);
    }

    public function test_tenant_can_view_property_details_and_start_chat_with_agency(): void
    {
        $this->actingAs($this->tenantUser);

        Livewire::test(\App\Livewire\Catalog\Show::class, ['propertyId' => $this->property->id])
            ->assertSee('Superbe Villa avec Jardin')
            ->assertSee('Vidéos de présentation')
            ->set('initialMessage', 'Je souhaite visiter ce bien samedi prochain.')
            ->call('startChat');

        $this->assertDatabaseHas('property_inquiries', [
            'agency_id'   => $this->agency->id,
            'property_id' => $this->property->id,
            'user_id'     => $this->tenantUser->id,
        ]);

        $this->assertDatabaseHas('property_chat_messages', [
            'message' => 'Je souhaite visiter ce bien samedi prochain.',
        ]);
    }

    public function test_agency_and_tenant_can_conclude_draft_lease_agreement(): void
    {
        $this->actingAs($this->tenantUser);

        $inquiry = PropertyInquiry::create([
            'agency_id'   => $this->agency->id,
            'property_id' => $this->property->id,
            'tenant_id'   => $this->tenant->id,
            'user_id'     => $this->tenantUser->id,
            'subject'     => 'Demande de location',
            'status'      => 'open',
        ]);

        Livewire::test(\App\Livewire\Inquiries\Chat::class, ['inquiryId' => $inquiry->id])
            ->set('selected_tenant_id', $this->tenant->id)
            ->set('start_date', '2026-08-01')
            ->set('duration_months', 12)
            ->set('rent_amount', 350000)
            ->set('deposit_amount', 700000)
            ->call('createDraftLease');

        $this->assertDatabaseHas('leases', [
            'agency_id'   => $this->agency->id,
            'property_id' => $this->property->id,
            'tenant_id'   => $this->tenant->id,
            'rent_amount' => 350000,
            'status'      => 'draft',
        ]);
    }
}
