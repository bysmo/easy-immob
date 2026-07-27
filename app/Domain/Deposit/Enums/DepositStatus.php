<?php

namespace App\Domain\Deposit\Enums;

enum DepositStatus: string
{
    case Pending           = 'pending';
    case Held              = 'held';
    case PartiallyRefunded = 'partially_refunded';
    case Refunded          = 'refunded';
    case Forfeited         = 'forfeited';

    public function label(): string
    {
        return match ($this) {
            self::Pending           => 'Attente de réception',
            self::Held              => 'Caution reçue & conservée',
            self::PartiallyRefunded => 'Partiellement restituée',
            self::Refunded          => 'Totalement restituée',
            self::Forfeited         => 'Retenue intégrale (confisquée)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending           => 'amber',
            self::Held              => 'green',
            self::PartiallyRefunded => 'amber',
            self::Refunded          => 'muted',
            self::Forfeited         => 'red',
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
