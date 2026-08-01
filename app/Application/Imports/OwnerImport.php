<?php

namespace App\Application\Imports;

use App\Application\Services\ReferenceGenerator;
use App\Domain\Owner\Models\Owner;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class OwnerImport implements ToCollection, WithHeadingRow
{
    /** @var array<int, array<string, mixed>> */
    public array $errors = [];

    /** @var int */
    public int $importedCount = 0;

    private ReferenceGenerator $generator;
    private int $agencyId;

    public function __construct(ReferenceGenerator $generator)
    {
        $this->generator = $generator;

        /** @var \App\Models\User $user */
        $user           = Auth::user();
        $this->agencyId = $user->agency_id;
    }

    public function collection(Collection $rows): void
    {
        $rowNumber = 2; // Row 1 = headings

        foreach ($rows as $row) {
            $data = [
                'prenom'   => trim((string) ($row['prenom'] ?? $row['prénom'] ?? '')),
                'nom'      => trim((string) ($row['nom'] ?? '')),
                'societe'  => trim((string) ($row['societe'] ?? $row['société'] ?? '')),
                'email'    => trim((string) ($row['email'] ?? '')),
                'telephone'=> trim((string) ($row['telephone'] ?? $row['téléphone'] ?? '')),
                'adresse'  => trim((string) ($row['adresse'] ?? '')),
                'statut'   => strtolower(trim((string) ($row['statut'] ?? 'active'))),
            ];

            // Normalize statut
            if (in_array($data['statut'], ['actif', 'activ', '1', 'oui', 'yes', 'active'])) {
                $data['statut'] = 'active';
            } else {
                $data['statut'] = 'inactive';
            }

            $validator = Validator::make($data, [
                'prenom'    => 'required|string|max:255',
                'nom'       => 'required|string|max:255',
                'societe'   => 'nullable|string|max:255',
                'email'     => 'nullable|email|max:255',
                'telephone' => 'nullable|string|max:50',
                'adresse'   => 'nullable|string|max:500',
                'statut'    => 'required|in:active,inactive',
            ], [
                'prenom.required'   => 'Le prénom est obligatoire.',
                'nom.required'      => 'Le nom est obligatoire.',
                'email.email'       => 'L\'adresse email est invalide.',
            ]);

            if ($validator->fails()) {
                $this->errors[] = [
                    'row'    => $rowNumber,
                    'name'   => trim("{$data['nom']} {$data['prenom']}") ?: "Ligne {$rowNumber}",
                    'errors' => $validator->errors()->all(),
                ];
                $rowNumber++;
                continue;
            }

            try {
                $reference = $this->generator->generate(Owner::class, $this->agencyId, 'PRO');

                Owner::create([
                    'agency_id'    => $this->agencyId,
                    'reference'    => $reference,
                    'first_name'   => $data['prenom'],
                    'last_name'    => $data['nom'],
                    'company_name' => $data['societe'] ?: null,
                    'email'        => $data['email'] ?: null,
                    'phone'        => $data['telephone'] ?: null,
                    'address'      => $data['adresse'] ?: null,
                    'status'       => $data['statut'],
                ]);

                $this->importedCount++;
            } catch (\Throwable $e) {
                $this->errors[] = [
                    'row'    => $rowNumber,
                    'name'   => trim("{$data['nom']} {$data['prenom']}"),
                    'errors' => ["Erreur lors de l'enregistrement : " . $e->getMessage()],
                ];
            }

            $rowNumber++;
        }
    }
}
