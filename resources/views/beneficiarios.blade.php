@extends('layouts.app')

@section('title', 'Beneficiarios')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Padrón de Beneficiarios</h2>
                        <div>
                            <a href="{{ route('administrador.importar_beneficiarios') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-upload"></i> Importar
                            </a>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createBeneficiarioModal">
                                <i class="bi bi-plus-lg"></i> Nuevo
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Barra de búsqueda y filtros -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form action="{{ route('beneficiarios') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="curp" class="form-control" placeholder="Buscar por CURP..." value="{{ request('curp') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="bi bi-search"></i> Buscar
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-info">Total: {{ $beneficiarios->total() }} registros</span>
                        </div>
                    </div>

                    <!-- Mensajes de alerta -->
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>¡Éxito!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error:</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Tabla de beneficiarios -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>CURP</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($beneficiarios as $beneficiario)
                                <tr>
                                    <td>{{ $beneficiario->id }}</td>
                                    <td>{{ $beneficiario->nombres }}</td>
                                    <td>{{ $beneficiario->apellidos }}</td>
                                    <td>{{ $beneficiario->curp }}</td>
                                    <td>{{ $beneficiario->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-btn" data-id="{{ $beneficiario->id }}" data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $beneficiario->id }}" data-bs-toggle="tooltip" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No hay beneficiarios registrados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $beneficiarios->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalles -->
<div class="modal fade" id="viewBeneficiarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Beneficiario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> <span id="view-id"></span></p>
                        <p><strong>Nombres:</strong> <span id="view-nombres"></span></p>
                        <p><strong>Apellidos:</strong> <span id="view-apellidos"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>CURP:</strong> <span id="view-curp"></span></p>
                        <p><strong>Fecha Registro:</strong> <span id="view-created"></span></p>
                        <p><strong>Última Actualización:</strong> <span id="view-updated"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar beneficiario -->
<div class="modal fade" id="createBeneficiarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Crear Nuevo Beneficiario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="beneficiarioForm">
                @csrf
                <div id="formMethod"></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombres" class="form-label">Nombres *</label>
                        <input type="text" class="form-control" id="nombres" name="nombres" required>
                    </div>
                    <div class="mb-3">
                        <label for="apellidos" class="form-label">Apellidos *</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                    </div>
                    <div class="mb-3">
                        <label for="curp" class="form-label">CURP *</label>
                        <input type="text" class="form-control" id="curp" name="curp" required maxlength="18">
                        <div class="form-text">18 caracteres exactos</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">

<style>
    .table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    }

    .table-responsive {
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.5em 0.75em;
    }
</style>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // URL base para las peticiones AJAX
        const baseUrl = '/beneficiarios';

        // Manejar clic en botones de ver
        document.querySelectorAll('.view-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                fetch(`${baseUrl}/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('view-id').textContent = data.data.id;
                        document.getElementById('view-nombres').textContent = data.data.nombres;
                        document.getElementById('view-apellidos').textContent = data.data.apellidos;
                        document.getElementById('view-curp').textContent = data.data.curp;
                        document.getElementById('view-created').textContent = new Date(data.data.created_at).toLocaleString();
                        document.getElementById('view-updated').textContent = new Date(data.data.updated_at).toLocaleString();

                        const viewModal = new bootstrap.Modal(document.getElementById('viewBeneficiarioModal'));
                        viewModal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al cargar los datos del beneficiario');
                    });
            });
        });

        // Manejar clic en botones de editar
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                fetch(`${baseUrl}/${id}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('modalTitle').textContent = 'Editar Beneficiario';
                        document.getElementById('nombres').value = data.data.nombres;
                        document.getElementById('apellidos').value = data.data.apellidos;
                        document.getElementById('curp').value = data.data.curp;

                        // Cambiar el formulario para edición
                        const form = document.getElementById('beneficiarioForm');
                        form.action = `${baseUrl}/${id}`;
                        document.getElementById('formMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';

                        const editModal = new bootstrap.Modal(document.getElementById('createBeneficiarioModal'));
                        editModal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al cargar los datos del beneficiario');
                    });
            });
        });

        // Resetear formulario cuando se abre para crear nuevo
        document.getElementById('createBeneficiarioModal').addEventListener('show.bs.modal', function() {
            document.getElementById('modalTitle').textContent = 'Crear Nuevo Beneficiario';
            document.getElementById('beneficiarioForm').reset();
            document.getElementById('beneficiarioForm').action = baseUrl;
            document.getElementById('formMethod').innerHTML = '';
        });

        // Manejar envío del formulario
        document.getElementById('beneficiarioForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const url = this.action;
            const method = this.querySelector('input[name="_method"]') ? this.querySelector('input[name="_method"]').value : 'POST';

            fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Error desconocido'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al procesar la solicitud');
                });
        });
    });
</script>
@endsection