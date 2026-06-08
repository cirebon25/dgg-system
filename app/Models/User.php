<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Role Constants ────────────────────────────────────────
    const ROLE_ADMIN    = 'admin';
    const ROLE_TEKNISI  = 'teknisi';
    const ROLE_KEUANGAN = 'keuangan';
    const ROLE_MANAGER  = 'manager';

    // ── Helper Methods ────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTeknisi(): bool
    {
        return $this->role === self::ROLE_TEKNISI;
    }

    public function isKeuangan(): bool
    {
        return $this->role === self::ROLE_KEUANGAN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles);
    }

    // ── Filament Access ───────────────────────────────────────
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // semua user bisa masuk panel
    }
}
