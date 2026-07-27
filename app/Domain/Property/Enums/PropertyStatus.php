<?php

namespace App\Domain\Property\Enums;

enum PropertyStatus: string
{
    case Available   = 'available';
    case Occupied    = 'occupied';
    case Maintenance = 'maintenance';
    case Inactive    = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Available   => 'Disponible',
            self::Occupied    => 'Occupé',
            self::Maintenance => 'Maintenance',
            self::Inactive    => 'Inactif',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Available   => 'green',
            self::Occupied    => 'amber',
            self::Maintenance => 'red',
            self::Inactive    => 'muted',
        };
    }

    /** @return list<array{value: string, label: string}> */
    public static function options(): array
    {
        return array_map(
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
