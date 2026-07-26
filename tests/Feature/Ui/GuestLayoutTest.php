<?php

namespace Tests\Feature\Ui;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class GuestLayoutTest extends TestCase
{
    public function test_guest_layout_renders_the_agency_name_and_slot(): void
    {
        $html = Blade::render(
            '<x-layouts.guest>Contenu de test</x-layouts.guest>'
        );

        $this->assertStringContainsString('EasyImmob', $html);
        $this->assertStringContainsString('Contenu de test', $html);
    }

    public function test_button_component_renders_its_slot(): void
    {
        $html = Blade::render('<x-button>Valider</x-button>');

        $this->assertStringContainsString('Valider', $html);
        $this->assertStringContainsString('<button', $html);
    }
}
