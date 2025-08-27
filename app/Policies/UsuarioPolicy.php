<?php

namespace App\Policies;

use App\Models\Usuario;

class UsuarioPolicy
{
    public function viewAny(Usuario $u): bool { return $u->tienePermiso('usuarios.ver'); }
    public function view(Usuario $u, Usuario $m): bool { return $u->tienePermiso('usuarios.ver'); }
    public function create(Usuario $u): bool { return $u->tienePermiso('usuarios.crear'); }
    public function update(Usuario $u, Usuario $m): bool { return $u->tienePermiso('usuarios.editar'); }
    public function delete(Usuario $u, Usuario $m): bool { return $u->tienePermiso('usuarios.eliminar'); }
    public function restore(Usuario $u, Usuario $m): bool { return $u->tienePermiso('usuarios.restaurar'); }
    public function forceDelete(Usuario $u, Usuario $m): bool { return $u->tienePermiso('usuarios.eliminar.forzado'); }
}
