@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Usuarios</h1>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Lista de Usuarios
            </div>
            @can('crear usuarios')
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
                <i class="fas fa-plus me-1"></i> Nuevo Usuario
            </button>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            @can('editar usuarios')
                            <th>Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->usuario_id }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @if($usuario->roles->count() > 0)
                                {{ $usuario->getRoleNames()->first() }}
                                @else
                                <span class="text-muted">Sin rol asignado</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $usuario->estatus ? 'success' : 'secondary' }}">
                                    {{ $usuario->estatus ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            @can('editar usuarios')
                            <td class="text-center">
                                <div class="btn-group" role="group">      
                                    <button class="btn btn-warning btn-sm editar-usuario"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarUsuarioModal"
                                        data-id="{{ $usuario->usuario_id }}">
                                        <i class="bi bi-pencil"></i>
                                        Editar
                                    </button>
                                </div>
                            </td>
                            @endcan
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('usuarios.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="crearUsuarioModalLabel">Crear Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="">Seleccionar Rol</option>
                            @foreach($roles as $rol)
                            <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <!-- Campo hidden para valor por defecto -->
                        <input type="hidden" name="estatus" value="0">
                        <input type="checkbox" class="form-check-input" id="estatus" name="estatus" value="1" checked>
                        <label class="form-check-label" for="estatus">Activo</label>
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

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editarUsuarioForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editarUsuarioLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Contraseña (opcional)</label>
                        <input type="password" class="form-control" id="edit_password" name="password" placeholder="Dejar en blanco para no cambiar">
                    </div>
                    <div class="mb-3">
                        <label for="edit_rol" class="form-label">Rol</label>
                        <select id="edit_rol" name="rol" class="form-select" required>
                            @foreach($roles as $rol)
                            <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="hidden" name="estatus" value="0">
                        <input type="checkbox" class="form-check-input" id="edit_estatus" name="estatus" value="1">
                        <label class="form-check-label" for="edit_estatus">Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Debug visual */
    .editar-usuario {
        border: 2px solid red !important;
    }

    #editarUsuarioModal {
        border: 3px solid blue !important;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        console.log('✅ Script de usuarios cargado');

        // Verificar que jQuery esté funcionando
        console.log('jQuery version:', $.fn.jquery);
        console.log('Botones encontrados:', $('.editar-usuario').length);

        // Event delegation para los botones de editar
        $(document).on('click', '.editar-usuario', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            console.log('🟡 Click en botón - ID:', id);
            
            loadUserData(id);
        });

        function loadUserData(id) {
            console.log('🟡 Cargando datos para usuario ID:', id);

            // Mostrar loading
            $('#edit_email').val('Cargando...');
            $('#edit_rol, #edit_estatus').prop('disabled', true);

            $.ajax({
                url: '/administrador/usuarios/' + id + '/ajax',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('✅ Datos recibidos:', response);
                    
                    $('#edit_email').val(response.email);
                    $('#edit_rol').val(response.rol);
                    $('#edit_estatus').prop('checked', Boolean(response.estatus));
                    
                    $('#editarUsuarioForm').attr('action', '/administrador/usuarios/' + id);
                    
                    $('#edit_rol, #edit_estatus').prop('disabled', false);
                    
                    console.log('✅ Formulario actualizado');
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error AJAX:', error);
                    $('#edit_email').val('Error al cargar');
                    alert('Error al cargar datos: ' + error);
                }
            });
        }

        // Limpiar modal al cerrar
        $('#editarUsuarioModal').on('hidden.bs.modal', function() {
            $('#editarUsuarioForm')[0].reset();
            $('#edit_rol, #edit_estatus').prop('disabled', false);
        });
    });
</script>
@endsection