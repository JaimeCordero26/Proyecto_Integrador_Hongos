<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Filament\Models\Contracts\FilamentUser;

class Usuario extends Authenticatable implements FilamentUser
{
    use Notifiable;

    protected $table = 'seguridad.usuarios';
    protected $primaryKey = 'usuario_id';
    public $timestamps = false;

    protected $fillable = [
        'rol_id',
        'nombre_completo',
        'email',
        'password',
        'activo',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function getAuthIdentifierName()
    {
        return 'email';
    }

    public function getAuthIdentifier()
    {
        return $this->email;
    }

    public function getAuthPassword()
    {
        $hash = $this->password_hash;
        if (substr($hash, 0, 4) === '$2b$') {
            $hash = '$2y$' . substr($hash, 4);
        }
        return $hash;
    }

    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
    }

    public function getRememberTokenName()
    {
        return null;
    }

    public function getNameAttribute()
    {
        return $this->nombre_completo;
    }

    public function isActive()
    {
        return $this->activo;
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'rol_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function tieneRol(string $nombreRol): bool
    {
        return optional($this->rol)->nombre_rol === $nombreRol;
    }

    public function permisosNombres(): array
    {
        return Cache::remember(
            "u:{$this->getKey()}:permisos",
            now()->addMinutes(10),
            function () {
                if (!$this->rol) return [];
                return Permiso::query()
                    ->select('seguridad.permisos.nombre_permiso')
                    ->join('seguridad.roles_permisos as rp', 'rp.permiso_id', '=', 'seguridad.permisos.permiso_id')
                    ->where('rp.rol_id', $this->rol_id)
                    ->pluck('nombre_permiso')
                    ->map(fn ($p) => (string) $p)
                    ->all();
            }
        );
    }

    public function tienePermiso(string $permiso): bool
    {
        if ($this->tieneRol('SUPERADMIN')) return true;
        $perms = $this->permisosNombres();
        if (str_contains($permiso, '*')) {
            $prefix = rtrim($permiso, '*');
            return collect($perms)->contains(fn ($p) => str_starts_with($p, $prefix));
        }
        return in_array($permiso, $perms, true);
    }

    public function invalidatePermisosCache(): void
    {
        Cache::forget("u:{$this->getKey()}:permisos");
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password_hash'] = Hash::make($value);
    }

    public function getPasswordAttribute()
    {
        return $this->getAuthPassword();
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->activo === true;
    }


    public function getFilamentName(): string
    {
        return $this->nombre_completo;
    }
}
