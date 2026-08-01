<?php

namespace Database\Seeders;

use App\Domain\Agency\Models\Agency;
use App\Domain\Arrears\Enums\ArrearSeverity;
use App\Domain\Arrears\Enums\ArrearStatus;
use App\Domain\Arrears\Models\Arrear;
use App\Domain\Arrears\Models\Reminder;
use App\Domain\Audit\Models\AuditLog;
use App\Domain\Deposit\Enums\DepositStatus;
use App\Domain\Deposit\Models\Deposit;
use App\Domain\Incident\Models\Incident;
use App\Domain\Lease\Enums\LeaseStatus;
use App\Domain\Lease\Models\Lease;
use App\Domain\Lease\Models\LeaseTemplate;
use App\Domain\Notification\Models\SystemNotification;
use App\Domain\Owner\Enums\ManagementContractStatus;
use App\Domain\Owner\Models\ManagementContract;
use App\Domain\Owner\Models\Owner;
use App\Domain\Payment\Enums\PaymentMethod;
use App\Domain\Payment\Models\Payment;
use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use App\Domain\Rent\Enums\RentScheduleStatus;
use App\Domain\Rent\Models\RentSchedule;
use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Agences
        $primaryAgency = Agency::firstOrCreate(
            ['email' => 'contact@horizon-immo.ci'],
            [
                'name'              => 'Horizon Immobilier SARL',
                'legal_name'        => 'Horizon Immobilier Côte d\'Ivoire SARL',
                'phone'             => '+225 07 08 09 10 11',
                'address'           => 'Cocody Ambassades, Rue des Ambassades, Abidjan',
                'commission_rate'   => 10.00,
                'is_subject_to_tva' => true,
                'status'            => 'active',
            ]
        );

        $secondaryAgency = Agency::firstOrCreate(
            ['email' => 'contact@prestige-habitat.ci'],
            [
                'name'              => 'Prestige Habitat SA',
                'legal_name'        => 'Prestige Habitat SA',
                'phone'             => '+225 05 04 03 02 01',
                'address'           => 'Plateau, Avenue Chardy, Abidjan',
                'commission_rate'   => 12.00,
                'is_subject_to_tva' => false,
                'status'            => 'active',
            ]
        );

        // Run Referential & Lease seeders to ensure basic types and templates exist for all agencies
        $this->call([
            ReferentialSeeder::class,
            LeaseSeeder::class,
        ]);

        // 2. Utilisateur Super Admin SaaS (Plateforme)
        $passwordHash = Hash::make('password');

        $superAdmin = User::firstOrCreate(
            ['email' => 'saasadmin@easyimmob.com'],
            [
                'agency_id'         => null,
                'name'              => 'Administrateur SaaS',
                'password'          => $passwordHash,
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // 3. Utilisateurs de test pour l'agence principale
        $admin = User::firstOrCreate(
            ['email' => 'admin@easyimmob.com'], //LOC-501409
            [
                'agency_id'         => $primaryAgency->id,
                'name'              => 'Jean-Luc Koffi',
                'password'          => $passwordHash,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('Administrateur');

        $gestionnaire = User::firstOrCreate(
            ['email' => 'gestionnaire@easyimmob.com'],
            [
                'agency_id'         => $primaryAgency->id,
                'name'              => 'Awa Touré',
                'password'          => $passwordHash,
                'email_verified_at' => now(),
            ]
        );
        $gestionnaire->assignRole('Gestionnaire');

        $comptable = User::firstOrCreate(
            ['email' => 'comptable@easyimmob.com'],
            [
                'agency_id'         => $primaryAgency->id,
                'name'              => 'Marc Yao',
                'password'          => $passwordHash,
                'email_verified_at' => now(),
            ]
        );
        $comptable->assignRole('Comptable');

        $agent = User::firstOrCreate(
            ['email' => 'agent@easyimmob.com'],
            [
                'agency_id'         => $primaryAgency->id,
                'name'              => 'Soro Guillaume',
                'password'          => $passwordHash,
                'email_verified_at' => now(),
            ]
        );
        $agent->assignRole('Agent');

        // Locataire citoyen test
        $locataireUser = User::firstOrCreate(
            ['email' => 'locataire@easyimmob.com'],
            [
                'agency_id'         => null,
                'name'              => 'David Yao',
                'password'          => $passwordHash,
                'email_verified_at' => now(),
            ]
        );
        $locataireUser->assignRole('Locataire');

        // Second agency admin
        $adminPrestige = User::firstOrCreate(
            ['email' => 'admin.prestige@easyimmob.com'],
            [
                'agency_id'         => $secondaryAgency->id,
                'name'              => 'Claire Bamba',
                'password'          => $passwordHash,
                'email_verified_at' => now(),
            ]
        );
        $adminPrestige->assignRole('Administrateur');

        // 3. Propriétaires (Owners)
        $ownersData = [
            [
                'reference'    => 'PRO-0001',
                'first_name'   => 'Ibrahim',
                'last_name'    => 'Koné',
                'company_name' => 'Ivoirienne d\'Investissement',
                'email'        => 'ibrahim.kone@invest-ci.com',
                'phone'        => '+225 07 01 02 03 04',
                'address'      => 'Cocody Riviera 3, Abidjan',
            ],
            [
                'reference'    => 'PRO-0002',
                'first_name'   => 'Marie-Claire',
                'last_name'    => 'Diallo',
                'company_name' => null,
                'email'        => 'mc.diallo@yahoo.fr',
                'phone'        => '+225 05 12 23 34 45',
                'address'      => 'Deux Plateaux Vallons, Abidjan',
            ],
            [
                'reference'    => 'PRO-0003',
                'first_name'   => 'Kouassi',
                'last_name'    => 'N\'Dri',
                'company_name' => 'SCI Ivoire Patrimoine',
                'email'        => 'contact@ivoire-patrimoine.ci',
                'phone'        => '+225 01 99 88 77 66',
                'address'      => 'Plateau, Immeuble Alpha, Abidjan',
            ],
            [
                'reference'    => 'PRO-0004',
                'first_name'   => 'Yves',
                'last_name'    => 'Bamba',
                'company_name' => null,
                'email'        => 'dr.bamba@clinic.ci',
                'phone'        => '+225 07 55 44 33 22',
                'address'      => 'Marcory Zone 4, Abidjan',
            ],
            [
                'reference'    => 'PRO-0005',
                'first_name'   => 'Chantal',
                'last_name'    => 'Kouassi',
                'company_name' => null,
                'email'        => 'chantal.kouassi@hotmail.com',
                'phone'        => '+225 05 66 77 88 99',
                'address'      => 'Cocody Danga, Abidjan',
            ],
            [
                'reference'    => 'PRO-0006',
                'first_name'   => 'Seydou',
                'last_name'    => 'Gbané',
                'company_name' => 'KGS Immobilier SARL',
                'email'        => 'kgs@group-kgs.com',
                'phone'        => '+225 07 44 33 22 11',
                'address'      => 'Yopougon Zone Industrielle, Abidjan',
            ],
        ];

        $owners = [];
        foreach ($ownersData as $oData) {
            $owners[$oData['reference']] = Owner::withoutGlobalScopes()->firstOrCreate(
                [
                    'agency_id' => $primaryAgency->id,
                    'reference' => $oData['reference'],
                ],
                array_merge($oData, ['status' => 'active'])
            );
        }

        // 3b. Mandats de gestion (Management Contracts)
        $contracts = [];
        foreach ($owners as $ref => $owner) {
            $contracts[$ref] = ManagementContract::withoutGlobalScopes()->firstOrCreate(
                [
                    'agency_id' => $primaryAgency->id,
                    'reference' => 'MAN-2025-' . str_replace('PRO-', '', $ref),
                ],
                [
                    'owner_id'              => $owner->id,
                    'title'                 => 'Mandat de Gestion Exclusif — ' . $owner->full_name,
                    'start_date'            => '2025-01-01',
                    'duration_months'       => 12,
                    'commission_type'       => 'percentage',
                    'commission_value'      => 10.00,
                    'irf_paid_by_owner'     => true,
                    'caution_kept_by_agency'=> true,
                    'notice_period_months'  => 3,
                    'payment_bank_details'  => 'Virement bancaire / Compte BOA N° CI092 01001 123456789012 34',
                    'status'                => ManagementContractStatus::Active,
                    'signed_at'             => '2024-12-15 10:00:00',
                ]
            );
        }

        // Types de biens
        $typeVilla       = PropertyType::withoutGlobalScopes()->where('agency_id', $primaryAgency->id)->where('name', 'Villa')->first();
        $typeAppartement = PropertyType::withoutGlobalScopes()->where('agency_id', $primaryAgency->id)->where('name', 'Appartement')->first();
        $typeStudio      = PropertyType::withoutGlobalScopes()->where('agency_id', $primaryAgency->id)->where('name', 'Studio')->first();
        $typeMaison      = PropertyType::withoutGlobalScopes()->where('agency_id', $primaryAgency->id)->where('name', 'Maison')->first();
        $typeMagasin     = PropertyType::withoutGlobalScopes()->where('agency_id', $primaryAgency->id)->where('name', 'Magasin')->first();
        $typeBureau      = PropertyType::withoutGlobalScopes()->where('agency_id', $primaryAgency->id)->where('name', 'Bureau')->first();
        $typeEntrepot    = PropertyType::withoutGlobalScopes()->where('agency_id', $primaryAgency->id)->where('name', 'Entrepôt')->first();
        $typeTerrain     = PropertyType::withoutGlobalScopes()->where('agency_id', $primaryAgency->id)->where('name', 'Terrain')->first();

        // 4. Biens Immobiliers (Properties)
        $propertiesData = [
            [
                'reference'        => 'BIE-0001',
                'owner_id'         => $owners['PRO-0001']->id,
                'property_type_id' => $typeVilla->id,
                'title'            => 'Villa Duplex 5 Pièces avec Piscine',
                'description'      => 'Superbe villa de grand standing comprenant 4 chambres autonomes, un grand séjour, cuisine équipée, jardin arboré et piscine privée.',
                'address'          => 'Rue des Jardins, Cité CIRA',
                'city'             => 'Abidjan',
                'neighborhood'     => 'Cocody Riviera 3',
                'latitude'         => 5.359951,
                'longitude'        => -4.008256,
                'google_maps_url'  => 'https://maps.google.com/?q=5.359951,-4.008256',
                'surface_area'     => 350.00,
                'bedrooms'         => 4,
                'bathrooms'        => 4,
                'rent_amount'      => 1200000.00,
                'photos'           => [
                    'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1000&q=80'
                ],
                'videos'           => [
                    'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
                ],
                'status'           => PropertyStatus::Occupied,
            ],
            [
                'reference'        => 'BIE-0002',
                'owner_id'         => $owners['PRO-0001']->id,
                'property_type_id' => $typeAppartement->id,
                'title'            => 'Appartement Standing 3 Pièces Vue Lagune',
                'description'      => 'Appartement lumineux avec balcons offrant une vue panoramique sur la lagune Ébrié, ascenseur et sécurité 24/7.',
                'address'          => 'Avenue Nogues, Résidence Lagune B',
                'city'             => 'Abidjan',
                'neighborhood'     => 'Plateau',
                'latitude'         => 5.326111,
                'longitude'        => -4.021111,
                'google_maps_url'  => 'https://maps.google.com/?q=5.326111,-4.021111',
                'surface_area'     => 110.00,
                'bedrooms'         => 2,
                'bathrooms'        => 2,
                'rent_amount'      => 450000.00,
                'photos'           => [
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1000&q=80'
                ],
                'videos'           => [],
                'status'           => PropertyStatus::Occupied,
            ],
            [
                'reference'        => 'BIE-0003',
                'owner_id'         => $owners['PRO-0002']->id,
                'property_type_id' => $typeStudio->id,
                'title'            => 'Studio Meublé Moderne',
                'description'      => 'Studio coquet entièrement équipé, idéal pour jeune professionnel ou cadre de passage. Climatisation et Wi-Fi inclus.',
                'address'          => 'Boulevard Latrille',
                'city'             => 'Abidjan',
                'neighborhood'     => 'Cocody Angré 8ème Tranche',
                'surface_area'     => 45.00,
                'bedrooms'         => 1,
                'bathrooms'        => 1,
                'rent_amount'      => 180000.00,
                'status'           => PropertyStatus::Occupied,
            ],
            [
                'reference'        => 'BIE-0004',
                'owner_id'         => $owners['PRO-0002']->id,
                'property_type_id' => $typeMaison->id,
                'title'            => 'Maison Basse 6 Pièces Jardin',
                'description'      => 'Grande résidence familiale située dans un quartier calme et sécurisé avec garage 2 véhicules et dépendance gardien.',
                'address'          => 'Rue des Hortensias',
                'city'             => 'Abidjan',
                'neighborhood'     => 'Deux Plateaux Vallons',
                'surface_area'     => 280.00,
                'bedrooms'         => 4,
                'bathrooms'        => 3,
                'rent_amount'      => 850000.00,
                'status'           => PropertyStatus::Occupied,
            ],
            [
                'reference'        => 'BIE-0005',
                'owner_id'         => $owners['PRO-0003']->id,
                'property_type_id' => $typeMagasin->id,
                'title'            => 'Boutique Commerciale Marcory',
                'description'      => 'Emplacement commercial stratégique à fort passage pieton et automobile. Vitrine securisée.',
                'address'          => 'Boulevard de Marseille',
                'city'             => 'Abidjan',
                'neighborhood'     => 'Marcory Zone 4',
                'surface_area'     => 85.00,
                'bedrooms'         => null,
                'bathrooms'        => 1,
                'rent_amount'      => 600000.00,
                'status'           => PropertyStatus::Occupied,
            ],
            [
                'reference'        => 'BIE-0006',
                'owner_id'         => $owners['PRO-0003']->id,
                'property_type_id' => $typeBureau->id,
                'title'            => 'Plateau de Bureaux Open Space 200m²',
                'description'      => 'Espace de bureau modulable avec salle de réunion, coin kitchinette et câblage réseau haute vitesse.',
                'address'          => 'Rue du Commerce, Immeuble Le Postel',
                'city'             => 'Abidjan',
                'neighborhood'     => 'Plateau',
                'surface_area'     => 200.00,
                'bedrooms'         => null,
                'bathrooms'        => 2,
                'rent_amount'      => 1500000.00,
                'status'           => PropertyStatus::Occupied,
            ],
            [
                'reference'        => 'BIE-0007',
                'owner_id'         => $owners['PRO-0004']->id,
                'property_type_id' => $typeVilla->id,
                'title'            => 'Villa Basse 4 Pièces',
                'description'      => 'Jolie villa individuelle avec cour avant et arrière. Quartier résidentiel paisible.',
                'address'          => 'Carrefour Lokoa',
                'city'             => 'Abidjan',
                'neighborhood'     => 'Yopougon Niangon',
                'surface_area'     => 150.00,
                'bedrooms'         => 3,
                'bathrooms'        => 2,
                'rent_amount'      => 250000.00,
                'status'           => PropertyStatus::Occupied,
            ],
            [
                'reference'        => 'BIE-0008',
                'owner_id'         => $owners['PRO-0006']->id,
                'property_type_id' => $typeEntrepot->id,
                'title'            => 'Entrepôt Logistique Stockage 500m²',
                'description'      => 'Grand hangar industriel sécurisé avec accès poids lourds, quai de déchargement et bureaux administratifs.',
                'address'          => 'Boulevard de Vridi',
                'city'             => 'Abidjan',
                'neighborhood'     => 'Vridi Zone Industrielle',
                'surface_area'     => 500.00,
                'bedrooms'         => null,
                'bathrooms'        => 2,
                'rent_amount'      => 2000000.00,
                'status'           => PropertyStatus::Available,
            ],
            [
                'reference'        => 'BIE-0009',
                'owner_id'         => $owners['PRO-0005']->id,
                'property_type_id' => $typeAppartement->id,
                'title'            => 'Appartement 2 Pièces Équipé',
                'description'      => 'Appartement meublé dans un immeuble récent avec place de parking et balcon.',
                'address'          => 'Résidence Saint Michel',
                'city'             => 'Abidjan',
                'neighborhood'     => 'Cocody Riviera Palmeraie',
                'surface_area'     => 60.00,
                'bedrooms'         => 1,
                'bathrooms'        => 1,
                'rent_amount'      => 220000.00,
                'status'           => PropertyStatus::Available,
            ],
            [
                'reference'        => 'BIE-0010',
                'owner_id'         => $owners['PRO-0004']->id,
                'property_type_id' => $typeTerrain->id,
                'title'            => 'Terrain Commercial Clôturé 1000m²',
                'description'      => 'Terrain plat avec ACD, idéal pour base logistique, dépôt ou construction commerciale.',
                'address'          => 'Route de Bingerville',
                'city'             => 'Bingerville',
                'neighborhood'     => 'Feh Kessé',
                'surface_area'     => 1000.00,
                'bedrooms'         => null,
                'bathrooms'        => null,
                'rent_amount'      => 300000.00,
                'status'           => PropertyStatus::Available,
            ],
            [
                'reference'        => 'BIE-0011',
                'owner_id'         => $owners['PRO-0005']->id,
                'property_type_id' => $typeAppartement->id,
                'title'            => 'Appartement 4 Pièces Haut Standing',
                'description'      => 'Actuellement en réfection peinture et plomberie. Disponible sous 2 semaines.',
                'address'          => 'Rue Biaka Boda',
                'city'             => 'Abidjan',
                'neighborhood'     => 'Marcory Résidentiel',
                'surface_area'     => 140.00,
                'bedrooms'         => 3,
                'bathrooms'        => 2,
                'rent_amount'      => 550000.00,
                'status'           => PropertyStatus::Maintenance,
            ],
            [
                'reference'        => 'BIE-0012',
                'owner_id'         => $owners['PRO-0006']->id,
                'property_type_id' => $typeVilla->id,
                'title'            => 'Duplex Moderne 5 Pièces',
                'description'      => 'Duplex spacieux remis à neuf récemment avec garage couvert.',
                'address'          => 'Rue Mermoz',
                'city'             => 'Abidjan',
                'neighborhood'     => 'Cocody Mermoz',
                'surface_area'     => 250.00,
                'bedrooms'         => 3,
                'bathrooms'        => 3,
                'rent_amount'      => 950000.00,
                'status'           => PropertyStatus::Available,
            ],
        ];

        $properties = [];
        foreach ($propertiesData as $pData) {
            $ownerRef = array_search($pData['owner_id'], array_map(fn($o) => $o->id, $owners));
            if ($ownerRef && isset($contracts[$ownerRef])) {
                $pData['management_contract_id'] = $contracts[$ownerRef]->id;
            }

            $properties[$pData['reference']] = Property::withoutGlobalScopes()->updateOrCreate(
                [
                    'agency_id' => $primaryAgency->id,
                    'reference' => $pData['reference'],
                ],
                $pData
            );
        }

        // 5. Locataires (Tenants)
        $tenantsData = [
            [
                'reference'         => 'LOC-0001',
                'first_name'        => 'David',
                'last_name'         => 'Yao',
                'email'             => 'david.yao@gmail.com',
                'phone'             => '+225 07 11 22 33 44',
                'address'           => 'Cocody Riviera 3, Abidjan',
                'emergency_contact' => 'Mme Koffi Akissi (Sœur) - +225 07 99 88 77',
            ],
            [
                'reference'         => 'LOC-0002',
                'first_name'        => 'Fatou',
                'last_name'         => 'Cissé',
                'email'             => 'fatou.cisse@yahoo.fr',
                'phone'             => '+225 05 22 33 44 55',
                'address'           => 'Plateau, Abidjan',
                'emergency_contact' => 'M. Bakary Cissé (Frère) - +225 05 11 22 33',
            ],
            [
                'reference'         => 'LOC-0003',
                'first_name'        => 'Stephane',
                'last_name'         => 'Koffi',
                'email'             => 'stephane.koffi@gmail.com',
                'phone'             => '+225 01 33 44 55 66',
                'address'           => 'Angré 8ème Tranche, Abidjan',
                'emergency_contact' => 'Mme Yao Affoué (Epouse) - +225 01 00 11 22',
            ],
            [
                'reference'         => 'LOC-0004',
                'first_name'        => 'Aminata',
                'last_name'         => 'Traoré',
                'email'             => 'aminata.traore@corporate.ci',
                'phone'             => '+225 07 44 55 66 77',
                'address'           => 'Deux Plateaux Vallons, Abidjan',
                'emergency_contact' => 'M. Oumar Traoré (Père) - +225 07 88 99 00',
            ],
            [
                'reference'         => 'LOC-0005',
                'first_name'        => 'Christian',
                'last_name'         => 'N\'Guessan',
                'email'             => 'christian.nguessan@gmail.com',
                'phone'             => '+225 05 55 66 77 88',
                'address'           => 'Marcory Zone 4, Abidjan',
                'emergency_contact' => 'Mme Estelle N\'Guessan - +225 05 44 33 22',
            ],
            [
                'reference'         => 'LOC-0006',
                'first_name'        => 'TechIvoire',
                'last_name'         => 'SARL',
                'email'             => 'contact@techivoire.ci',
                'phone'             => '+225 01 66 77 88 99',
                'address'           => 'Plateau Rue du Commerce, Abidjan',
                'emergency_contact' => 'DG Alain Bamba - +225 01 22 33 44',
            ],
            [
                'reference'         => 'LOC-0007',
                'first_name'        => 'Franck',
                'last_name'         => 'Kouadio',
                'email'             => 'franck.kouadio@outlook.com',
                'phone'             => '+225 07 77 88 99 00',
                'address'           => 'Yopougon Niangon, Abidjan',
                'emergency_contact' => 'Mme Kouadio Amenan - +225 07 12 34 56',
            ],
            [
                'reference'         => 'LOC-0008',
                'first_name'        => 'Patricia',
                'last_name'         => 'Aka',
                'email'             => 'patricia.aka@gmail.com',
                'phone'             => '+225 05 88 99 00 11',
                'address'           => 'Cocody Palmeraie, Abidjan',
                'emergency_contact' => 'Mme Aka Juliette - +225 05 99 88 77',
            ],
            [
                'reference'         => 'LOC-0009',
                'first_name'        => 'Jean-Philippe',
                'last_name'         => 'Diabaté',
                'email'             => 'jp.diabate@gmail.com',
                'phone'             => '+225 01 99 00 11 22',
                'address'           => 'Cocody Mermoz, Abidjan',
                'emergency_contact' => 'M. Diabaté Lassina - +225 01 55 66 77',
            ],
            [
                'reference'         => 'LOC-0010',
                'first_name'        => 'Sandrine',
                'last_name'         => 'Boni',
                'email'             => 'sandrine.boni@gmail.com',
                'phone'             => '+225 07 00 11 22 33',
                'address'           => 'Bingerville, Abidjan',
                'emergency_contact' => 'M. Boni Serge - +225 07 66 55 44',
            ],
        ];

        $tenants = [];
        foreach ($tenantsData as $tData) {
            $extraData = ['status' => 'active'];
            if ($tData['reference'] === 'LOC-0001') {
                $extraData['user_id'] = $locataireUser->id;
            }

            $tenants[$tData['reference']] = Tenant::withoutGlobalScopes()->firstOrCreate(
                [
                    'agency_id' => $primaryAgency->id,
                    'reference' => $tData['reference'],
                ],
                array_merge($tData, $extraData)
            );
        }

        // Template de bail
        $leaseTemplate = LeaseTemplate::withoutGlobalScopes()
            ->where('agency_id', $primaryAgency->id)
            ->first();

        // 6. Contrats de location (Leases)
        $leasesData = [
            [
                'reference'       => 'CON-0001',
                'property_id'     => $properties['BIE-0001']->id,
                'tenant_id'       => $tenants['LOC-0001']->id,
                'template_id'     => $leaseTemplate?->id,
                'start_date'      => '2025-11-01',
                'end_date'        => '2027-10-31',
                'rent_amount'     => 1200000.00,
                'charges_amount'  => 50000.00,
                'payment_due_day' => 5,
                'deposit_amount'  => 2400000.00,
                'status'          => LeaseStatus::Active,
                'signed_at'       => '2025-10-25 10:00:00',
            ],
            [
                'reference'       => 'CON-0002',
                'property_id'     => $properties['BIE-0002']->id,
                'tenant_id'       => $tenants['LOC-0002']->id,
                'template_id'     => $leaseTemplate?->id,
                'start_date'      => '2026-01-01',
                'end_date'        => '2027-12-31',
                'rent_amount'     => 450000.00,
                'charges_amount'  => 20000.00,
                'payment_due_day' => 5,
                'deposit_amount'  => 900000.00,
                'status'          => LeaseStatus::Active,
                'signed_at'       => '2025-12-20 15:30:00',
            ],
            [
                'reference'       => 'CON-0003',
                'property_id'     => $properties['BIE-0003']->id,
                'tenant_id'       => $tenants['LOC-0003']->id,
                'template_id'     => $leaseTemplate?->id,
                'start_date'      => '2025-09-01',
                'end_date'        => '2026-08-31',
                'rent_amount'     => 180000.00,
                'charges_amount'  => 10000.00,
                'payment_due_day' => 5,
                'deposit_amount'  => 360000.00,
                'status'          => LeaseStatus::Active,
                'signed_at'       => '2025-08-28 11:00:00',
            ],
            [
                'reference'       => 'CON-0004',
                'property_id'     => $properties['BIE-0004']->id,
                'tenant_id'       => $tenants['LOC-0004']->id,
                'template_id'     => $leaseTemplate?->id,
                'start_date'      => '2026-03-01',
                'end_date'        => '2028-02-28',
                'rent_amount'     => 850000.00,
                'charges_amount'  => 30000.00,
                'payment_due_day' => 5,
                'deposit_amount'  => 1700000.00,
                'status'          => LeaseStatus::Active,
                'signed_at'       => '2026-02-22 09:15:00',
            ],
            [
                'reference'       => 'CON-0005',
                'property_id'     => $properties['BIE-0005']->id,
                'tenant_id'       => $tenants['LOC-0005']->id,
                'template_id'     => $leaseTemplate?->id,
                'start_date'      => '2025-06-01',
                'end_date'        => '2027-05-31',
                'rent_amount'     => 600000.00,
                'charges_amount'  => 25000.00,
                'payment_due_day' => 5,
                'deposit_amount'  => 1200000.00,
                'status'          => LeaseStatus::Active,
                'signed_at'       => '2025-05-20 14:00:00',
            ],
            [
                'reference'       => 'CON-0006',
                'property_id'     => $properties['BIE-0006']->id,
                'tenant_id'       => $tenants['LOC-0006']->id,
                'template_id'     => $leaseTemplate?->id,
                'start_date'      => '2026-04-01',
                'end_date'        => '2029-03-31',
                'rent_amount'     => 1500000.00,
                'charges_amount'  => 100000.00,
                'payment_due_day' => 5,
                'deposit_amount'  => 3000000.00,
                'status'          => LeaseStatus::Active,
                'signed_at'       => '2026-03-25 16:45:00',
            ],
            [
                'reference'       => 'CON-0007',
                'property_id'     => $properties['BIE-0007']->id,
                'tenant_id'       => $tenants['LOC-0007']->id,
                'template_id'     => $leaseTemplate?->id,
                'start_date'      => '2025-05-01',
                'end_date'        => '2027-04-30',
                'rent_amount'     => 250000.00,
                'charges_amount'  => 15000.00,
                'payment_due_day' => 5,
                'deposit_amount'  => 500000.00,
                'status'          => LeaseStatus::Active,
                'signed_at'       => '2025-04-20 11:30:00',
            ],
            [
                'reference'       => 'CON-0008',
                'property_id'     => $properties['BIE-0009']->id,
                'tenant_id'       => $tenants['LOC-0008']->id,
                'template_id'     => $leaseTemplate?->id,
                'start_date'      => '2026-08-01',
                'end_date'        => '2027-07-31',
                'rent_amount'     => 220000.00,
                'charges_amount'  => 10000.00,
                'payment_due_day' => 5,
                'deposit_amount'  => 440000.00,
                'status'          => LeaseStatus::Draft,
                'signed_at'       => null,
            ],
            [
                'reference'       => 'CON-0009',
                'property_id'     => $properties['BIE-0012']->id,
                'tenant_id'       => $tenants['LOC-0009']->id,
                'template_id'     => $leaseTemplate?->id,
                'start_date'      => '2024-06-01',
                'end_date'        => '2026-05-31',
                'rent_amount'     => 950000.00,
                'charges_amount'  => 40000.00,
                'payment_due_day' => 5,
                'deposit_amount'  => 1900000.00,
                'status'          => LeaseStatus::Terminated,
                'signed_at'       => '2024-05-25 10:00:00',
                'terminated_at'   => '2026-05-31 23:59:59',
            ],
        ];

        $leases = [];
        foreach ($leasesData as $lData) {
            $leases[$lData['reference']] = Lease::withoutGlobalScopes()->firstOrCreate(
                [
                    'agency_id' => $primaryAgency->id,
                    'reference' => $lData['reference'],
                ],
                $lData
            );
        }

        // 7. Cautions / Dépôts de garantie (Deposits)
        foreach ($leases as $ref => $lease) {
            if ($lease->status === LeaseStatus::Active) {
                Deposit::withoutGlobalScopes()->firstOrCreate(
                    [
                        'agency_id' => $primaryAgency->id,
                        'lease_id'  => $lease->id,
                    ],
                    [
                        'expected_amount' => $lease->deposit_amount,
                        'received_amount' => $lease->deposit_amount,
                        'received_at'     => $lease->start_date,
                        'retained_amount' => 0,
                        'refunded_amount' => 0,
                        'status'          => DepositStatus::Held,
                    ]
                );
            } elseif ($lease->status === LeaseStatus::Terminated) {
                Deposit::withoutGlobalScopes()->firstOrCreate(
                    [
                        'agency_id' => $primaryAgency->id,
                        'lease_id'  => $lease->id,
                    ],
                    [
                        'expected_amount'  => $lease->deposit_amount,
                        'received_amount'  => $lease->deposit_amount,
                        'received_at'      => $lease->start_date,
                        'retained_amount'  => 200000.00,
                        'retention_reason' => 'Réfection peinture salon et remplacement serrure',
                        'refunded_amount'  => $lease->deposit_amount - 200000.00,
                        'refunded_at'      => '2026-06-15',
                        'status'           => DepositStatus::Refunded,
                    ]
                );
            }
        }

        // 8. Échéanciers de loyer (Rent Schedules) & Règlements (Payments)
        // Mois concernés: 2026-04, 2026-05, 2026-06, 2026-07, 2026-08
        $periods = [
            '2026-04' => '2026-04-05',
            '2026-05' => '2026-05-05',
            '2026-06' => '2026-06-05',
            '2026-07' => '2026-07-05',
            '2026-08' => '2026-08-05',
        ];

        $paymentIndex = 1;

        foreach ($leases as $leaseRef => $lease) {
            if ($lease->status !== LeaseStatus::Active) {
                continue;
            }

            $monthlyTotal = $lease->rent_amount + $lease->charges_amount;

            foreach ($periods as $period => $dueDate) {
                // If period is before lease start_date, skip
                if (Carbon::parse($period . '-01')->lt(Carbon::parse($lease->start_date)->startOfMonth())) {
                    continue;
                }

                // Determiner le statut et les montants payes selon le cas de figure pour varier la demo
                $status         = RentScheduleStatus::Paid;
                $paidAmount     = $monthlyTotal;
                $remaining      = 0.00;
                $isOverdue      = false;
                $isPartial      = false;

                if ($period === '2026-07') {
                    if ($leaseRef === 'CON-0005') {
                        // Paiement partiel
                        $status     = RentScheduleStatus::PartiallyPaid;
                        $paidAmount = 300000.00;
                        $remaining  = $monthlyTotal - $paidAmount;
                        $isPartial  = true;
                    } elseif ($leaseRef === 'CON-0007') {
                        // En retard / Impayé
                        $status     = RentScheduleStatus::Overdue;
                        $paidAmount = 0.00;
                        $remaining  = $monthlyTotal;
                        $isOverdue  = true;
                    }
                } elseif ($period === '2026-08') {
                    if ($leaseRef === 'CON-0001') {
                        // Payé en avance
                        $status     = RentScheduleStatus::Paid;
                        $paidAmount = $monthlyTotal;
                        $remaining  = 0.00;
                    } elseif ($leaseRef === 'CON-0007') {
                        // 2ème mois en retard
                        $status     = RentScheduleStatus::Overdue;
                        $paidAmount = 0.00;
                        $remaining  = $monthlyTotal;
                        $isOverdue  = true;
                    } else {
                        // En attente
                        $status     = RentScheduleStatus::Pending;
                        $paidAmount = 0.00;
                        $remaining  = $monthlyTotal;
                    }
                }

                $schedule = RentSchedule::withoutGlobalScopes()->firstOrCreate(
                    [
                        'agency_id' => $primaryAgency->id,
                        'lease_id'  => $lease->id,
                        'period'    => $period,
                    ],
                    [
                        'due_date'         => $dueDate,
                        'expected_amount'  => $monthlyTotal,
                        'paid_amount'      => $paidAmount,
                        'remaining_amount' => $remaining,
                        'status'           => $status,
                    ]
                );

                // Si un paiement a été effectué, créer l'enregistrement Payment
                if ($paidAmount > 0) {
                    $method = match ($paymentIndex % 4) {
                        0 => PaymentMethod::BankTransfer,
                        1 => PaymentMethod::MobileMoney,
                        2 => PaymentMethod::Cash,
                        3 => PaymentMethod::Check,
                    };

                    Payment::withoutGlobalScopes()->firstOrCreate(
                        [
                            'agency_id'        => $primaryAgency->id,
                            'rent_schedule_id' => $schedule->id,
                            'reference'        => 'PAY-' . str_pad((string) $paymentIndex, 5, '0', STR_PAD_LEFT),
                        ],
                        [
                            'recorded_by_id' => $comptable->id,
                            'amount'         => $paidAmount,
                            'payment_date'   => Carbon::parse($dueDate)->subDays(rand(0, 3))->format('Y-m-d'),
                            'payment_method' => $method,
                            'status'         => 'completed',
                            'notes'          => 'Règlement loyer + charges ' . $period,
                        ]
                    );

                    $paymentIndex++;
                }

                // Si l'échéance est en impayé ou partiel, créer une entrée dans Arrears
                if ($isOverdue || $isPartial) {
                    $severity = $isOverdue ? ArrearSeverity::Warning : ArrearSeverity::Warning;
                    $arrStatus = $isPartial ? ArrearStatus::Open : ArrearStatus::Open;

                    $arrear = Arrear::withoutGlobalScopes()->firstOrCreate(
                        [
                            'agency_id'        => $primaryAgency->id,
                            'rent_schedule_id' => $schedule->id,
                        ],
                        [
                            'lease_id'           => $lease->id,
                            'tenant_id'          => $lease->tenant_id,
                            'amount_due'         => $monthlyTotal,
                            'amount_paid'        => $paidAmount,
                            'remaining_amount'   => $remaining,
                            'first_overdue_date' => $dueDate,
                            'severity'           => $severity,
                            'status'             => $arrStatus,
                        ]
                    );

                    // Créer des relances pour l'impayé
                    if ($isOverdue) {
                        Reminder::withoutGlobalScopes()->firstOrCreate(
                            [
                                'agency_id'  => $primaryAgency->id,
                                'arrears_id' => $arrear->id,
                                'channel'    => 'email',
                            ],
                            [
                                'sent_at' => Carbon::parse($dueDate)->addDays(5),
                                'content' => 'Avis de relance de loyer impayé pour la période ' . $period . '. Montant réclamé : ' . number_format($remaining, 0, ',', ' ') . ' FCFA.',
                                'status'  => 'sent',
                            ]
                        );

                        Reminder::withoutGlobalScopes()->firstOrCreate(
                            [
                                'agency_id'  => $primaryAgency->id,
                                'arrears_id' => $arrear->id,
                                'channel'    => 'sms',
                            ],
                            [
                                'sent_at' => Carbon::parse($dueDate)->addDays(12),
                                'content' => 'Rappel Horizon Immo: Votre loyer de ' . $period . ' de ' . number_format($remaining, 0, ',', ' ') . ' FCFA est en retard. Merci de régulariser.',
                                'status'  => 'sent',
                            ]
                        );
                    }
                }
            }
        }

        // 9. Notifications Système
        SystemNotification::withoutGlobalScopes()->firstOrCreate(
            [
                'agency_id' => $primaryAgency->id,
                'subject'   => 'Bienvenue sur Easy-Immob',
            ],
            [
                'recipient_type' => User::class,
                'recipient_id'   => $admin->id,
                'type'           => 'welcome',
                'channel'        => 'database',
                'content'        => 'Votre plateforme de gestion immobilière est initialisée et prête à l\'emploi.',
                'sent_at'        => now()->subDays(30),
                'status'         => 'sent',
            ]
        );

        SystemNotification::withoutGlobalScopes()->firstOrCreate(
            [
                'agency_id' => $primaryAgency->id,
                'subject'   => 'Alerte Retard de Loyer - Franck Kouadio',
            ],
            [
                'recipient_type' => User::class,
                'recipient_id'   => $gestionnaire->id,
                'type'           => 'arrear_alert',
                'channel'        => 'database',
                'content'        => 'Le locataire Franck Kouadio a accumulé du retard sur son loyer de Juillet 2026.',
                'sent_at'        => now()->subDays(5),
                'status'         => 'sent',
            ]
        );

        // 10. Audit Logs
        AuditLog::withoutGlobalScopes()->firstOrCreate(
            [
                'agency_id'   => $primaryAgency->id,
                'action'      => 'CREATE_LEASE',
                'entity_type' => Lease::class,
                'entity_id'   => $leases['CON-0001']->id,
            ],
            [
                'user_id'    => $gestionnaire->id,
                'new_values' => ['reference' => 'CON-0001', 'status' => 'active'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            ]
        );

        AuditLog::withoutGlobalScopes()->firstOrCreate(
            [
                'agency_id'   => $primaryAgency->id,
                'action'      => 'RECORD_PAYMENT',
                'entity_type' => Payment::class,
                'entity_id'   => 1,
            ],
            [
                'user_id'    => $comptable->id,
                'new_values' => ['reference' => 'PAY-00001', 'amount' => 1250000.00],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            ]
        );

        // 11. Incidents & Réparations
        Incident::withoutGlobalScopes()->firstOrCreate(
            ['reference' => 'INC-0001'],
            [
                'agency_id'      => $primaryAgency->id,
                'property_id'    => $properties['BIE-0001']->id,
                'lease_id'       => $leases['CON-0001']->id,
                'tenant_id'      => $tenants['LOC-0001']->id,
                'title'          => 'Fuite d\'eau sous évier cuisine',
                'description'    => 'Le tuyau sous l\'évier goutte abondamment depuis ce matin.',
                'priority'       => 'high',
                'status'         => 'resolved',
                'repair_details' => 'Remplacement du joint de siphon et serrage des raccordements par le plombier partenaire.',
                'repair_cost'    => 35000.00,
                'resolved_at'    => now()->subDays(2),
            ]
        );

        Incident::withoutGlobalScopes()->firstOrCreate(
            ['reference' => 'INC-0002'],
            [
                'agency_id'                 => $primaryAgency->id,
                'property_id'               => $properties['BIE-0001']->id,
                'lease_id'                  => $leases['CON-0001']->id,
                'tenant_id'                 => $tenants['LOC-0001']->id,
                'title'                     => 'Dysfonctionnement Climatiseur Salon',
                'description'               => 'Le climatiseur du grand salon ne souffle plus d\'air frais.',
                'priority'                  => 'medium',
                'status'                    => 'closed',
                'repair_details'            => 'Recharge en gaz R410A et nettoyage complet des filtres.',
                'repair_cost'               => 45000.00,
                'tenant_confirmation_note' => 'Climatisation réparée parfaitement, merci !',
                'resolved_at'               => now()->subDays(10),
                'closed_at'                 => now()->subDays(8),
            ]
        );
    }
}
