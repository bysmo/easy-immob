<?php

namespace App\Domain\Arrears\Enums;

enum ArrearSeverity: string
{
    case Warning  = 'warning';
    case Serious  = 'serious';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Warning  => 'Avertissement (J+1 à J+6)',
            self::Serious  => 'Sérieux (J+7 à J+14)',
            self::Critical => 'Critique (J+15+)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Warning  => 'amber',
            self::Serious  => 'amber',
            self::Critical => 'red',
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
