<?php

namespace App\Console\Commands;

use App\Domain\Arrears\Services\ArrearDetector;
use Illuminate\Console\Command;

class DetectArrearsCommand extends Command
{
    protected $signature = 'immob:detect-arrears';

    protected $description = 'Détecte les loyers impayés et met à jour les dossiers de recouvrement.';

    public function handle(ArrearDetector $detector): int
    {
        $this->info('Début de la détection des impayés...');

        $count = $detector->detect();

        $this->info("Détection terminée. {$count} dossier(s) d'impayé(s) traité(s).");

        return Command::SUCCESS;
    }
}
