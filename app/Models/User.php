<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domain\Owner\Models\Owner;
use App\Domain\Tenant\Models\Tenant;
use App\Support\Tenancy\BelongsToAgency;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['agency_id', 'name', 'email', 'phone', 'avatar_path', 'password', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use BelongsToAgency, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'agency_id',
        'name',
        'email',
        'phone',
        'avatar_path',
        'password',
        'email_verified_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * URL publique de l'avatar de l'utilisateur.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
            return Storage::disk('public')->url($this->avatar_path);
        }

        return null;
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class)->withoutGlobalScopes();
    }

    public function isTenant(): bool
    {
        return $this->hasRole('Locataire');
    }

    public function isOwner(): bool
    {
        return $this->hasRole('Bailleur');
    }

    public function owner(): HasOne
    {
        return $this->hasOne(Owner::class)->withoutGlobalScopes();
    }

    public function isAgencyAdmin(): bool
    {
        return $this->hasRole('Administrateur') || $this->hasRole('Gestionnaire') || $this->hasRole('Agent') || $this->hasRole('Comptable');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin') || $this->can('saas.admin');
    }
}
