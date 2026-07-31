<?php

namespace App\Application\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Génère des références uniques par agence, par modèle et par préfixe.
 *
 * Format : PRE-XXXX (ex: PRO-0001, BIE-0042, LOC-0123)
 *
 * La génération est atomique : on lit le MAX existant et on incrémente
 * dans la même transaction pour éviter les doublons sous charge.
 */
class ReferenceGenerator
{
    /**
     * Génère et retourne une référence unique pour le modèle donné.
     *
     * @param  class-string<Model>  $modelClass
     */
    public function generate(string $modelClass, int $agencyId, string $prefix, int $padding = 4): string
    {
        return DB::transaction(function () use ($modelClass, $agencyId, $prefix, $padding): string {
            /** @var Model $instance */
            $instance = new $modelClass;

            $table = $instance->getTable();

            // Récupérer toutes les références existantes pour cette agence et ce préfixe
            $references = DB::table($table)
                ->where('agency_id', $agencyId)
                ->where('reference', 'like', "{$prefix}-%")
                ->lockForUpdate()
                ->pluck('reference');

            $nextNumber = 1;

            foreach ($references as $ref) {
                $parts = explode('-', $ref, 2);
                if (isset($parts[1]) && is_numeric($parts[1])) {
                    $num = (int) $parts[1];
                    if ($num >= $nextNumber) {
                        $nextNumber = $num + 1;
                    }
                }
            }

            return sprintf('%s-%0' . $padding . 'd', $prefix, $nextNumber);
        });
    }
}
