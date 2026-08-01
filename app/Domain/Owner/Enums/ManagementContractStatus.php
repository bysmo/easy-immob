<?php

namespace App\Domain\Owner\Enums;

enum ManagementContractStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Expired = 'expired';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::Draft      => 'Brouillon',
            self::Active     => 'Actif',
            self::Expired    => 'Expiré',
            self::Terminated => 'Résilié',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Draft      => 'muted',
            self::Active     => 'green',
            self::Expired    => 'amber',
            self::Terminated => 'red',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft      => 'badge-ghost',
            self::Active     => 'badge-success',
            self::Expired    => 'badge-warning',
            self::Terminated => 'badge-error',
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
