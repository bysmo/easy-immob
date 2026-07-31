<?php

namespace App\Domain\Owner\Enums;

enum OwnerPayoutStatus: string
{
    case Pending       = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Paid          = 'paid';
    case Cancelled     = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending       => 'À régler',
            self::PartiallyPaid => 'Partiellement réglé',
            self::Paid          => 'Réglé',
            self::Cancelled     => 'Annulé',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending       => 'amber',
            self::PartiallyPaid => 'indigo',
            self::Paid          => 'emerald',
            self::Cancelled     => 'rose',
        };
    }
}
