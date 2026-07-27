<?php

namespace App\Domain\Rent\Enums;

enum RentScheduleStatus: string
{
    case Pending       = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Paid          = 'paid';
    case Overdue       = 'overdue';
    case Cancelled     = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending       => 'En attente',
            self::PartiallyPaid => 'Partiellement payé',
            self::Paid          => 'Payé',
            self::Overdue       => 'En retard',
            self::Cancelled     => 'Annulé',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending       => 'amber',
            self::PartiallyPaid => 'amber',
            self::Paid          => 'green',
            self::Overdue       => 'red',
            self::Cancelled     => 'muted',
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
