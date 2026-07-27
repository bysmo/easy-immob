<?php

namespace App\Domain\Lease\Enums;

enum LeaseStatus: string
{
    case Draft            = 'draft';
    case PendingSignature = 'pending_signature';
    case Active           = 'active';
    case Expired          = 'expired';
    case Terminated       = 'terminated';
    case Cancelled        = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft            => 'Brouillon',
            self::PendingSignature => 'En attente de signature',
            self::Active           => 'Actif',
            self::Expired          => 'Expiré',
            self::Terminated       => 'Résilié',
            self::Cancelled        => 'Annulé',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Draft            => 'muted',
            self::PendingSignature => 'amber',
            self::Active           => 'green',
            self::Expired          => 'red',
            self::Terminated       => 'red',
            self::Cancelled        => 'muted',
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
