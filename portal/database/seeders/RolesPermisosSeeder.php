<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Roles y permisos según la matriz §26 del requerimiento (docs/03 §1).
 */
class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permisos = [
            'alumnos.ver', 'alumnos.editar', 'alumnos.bloquear_edicion',
            'documentacion.validar', 'formatos.descargar',
            'csv.exportar', 'csv.importar',
            'omr.procesar', 'omr.corregir',
            'resultados.cargar',
            'dashboard.academico', 'dashboard.registros',
            'grupos.asignar', 'avisos.publicar',
            'catalogos.administrar', 'modulos.publicar',
            'usuarios.administrar',
        ];

        foreach ($permisos as $permiso) {
            Permission::findOrCreate($permiso);
        }

        $matriz = [
            'control_escolar' => [
                'alumnos.ver', 'alumnos.editar', 'alumnos.bloquear_edicion',
                'documentacion.validar', 'formatos.descargar',
                'csv.exportar', 'csv.importar',
                'dashboard.registros', 'grupos.asignar',
                'avisos.publicar', 'modulos.publicar',
            ],
            'coordinacion' => [
                'alumnos.ver', 'csv.exportar', 'csv.importar',
                'omr.corregir', 'resultados.cargar',
                'dashboard.academico', 'dashboard.registros',
                'grupos.asignar', 'avisos.publicar',
            ],
            'direccion' => [
                'alumnos.ver', 'csv.exportar',
                'dashboard.academico', 'dashboard.registros',
                'avisos.publicar',
            ],
            'tecnico' => [
                'csv.importar', 'omr.procesar', 'omr.corregir',
                'resultados.cargar', 'catalogos.administrar',
            ],
            'docente' => [
                // Fase posterior: dashboard académico de sus grupos.
                'dashboard.academico',
            ],
        ];

        foreach ($matriz as $rol => $permisosRol) {
            Role::findOrCreate($rol)->syncPermissions($permisosRol);
        }

        // Admin: todos los permisos.
        Role::findOrCreate('admin')->syncPermissions($permisos);
    }
}
