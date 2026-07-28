<?php

namespace Tests\Unit\Support\Navigation;

use App\Support\Navigation\SidebarMenu;
use Tests\TestCase;

class SidebarMenuTest extends TestCase
{
    public function test_it_returns_exactly_fourteen_entries(): void
    {
        $this->assertCount(16, SidebarMenu::items());
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
                'catalog.index' => 1,
                'inquiries.index' => 1,
                'owners.index' => 1,
                'properties.index' => 1,
                'tenants.index' => 1,
                'leases.index' => 1,
                'incidents.index' => 1,
                'rents.index' => 1,
                'deposits.index' => 1,
                'arrears.index' => 1,
                'notifications.index' => 1,
                'reports.index' => 1,
                'admin.lease-templates.index' => 1,
                'admin.property-types.index' => 1,
                'admin.users.index' => 1,
            ],
            $counts
        );
    }
}
