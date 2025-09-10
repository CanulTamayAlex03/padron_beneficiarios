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

                        <div>
                            @can('crear beneficiarios')
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createBeneficiarioModal">
                                <i class="bi bi-plus-lg"></i> Nuevo
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <form action="{{ route('beneficiarios') }}" method="GET" class="d-flex">
                                <div class="input-group">
                                    <input type="text" name="curp" class="form-control" placeholder="Buscar por CURP..." value="{{ request('curp') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="bi bi-search"></i> Buscar
                                    </button>
                                    <a href="{{ route('beneficiarios') }}" class="btn btn-outline-danger">
                                        <i class="bi bi-x-circle"></i> Limpiar
                                    </a>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-dark">Total: {{ $beneficiarios->total() }} registros</span>
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
                                        <button class="btn btn-sm btn-primary view-btn" data-id="{{ $beneficiario->id }}" data-bs-toggle="tooltip" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @can('editar beneficiarios')
                                        <button class="btn btn-sm btn-warning edit-btn"
                                            data-id="{{ $beneficiario->id }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editBeneficiarioModal"
                                            data-bs-toggle="tooltip" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        @endcan
                                        @can('eliminar beneficiarios')
                                        <button class="btn btn-sm btn-danger delete-btn"
                                            data-id="{{ $beneficiario->id }}"
                                            data-nombres="{{ $beneficiario->nombres }}"
                                            data-apellidos="{{ $beneficiario->apellidos }}"
                                            data-bs-toggle="tooltip" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endcan
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

                    <!-- Paginación Mejorada -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Mostrando {{ $beneficiarios->firstItem() ?? 0 }} - {{ $beneficiarios->lastItem() ?? 0 }} de {{ $beneficiarios->total() }} registros
                        </div>

                        <nav aria-label="Navegación de beneficiarios">
                            <ul class="pagination mb-0">
                                {{-- Primer página --}}
                                <li class="page-item {{ $beneficiarios->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $beneficiarios->url(1) }}" aria-label="Primera">
                                        <span aria-hidden="true">&laquo;&laquo;</span>
                                    </a>
                                </li>

                                {{-- Página anterior --}}
                                <li class="page-item {{ $beneficiarios->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $beneficiarios->previousPageUrl() }}" aria-label="Anterior">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                {{-- Páginas --}}
                                @php
                                $current = $beneficiarios->currentPage();
                                $last = $beneficiarios->lastPage();
                                $start = max($current - 2, 1);
                                $end = min($start + 4, $last);

                                if ($end - $start < 4) {
                                    $start=max($end - 4, 1);
                                    }
                                    @endphp

                                    @for ($i=$start; $i <=$end; $i++)
                                    <li class="page-item {{ $i == $current ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $beneficiarios->url($i) }}">{{ $i }}</a>
                                    </li>
                                    @endfor

                                    {{-- Página siguiente --}}
                                    <li class="page-item {{ !$beneficiarios->hasMorePages() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $beneficiarios->nextPageUrl() }}" aria-label="Siguiente">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>

                                    {{-- Última página --}}
                                    <li class="page-item {{ !$beneficiarios->hasMorePages() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $beneficiarios->url($last) }}" aria-label="Última">
                                            <span aria-hidden="true">&raquo;&raquo;</span>
                                        </a>
                                    </li>
                            </ul>
                        </nav>

                        <div class="d-none d-md-block">
                            <span class="badge bg-dark">Página {{ $beneficiarios->currentPage() }} de {{ $beneficiarios->lastPage() }}</span>
                        </div>
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

<!-- Modal para crear beneficiario -->
@can('crear beneficiarios')
<div class="modal fade" id="createBeneficiarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Beneficiario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createBeneficiarioForm" action="{{ route('beneficiarios.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_nombres" class="form-label">Nombres *</label>
                        <input type="text" class="form-control" id="create_nombres" name="nombres" required>
                    </div>
                    <div class="mb-3">
                        <label for="create_apellidos" class="form-label">Apellidos *</label>
                        <input type="text" class="form-control" id="create_apellidos" name="apellidos" required>
                    </div>
                    <div class="mb-3">
                        <label for="create_curp" class="form-label">CURP *</label>
                        <input type="text" class="form-control" id="create_curp" name="curp" required maxlength="18">
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
@endcan

<!-- Modal para editar beneficiario -->
@can('editar beneficiarios')
<div class="modal fade" id="editBeneficiarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Beneficiario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBeneficiarioForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nombres" class="form-label">Nombres *</label>
                        <input type="text" class="form-control" id="edit_nombres" name="nombres" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_apellidos" class="form-label">Apellidos *</label>
                        <input type="text" class="form-control" id="edit_apellidos" name="apellidos" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_curp" class="form-label">CURP *</label>
                        <input type="text" class="form-control" id="edit_curp" name="curp" required maxlength="18">
                        <div class="form-text">18 caracteres exactos</div>
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
@endcan

<!-- Modal de confirmación para eliminar -->
@can('eliminar beneficiarios')
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar al beneficiario <strong id="deleteBeneficiarioName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">

<style>
    .table th {
        background: linear-gradient(135deg, #1b1b1bff 0%, #1b1b1bff 100%);
        color: white;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
    }

    .card-header {
        background: linear-gradient(135deg, #1b1b1bff 0%, #1b1b1bff 100%);
        color: white;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1e62c9ff 0%, #1e62c9ff 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2f6bc5ff 0%, #2f6bc5ff 100%);
    }

    .table-responsive {
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.5em 0.75em;
    }

    .pagination {
        margin-bottom: 0;
    }

    .page-item {
        margin: 0 2px;
    }

    .page-link {
        border: none;
    }
</style>

@include('scripts.beneficiarios')
@endsection