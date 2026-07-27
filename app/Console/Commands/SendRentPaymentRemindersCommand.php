<?php

namespace App\Console\Commands;

use App\Domain\Notification\Models\SystemNotification;
use App\Domain\Rent\Models\RentSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendRentPaymentRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-rent-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrute les baux actifs et envoie des rappels de paiement de loyer 7d, 5d, 3d et 1d avant la date d\'échéance.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Début du balayage des échéances de loyers en attente...');

        $schedules = RentSchedule::with(['lease.property', 'lease.tenant', 'lease.agency'])
            ->whereIn('status', ['pending', 'partially_paid'])
            ->whereHas('lease', fn ($q) => $q->where('status', 'active'))
            ->get();

        $today = Carbon::today();
        $remindersCount = 0;

        foreach ($schedules as $schedule) {
            if (! $schedule->due_date) {
                continue;
            }

            $dueDate = $schedule->due_date->startOfDay();
            $daysUntilDue = (int) $today->diffInDays($dueDate, false);

            // Seuils de relance ciblés : 7 jours, 5 jours, 3 jours et 1 jour avant l'échéance
            if (! in_array($daysUntilDue, [7, 5, 3, 1], true)) {
                continue;
            }

            // Vérifier si un rappel a déjà été envoyé aujourd'hui pour ce seuil et cette échéance
            $alreadySent = SystemNotification::where('type', 'rent_reminder')
                ->where('created_at', '>=', $today)
                ->where('message', 'like', "%échéance #{$schedule->id}%")
                ->exists();

            if ($alreadySent) {
                continue;
            }

            $tenantName    = $schedule->lease?->tenant?->full_name ?? 'Locataire';
            $propertyTitle = $schedule->lease?->property?->title ?? 'Bien';
            $amountFormatted = number_format((float) $schedule->remaining_amount, 0, ',', ' ') . ' FCFA';
            $dueDateFormatted = $schedule->due_date->format('d/m/Y');

            $title = "Rappel de paiement loyer — J-{$daysUntilDue}";
            $message = "Rappel : Le loyer du bien {$propertyTitle} pour la période {$schedule->period} ({$amountFormatted}) arrive à échéance dans {$daysUntilDue} jour(s) le {$dueDateFormatted} (Échéance #{$schedule->id} pour {$tenantName}).";

            // Création de la notification système
            SystemNotification::create([
                'agency_id' => $schedule->lease?->agency_id,
                'title'     => $title,
                'message'   => $message,
                'type'      => 'rent_reminder',
                'status'    => 'unread',
            ]);

            $remindersCount++;
            $this->info("✔ Relance J-{$daysUntilDue} envoyée pour l'échéance #{$schedule->id} ({$tenantName} - {$propertyTitle})");
        }

        $this->info("Fin de la tâche de relance. Total relances envoyées : {$remindersCount}");

        return Command::SUCCESS;
    }
}
