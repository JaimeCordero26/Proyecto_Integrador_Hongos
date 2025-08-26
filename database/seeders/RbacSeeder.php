<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos base
        $permisos = [
            ['nombre_permiso' => 'usuarios.ver',      'descripcion' => 'Ver usuarios'],
            ['nombre_permiso' => 'usuarios.crear',    'descripcion' => 'Crear usuarios'],
            ['nombre_permiso' => 'usuarios.editar',   'descripcion' => 'Editar usuarios'],
            ['nombre_permiso' => 'usuarios.eliminar', 'descripcion' => 'Eliminar usuarios'],

            ['nombre_permiso' => 'reservas.ver',      'descripcion' => 'Ver reservas'],
            ['nombre_permiso' => 'reservas.crear',    'descripcion' => 'Crear reservas'],
            ['nombre_permiso' => 'reservas.editar',   'descripcion' => 'Editar reservas'],
            ['nombre_permiso' => 'reservas.cancelar', 'descripcion' => 'Cancelar reservas'],

            ['nombre_permiso' => 'reportes.ver',      'descripcion' => 'Ver reportes'],
        ];

        foreach ($permisos as $p) {
            DB::table('seguridad.permisos')->updateOrInsert(
                ['nombre_permiso' => $p['nombre_permiso']],
                ['descripcion' => $p['descripcion']]
            );
        }

        // Roles base
        $roles = [
            ['rol_id' => 1, 'nombre_rol' => 'SUPERADMIN', 'descripcion_rol' => 'Acceso total'],
            ['rol_id' => 2, 'nombre_rol' => 'ADMIN',      'descripcion_rol' => 'Administración general'],
            ['rol_id' => 3, 'nombre_rol' => 'OPERADOR',   'descripcion_rol' => 'Operación diaria'],
            ['rol_id' => 4, 'nombre_rol' => 'INVITADO',   'descripcion_rol' => 'Acceso lectura'],
        ];

        foreach ($roles as $r) {
            DB::table('seguridad.roles')->updateOrInsert(
                ['rol_id' => $r['rol_id']],
                ['nombre_rol' => $r['nombre_rol'], 'descripcion_rol' => $r['descripcion_rol']]
            );
        }

        // Asignaciones rol-permiso (comodines)
        $map = [
            1 => ['*'], // SUPERADMIN → todos por Gate::before
            2 => ['usuarios.*', 'reservas.*', 'reportes.ver'],
            3 => ['reservas.ver', 'reservas.crear', 'reservas.editar'],
            4 => ['reservas.ver', 'reportes.ver'],
        ];

        $allPerms = DB::table('seguridad.permisos')->pluck('permiso_id', 'nombre_permiso')->all();

        foreach ($map as $rolId => $perms) {
            // Limpia asignaciones previas del rol
            DB::table('seguridad.roles_permisos')->where('rol_id', $rolId)->delete();

            $toAttach = [];
            foreach ($perms as $needle) {
                if ($needle === '*') {
                    $toAttach = array_values($allPerms);
                    break;
                }
                if (str_ends_with($needle, '.*')) {
                    $prefix = substr($needle, 0, -2);
                    foreach ($allPerms as $name => $pid) {
                        if (str_starts_with($name, $prefix.'.')) $toAttach[] = $pid;
                    }
                } else {
                    if (isset($allPerms[$needle])) $toAttach[] = $allPerms[$needle];
                }
            }

            $toAttach = array_values(array_unique($toAttach));
            foreach ($toAttach as $pid) {
                DB::table('seguridad.roles_permisos')->updateOrInsert([
                    'rol_id' => $rolId,
                    'permiso_id' => $pid,
                ]);
            }
        }

        // Asigna un usuario de ejemplo al rol ADMIN (ajusta email)
        $u = DB::table('seguridad.usuarios')->where('email', 'admin@demo.local')->first();
        if ($u) {
            DB::table('seguridad.usuarios')->where('usuario_id', $u->usuario_id)->update(['rol_id' => 2]);
        }
    }
}
