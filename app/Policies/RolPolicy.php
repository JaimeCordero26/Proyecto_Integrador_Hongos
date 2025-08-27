<?php

namespace App\Policies;

use App\Models\Rol;
use App\Models\Usuario;

class RolPolicy
{
    public function viewAny(Usuario $u): bool { return $u->tienePermiso('roles.ver'); }
    public function view(Usuario $u, Rol $m): bool { return $u->tienePermiso('roles.ver'); }
    public function create(Usuario $u): bool { return $u->tienePermiso('roles.crear'); }
    public function update(Usuario $u, Rol $m): bool { return $u->tienePermiso('roles.editar'); }
    public function delete(Usuario $u, Rol $m): bool { return $u->tienePermiso('roles.eliminar'); }
    public function restore(Usuario $u, Rol $m): bool { return $u->tienePermiso('roles.restaurar'); }
    public function forceDelete(Usuario $u, Rol $m): bool { return $u->tienePermiso('roles.eliminar.forzado'); }
}
