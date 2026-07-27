<?php

namespace App\Domain\Lease\Services;

use App\Domain\Lease\Models\Lease;

class LeaseContractGenerator
{
    public function generate(Lease $lease, string $templateContent): string
    {
        $replacer = new TemplateVariableReplacer();
        $content = $replacer->replaceForLease($templateContent, $lease);

        // Support rétro-compatible pour les balises {{tenant_name}} etc.
        $lease->loadMissing(['tenant', 'property.owner']);
        $legacyReplacements = [
            '{{tenant_name}}'      => $lease->tenant?->full_name ?? '',
            '{{owner_name}}'       => $lease->property?->owner?->full_name ?? '',
            '{{property_address}}' => trim(($lease->property?->address ?? '') . ', ' . ($lease->property?->city ?? '')),
            '{{rent_amount}}'      => number_format((float) $lease->rent_amount, 0, ',', ' ') . ' FCFA',
            '{{charges_amount}}'   => number_format((float) ($lease->charges_amount ?? 0), 0, ',', ' ') . ' FCFA',
            '{{total_amount}}'     => number_format((float) ($lease->rent_amount + ($lease->charges_amount ?? 0)), 0, ',', ' ') . ' FCFA',
            '{{deposit_amount}}'   => number_format((float) $lease->deposit_amount, 0, ',', ' ') . ' FCFA',
            '{{start_date}}'       => $lease->start_date?->format('d/m/Y') ?? '',
            '{{end_date}}'         => $lease->end_date?->format('d/m/Y') ?? '',
            '{{lease_reference}}'  => $lease->reference,
        ];

        return str_replace(array_keys($legacyReplacements), array_values($legacyReplacements), $content);
    }
}
