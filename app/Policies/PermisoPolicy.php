<?php

namespace App\Policies;

use App\Models\Permiso;
use App\Models\Usuario;

class PermisoPolicy
{
    public function viewAny(Usuario $u): bool { return $u->tienePermiso('permisos.ver'); }
    public function view(Usuario $u, Permiso $m): bool { return $u->tienePermiso('permisos.ver'); }
    public function create(Usuario $u): bool { return $u->tienePermiso('permisos.crear'); }
    public function update(Usuario $u, Permiso $m): bool { return $u->tienePermiso('permisos.editar'); }
    public function delete(Usuario $u, Permiso $m): bool { return $u->tienePermiso('permisos.eliminar'); }
    public function restore(Usuario $u, Permiso $m): bool { return $u->tienePermiso('permisos.restaurar'); }
    public function forceDelete(Usuario $u, Permiso $m): bool { return $u->tienePermiso('permisos.eliminar.forzado'); }
}
