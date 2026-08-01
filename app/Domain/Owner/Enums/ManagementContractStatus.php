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

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft      => 'badge-ghost',
            self::Active     => 'badge-success',
            self::Expired    => 'badge-warning',
            self::Terminated => 'badge-error',
        };
    }
}
