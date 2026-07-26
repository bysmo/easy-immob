<?php

namespace App\Application\Actions\Auth;

use App\Domain\Agency\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class RegisterAgencyAction implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered agency and its first administrator.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'agency_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ])->validate();

        return DB::transaction(function () use ($input) {
            $agency = Agency::create([
                'name' => $input['agency_name'],
                'email' => $input['email'],
                'status' => 'active',
            ]);

            $user = User::create([
                'agency_id' => $agency->id,
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            $user->assignRole('Administrateur');

            return $user;
        });
    }
}
