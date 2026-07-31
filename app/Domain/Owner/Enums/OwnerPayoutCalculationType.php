<?php

namespace App\Domain\Owner\Enums;

enum OwnerPayoutCalculationType: string
{
    case Collected = 'collected';
    case Expected  = 'expected';

    public function label(): string
    {
        return match ($this) {
            self::Collected => 'Loyers réellement encaissés',
            self::Expected  => 'Loyers attendus',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Collected => 'emerald',
            self::Expected  => 'amber',
        };
    }
}
