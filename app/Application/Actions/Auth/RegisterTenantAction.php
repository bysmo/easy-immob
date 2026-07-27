<?php

namespace App\Application\Actions\Auth;

use App\Domain\Tenant\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterTenantAction
{
    /**
     * Valide et crée un compte utilisateur Locataire citoyen et son profil Tenant.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email'),
            ],
            'phone'      => ['nullable', 'string', 'max:255'],
            'password'   => ['required', 'confirmed', Password::min(8)],
        ])->validate();

        return DB::transaction(function () use ($input) {
            $fullName = trim("{$input['first_name']} {$input['last_name']}");

            $user = User::create([
                'agency_id' => null,
                'name'      => $fullName,
                'email'     => $input['email'],
                'phone'     => $input['phone'] ?? null,
                'password'  => Hash::make($input['password']),
            ]);

            $user->assignRole('Locataire');

            // Générer un code locataire unique à 6 chiffres (ex: LOC-849201)
            do {
                $code = 'LOC-' . str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            } while (Tenant::withoutGlobalScopes()->where('reference', $code)->exists());

            Tenant::create([
                'agency_id'  => null,
                'user_id'    => $user->id,
                'reference'  => $code,
                'first_name' => $input['first_name'],
                'last_name'  => $input['last_name'],
                'email'      => $input['email'],
                'phone'      => $input['phone'] ?? null,
                'status'     => 'active',
            ]);

            return $user;
        });
    }
}
