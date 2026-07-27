<?php

namespace App\Domain\Arrears\Enums;

enum ArrearStatus: string
{
    case Open      = 'open';
    case Settled   = 'settled';
    case Escalated = 'escalated';

    public function label(): string
    {
        return match ($this) {
            self::Open      => 'En cours',
            self::Settled   => 'Réglé (Clôturé)',
            self::Escalated => 'Escaladé (Juridique/Agence)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Open      => 'red',
            self::Settled   => 'green',
            self::Escalated => 'amber',
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
