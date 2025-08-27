<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('seguridad.roles')->updateOrInsert(
            ['nombre_rol' => 'SUPERADMIN'],
            ['descripcion_rol' => 'Acceso total']
        );

        DB::table('seguridad.roles')->updateOrInsert(
            ['nombre_rol' => 'ADMIN'],
            ['descripcion_rol' => 'Administrador']
        );

        $permisosBase = [
            'usuarios.ver','usuarios.crear','usuarios.editar','usuarios.eliminar',
            'roles.ver','roles.crear','roles.editar','roles.eliminar',
            'permisos.ver','permisos.crear','permisos.editar','permisos.eliminar',
        ];

        foreach ($permisosBase as $p) {
            DB::table('seguridad.permisos')->updateOrInsert(
                ['nombre_permiso' => $p],
                ['descripcion' => $p]
            );
        }

        $rolesMap = DB::table('seguridad.roles')->pluck('rol_id', 'nombre_rol')->all();
        $permisosMap = DB::table('seguridad.permisos')->pluck('permiso_id', 'nombre_permiso')->all();

        $adminPerms = array_keys($permisosMap);

        foreach ($adminPerms as $permName) {
            DB::table('seguridad.roles_permisos')->updateOrInsert(
                ['rol_id' => $rolesMap['ADMIN'] ?? null, 'permiso_id' => $permisosMap[$permName] ?? null],
                []
            );
        }
    }
}
