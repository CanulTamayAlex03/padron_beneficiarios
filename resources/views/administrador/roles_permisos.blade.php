@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Roles y Permisos</h1>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-shield-alt me-1"></i>
                    Lista de Roles
                </div>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#crearRolModal">
                    <i class="fas fa-plus me-1"></i> Nuevo Rol
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Rol</th>
                            <th>Permisos</th>
                            <th style="width: 12%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $rol)
                        <tr>
                            <td>{{ $rol->id }}</td>
                            <td>{{ $rol->name }}</td>
                            <td>
                                @if($rol->permissions->count() > 0)
                                @foreach($rol->permissions as $permiso)
                                <span class="badge bg-success text-light mb-1">{{ $permiso->name }}</span>
                                @endforeach
                                @else
                                <span class="text-muted">Sin permisos</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-warning btn-sm editar-rol"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarRolModal"
                                        data-id="{{ $rol->id }}">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Rol -->
<div class="modal fade" id="crearRolModal" tabindex="-1" aria-labelledby="crearRolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="crearRolModalLabel">Crear Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Rol</label>
                        <input type="text" class="form-control" id="nombre" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permisos</label>
                        <div class="border p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                            <div class="row">
                                @foreach($permisos as $permiso)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            name="permissions[]"
                                            value="{{ $permiso->name }}"
                                            id="permiso_{{ $permiso->id }}">
                                        <label class="form-check-label" for="permiso_{{ $permiso->id }}">
                                            {{ $permiso->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Rol -->
<div class="modal fade" id="editarRolModal" tabindex="-1" aria-labelledby="editarRolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editarRolForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="rol_id" id="edit_rol_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarRolModalLabel">Editar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre del Rol</label>
                        <input type="text" class="form-control" id="edit_nombre" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permisos</label>
                        <div class="border p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                            <div class="row" id="permisosContainer">
                                <div class="col-12 text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando permisos...</span>
                                    </div>
                                    <p class="mt-2 small">Cargando permisos...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        console.log('Script de roles cargado - MODO DEBUG EXTREMO');

        // Debug: Verificar que los elementos existen
        console.log('Botones editar-rol encontrados:', $('.editar-rol').length);
        console.log('Modal editarRolModal existe:', $('#editarRolModal').length > 0);
        console.log('Container permisosContainer existe:', $('#permisosContainer').length > 0);

        // Mostrar modal de edición con AJAX
        $(document).on('click', '.editar-rol', function() {
            const id = $(this).data('id');
            console.log('CLICK DETECTADO - Editando rol ID:', id);
            console.log('Texto del botón:', $(this).text());
            loadRoleData(id);
        });

        function loadRoleData(id) {
            console.log('INICIANDO loadRoleData con ID:', id);

            // Mostrar spinner de carga
            $('#permisosContainer').html(`
            <div class="col-12 text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando permisos...</span>
                </div>
                <p class="mt-2 small">Cargando datos...</p>
            </div>
        `);

            console.log('Spinner mostrado, abriendo modal...');

            // Mostrar el modal PRIMERO
            $('#editarRolModal').modal('show');

            console.log('Modal mostrado, haciendo AJAX...');

            // Hacer petición AJAX para obtener los datos
            $.ajax({
                url: '/roles/' + id + '/ajax-edit',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    console.log('AJAX beforeSend - Petición enviada');
                },
                success: function(response) {
                    console.log('AJAX SUCCESS - Datos recibidos:', response);
                    console.log('Rol name:', response.rol.name);
                    console.log('Permisos del rol:', response.rol.permissions.length);
                    console.log('Todos los permisos:', response.permisos.length);

                    if (response.rol) {
                        $('#edit_rol_id').val(response.rol.id);
                        $('#edit_nombre').val(response.rol.name);

                        // CORRECCIÓN IMPORTANTE: Usar la ruta correcta
                        $('#editarRolForm').attr('action', '/roles/' + response.rol.id);
                        console.log('Form action actualizado:', $('#editarRolForm').attr('action'));

                        // Construir los checkboxes de permisos
                        let permissionsHtml = '';
                        if (response.permisos && response.permisos.length > 0) {
                            response.permisos.forEach(function(perm) {
                                let isChecked = false;
                                if (response.rol.permissions) {
                                    isChecked = response.rol.permissions.some(function(p) {
                                        return p.id === perm.id;
                                    });
                                }

                                permissionsHtml += `
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="edit_perm_${perm.id}" 
                                               name="permissions[]" 
                                               value="${perm.id}"
                                               ${isChecked ? 'checked' : ''}>
                                        <label for="edit_perm_${perm.id}" class="form-check-label">
                                            ${perm.name}
                                        </label>
                                    </div>
                                </div>
                            `;
                            });
                        } else {
                            permissionsHtml = '<div class="col-12 text-center text-muted py-3">No hay permisos disponibles</div>';
                        }

                        console.log('Insertando HTML en permisosContainer...');
                        $('#permisosContainer').html(permissionsHtml);
                        console.log('HTML insertado correctamente');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX ERROR:', xhr.status, xhr.statusText);
                    console.error('Error:', error);
                    console.error('Response:', xhr.responseText);

                    $('#permisosContainer').html(`
                    <div class="col-12 text-center text-danger py-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        <p>Error al cargar los datos</p>
                        <small>Status: ${xhr.status} - ${error}</small>
                    </div>
                `);
                },
                complete: function() {
                    console.log('AJAX complete - Petición finalizada');
                }
            });
        }

        // Eventos del modal para debugging
        $('#editarRolModal').on('show.bs.modal', function() {
            console.log('Modal EVENT: show.bs.modal');
        });

        $('#editarRolModal').on('shown.bs.modal', function() {
            console.log('Modal EVENT: shown.bs.modal - Completamente visible');
        });

        $('#editarRolModal').on('hidden.bs.modal', function() {
            console.log('Modal EVENT: hidden.bs.modal - Cerrado');
            $('#permisosContainer').html(`
            <div class="col-12 text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando permisos...</span>
                </div>
            </div>
        `);
        });

        window.debugLoadRole = function(id) {
            console.log('DEBUG MANUAL - Cargando rol ID:', id);
            loadRoleData(id);
        };

        console.log('Todos los eventos registrados - Script listo');
    });
</script>
@endpush