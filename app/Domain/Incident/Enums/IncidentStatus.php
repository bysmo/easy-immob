<?php

namespace App\Domain\Incident\Enums;

enum IncidentStatus: string
{
    case Reported   = 'reported';
    case InProgress = 'in_progress';
    case Resolved   = 'resolved';
    case Closed     = 'closed';
    case Rejected   = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Reported   => 'Signalé',
            self::InProgress => 'En cours',
            self::Resolved   => 'Traité par l\'agence',
            self::Closed     => 'Clôturé & Confirmé',
            self::Rejected   => 'Rejeté',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Reported   => 'bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800',
            self::InProgress => 'bg-blue-100 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800',
            self::Resolved   => 'bg-indigo-100 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
            self::Closed     => 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
            self::Rejected   => 'bg-rose-100 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800',
        };
    }
}
