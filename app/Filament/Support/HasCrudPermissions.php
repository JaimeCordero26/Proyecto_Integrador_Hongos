<?php

namespace App\Filament\Support;

trait HasCrudPermissions
{
    protected static function perm(string $sufijo): string
    {
        $prefix = static::$permPrefix ?? '';
        return $prefix ? ($prefix . '.' . $sufijo) : $sufijo;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->tienePermiso(static::perm('ver')) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->tienePermiso(static::perm('crear')) ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->tienePermiso(static::perm('editar')) ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->tienePermiso(static::perm('eliminar')) ?? false;
    }
}
