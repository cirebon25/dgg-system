<?php

namespace App\Filament\Traits;

trait HasRoleAccess
{
    protected static array $allowedRoles = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        if (empty(static::$allowedRoles)) return true;
        return $user->hasRole(static::$allowedRoles);
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }
    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canEdit($record): bool
    {
        return static::canAccess();
    }
    public static function canDelete($record): bool
    {
        return static::canAccess();
    }
    public static function canView($record): bool
    {
        return static::canAccess();
    }
}
