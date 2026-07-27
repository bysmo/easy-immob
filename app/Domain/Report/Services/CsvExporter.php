<?php

namespace App\Domain\Report\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    /**
     * Exporte un tableau de données au format CSV en réponse HTTP Streamed.
     *
     * @param array<int, string> $headers
     * @param array<int, array<string, mixed>> $data
     */
    public static function download(string $filename, array $headers, array $data): StreamedResponse
    {
        return new StreamedResponse(function () use ($headers, $data): void {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM pour Excel
            fputs($handle, "\xEF\xBB\xBF");

            // Ligne d'en-tête
            fputcsv($handle, $headers, ';');

            // Lignes de données
            foreach ($data as $row) {
                fputcsv($handle, array_values($row), ';');
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
