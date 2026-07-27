<?php

namespace App\Domain\Payment\Enums;

enum PaymentMethod: string
{
    case Cash         = 'cash';
    case BankTransfer = 'bank_transfer';
    case MobileMoney  = 'mobile_money';
    case Card         = 'card';
    case Check        = 'check';
    case Other        = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash         => 'Espèces',
            self::BankTransfer => 'Virement bancaire',
            self::MobileMoney  => 'Mobile Money (Orange/MTN/Wave)',
            self::Card         => 'Carte bancaire',
            self::Check        => 'Chèque',
            self::Other        => 'Autre',
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
