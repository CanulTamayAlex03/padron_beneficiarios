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

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    Lista de Usuarios
                </div>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
                    <i class="fas fa-plus me-1"></i> Nuevo Usuario
                </button>
            </div>
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->usuario_id }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->rol->descripcion }}</td>
                            <td>
                                <span class="badge bg-{{ $usuario->estatus ? 'success' : 'secondary' }}">
                                    {{ $usuario->estatus ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-warning btn-sm editar-usuario"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarUsuarioModal"
                                        data-id="{{ $usuario->usuario_id }}"
                                        data-email="{{ $usuario->email }}"
                                        data-rol_id="{{ $usuario->rol_id }}"
                                        data-estatus="{{ (int) $usuario->estatus }}">
                                        <i class="bi bi-pencil"></i>
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

<!-- Modal para crear usuario -->
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('usuarios.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="crearUsuarioModalLabel">Crear Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <label for="rol_id" class="form-label">Rol</label>
                        <select class="form-select" id="rol_id" name="rol_id" required>
                            <option value="">Seleccionar Rol</option>
                            @foreach($roles as $rol)
                            <option value="{{ $rol->rol_id }}">{{ $rol->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 form-check">
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

<!-- Modal para editar usuario -->
<div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('usuarios.update', ['usuario' => $usuario->usuario_id]) }}">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Contraseña (dejar en blanco para no cambiar)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="edit_rol_id" class="form-label">Rol</label>
                        <select class="form-select" id="edit_rol_id" name="rol_id" required>
                            <option value="">Seleccionar Rol</option>
                            @foreach($roles as $rol)
                            <option value="{{ $rol->rol_id }}">{{ $rol->descripcion }}</option>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.querySelectorAll('.editar-usuario').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const email = this.getAttribute('data-email');
            const rol_id = this.getAttribute('data-rol_id');
            const estatus = this.getAttribute('data-estatus');

            document.getElementById('edit_email').value = email;
            document.getElementById('edit_rol_id').value = rol_id;
            document.getElementById('edit_estatus').checked = (estatus === '1');


            console.log('Valores establecidos:', {
                email: email,
                rol_id: rol_id,
                estatus: estatus
            });

            document.getElementById('editarUsuarioForm').action = `/administrador/usuarios/${id}`;
        });
    });
</script>
@endsection
@endsection