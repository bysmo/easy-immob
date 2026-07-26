<?php

namespace Tests\Unit\Support\Navigation;

use App\Support\Navigation\SidebarMenu;
use PHPUnit\Framework\TestCase;

class SidebarMenuTest extends TestCase
{
    public function test_it_returns_exactly_eleven_entries(): void
    {
        $this->assertCount(11, SidebarMenu::items());
    }

    public function test_each_entry_has_the_expected_keys(): void
    {
        foreach (SidebarMenu::items() as $item) {
            $this->assertSame(
                ['label', 'icon', 'route', 'params'],
                array_keys($item)
            );
        }
    }

    public function test_it_only_references_the_expected_route_names(): void
    {
        $routes = array_map(static fn (array $item) => $item['route'], SidebarMenu::items());

        $counts = array_count_values($routes);

        $this->assertSame(
            [
                'dashboard' => 1,
                'modules.coming-soon' => 9,
                'admin.users.index' => 1,
            ],
            $counts
        );
    }
}
