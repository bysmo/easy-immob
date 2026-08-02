<?php

namespace Tests\Feature;

use App\Application\Services\DynamicMailConfigurator;
use App\Domain\Subscription\Models\SaasSetting;
use App\Livewire\Admin\MailSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use Tests\TestCase;

class AgencyMailSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_super_admin_can_view_mail_settings_page(): void
    {
        $superAdmin = User::factory()->create(['agency_id' => null]);
        $superAdmin->assignRole('Super Admin');

        $response = $this->actingAs($superAdmin)->get(route('admin.mail-settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Configuration Mails SMTP');
    }

    public function test_regular_agency_user_cannot_access_mail_settings_page(): void
    {
        $agency = \App\Domain\Agency\Models\Agency::factory()->create();
        $agencyAdmin = User::factory()->create(['agency_id' => $agency->id]);
        $agencyAdmin->assignRole('Administrateur');

        $response = $this->actingAs($agencyAdmin)->get(route('admin.mail-settings.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_save_global_smtp_settings(): void
    {
        $superAdmin = User::factory()->create(['agency_id' => null]);
        $superAdmin->assignRole('Super Admin');

        Livewire::actingAs($superAdmin)
            ->test(MailSettings::class)
            ->set('mail_mailer', 'smtp')
            ->set('mail_host', 'smtp.sendgrid.net')
            ->set('mail_port', 587)
            ->set('mail_username', 'apikey')
            ->set('mail_password', 'SG.supersecret')
            ->set('mail_encryption', 'tls')
            ->set('mail_from_address', 'notifications@easyimmob.com')
            ->set('mail_from_name', 'EasyImmob SaaS Platform')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('smtp.sendgrid.net', SaasSetting::get('mail_host'));
        $this->assertEquals('587', SaasSetting::get('mail_port'));
        $this->assertEquals('apikey', SaasSetting::get('mail_username'));
        $this->assertEquals('SG.supersecret', SaasSetting::get('mail_password'));
        $this->assertEquals('notifications@easyimmob.com', SaasSetting::get('mail_from_address'));
        $this->assertEquals('EasyImmob SaaS Platform', SaasSetting::get('mail_from_name'));
    }

    public function test_dynamic_mail_configurator_applies_saas_global_settings(): void
    {
        SaasSetting::set('mail_mailer', 'smtp');
        SaasSetting::set('mail_host', 'smtp.global-saas.com');
        SaasSetting::set('mail_port', '465');
        SaasSetting::set('mail_username', 'saas_user');
        SaasSetting::set('mail_password', 'saas_pass');
        SaasSetting::set('mail_encryption', 'ssl');
        SaasSetting::set('mail_from_address', 'saas@global.com');
        SaasSetting::set('mail_from_name', 'SaaS Global System');

        DynamicMailConfigurator::apply();

        $this->assertEquals('smtp', Config::get('mail.default'));
        $this->assertEquals('smtp.global-saas.com', Config::get('mail.mailers.smtp.host'));
        $this->assertEquals(465, Config::get('mail.mailers.smtp.port'));
        $this->assertEquals('ssl', Config::get('mail.mailers.smtp.encryption'));
        $this->assertEquals('saas_user', Config::get('mail.mailers.smtp.username'));
        $this->assertEquals('saas_pass', Config::get('mail.mailers.smtp.password'));
        $this->assertEquals('saas@global.com', Config::get('mail.from.address'));
        $this->assertEquals('SaaS Global System', Config::get('mail.from.name'));
    }
}
