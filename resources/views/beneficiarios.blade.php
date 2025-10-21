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

                        <!--
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
                        @endif -->

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
                                    <th>Nombre completo</th>
                                    <th>CURP</th>
                                    <th>Fecha Registro</th>
                                    <th>Estudios</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($beneficiarios as $beneficiario)

                                @php
                                $cantidadEstudios = $beneficiario->estudiosSocioeconomicos->count();

                                $tieneEstudiosCompletos = $beneficiario->estudiosSocioeconomicos
                                ->whereNotNull('res_estudio_1')
                                ->whereNotNull('res_estudio_2')
                                ->whereNotNull('res_estudio_3')
                                ->count() > 0;

                                if ($cantidadEstudios > 0) {
                                $rutaEdicion = route('beneficiarios.estudios.editar', [$beneficiario->id, $beneficiario->estudiosSocioeconomicos->first()->id]);
                                $tooltip = "Editar beneficiario y estudios (" . $cantidadEstudios . " estudios)";
                                $badge = '<span class="badge bg-dark ms-1">' . $cantidadEstudios . '</span>';
                                } else {
                                $rutaEdicion = route('beneficiarios.editar', $beneficiario->id);
                                $tooltip = "Editar beneficiario (sin estudios)";
                                $badge = '';
                                }
                                @endphp
                                <tr>
                                    <td>{{ $beneficiario->id }}</td>

                                    <td>
                                        <span class="view-details-name"
                                            style="cursor: pointer; text-decoration: none; font-weight: 500;"
                                            data-id="{{ $beneficiario->id }}"
                                            data-nombres="{{ $beneficiario->nombres }}"
                                            data-primer_apellido="{{ $beneficiario->primer_apellido }}"
                                            data-segundo_apellido="{{ $beneficiario->segundo_apellido }}"
                                            data-curp="{{ $beneficiario->curp }}"
                                            data-fecha_nac="{{ $beneficiario->fecha_nac }}"
                                            data-estado_nac="{{ $beneficiario->estado->nombre ?? 'N/A' }}"
                                            data-sexo="{{ $beneficiario->sexo }}"
                                            data-discapacidad="{{ $beneficiario->discapacidad ? 1 : 0 }}"
                                            data-indigena="{{ $beneficiario->indigena ? 1 : 0 }}"
                                            data-maya_hablante="{{ $beneficiario->maya_hablante ? 1 : 0 }}"
                                            data-afromexicano="{{ $beneficiario->afromexicano ? 1 : 0 }}"
                                            data-estado_civil="{{ $beneficiario->estado_civil }}"
                                            data-ocupacion="{{ $beneficiario->ocupacion->ocupacion ?? 'N/A' }}"
                                            data-created="{{ $beneficiario->created_at }}"
                                            data-updated="{{ $beneficiario->updated_at }}"
                                            title="Click para ver detalles">
                                            {{ $beneficiario->nombres }} {{ $beneficiario->primer_apellido }} {{ $beneficiario->segundo_apellido }}
                                        </span>
                                    </td>
                                    <td>{{ $beneficiario->curp }}</td>
                                    <td>{{ $beneficiario->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $cantidadEstudios }}</td>
                                    <td>

                                        @can('editar beneficiarios')
                                        <a href="{{ route('estudios.create', $beneficiario->id) }}"
                                            class="btn btn-sm btn-primary"
                                            data-bs-toggle="tooltip"
                                            title="Crear estudio socioeconómico">
                                            <i class="bi bi-clipboard-plus"></i>
                                        </a>

                                        <a href="{{ $rutaEdicion }}"
                                            class="btn btn-sm btn-warning"
                                            data-bs-toggle="tooltip"
                                            title="{{ $tooltip }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @endcan

                                        @can('eliminar beneficiarios')
                                        <button
                                            class="btn btn-sm btn-danger delete-btn"
                                            data-id="{{ $beneficiario->id }}"
                                            data-nombres="{{ $beneficiario->nombres }}"
                                            data-apellidos="{{ trim($beneficiario->primer_apellido . ' ' . $beneficiario->segundo_apellido) }}"
                                            data-primer_apellido="{{ $beneficiario->primer_apellido }}"
                                            data-segundo_apellido="{{ $beneficiario->segundo_apellido }}"
                                            data-bs-toggle="tooltip"
                                            title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endcan

                                        @if($tieneEstudiosCompletos)
                                        <button class="btn btn-sm btn-purple view-resultados-btn"
                                            data-beneficiario-id="{{ $beneficiario->id }}"
                                            data-bs-toggle="tooltip"
                                            title="Ver resultados completos de estudios">
                                            <i class="bi bi-graph-up"></i>
                                        </button>
                                        @else
                                        <button class="btn btn-sm btn-purple" disabled
                                            data-bs-toggle="tooltip"
                                            title="No hay estudios completos para mostrar">
                                            <i class="bi bi-graph-up"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay beneficiarios registrados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

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

<!-- Incluir modales separados -->
@include('modals.view-beneficiario')
@include('modals.create-beneficiario')
@include('modals.edit-beneficiario')
@include('modals.delete-beneficiario')
@include('modals.resultados-estudios')

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

    #curp-status .text-danger,
    #curp-status .text-success {
        font-weight: 500;
    }

    #create_curp_confirm:disabled {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    .fa-times-circle,
    .fa-check-circle {
        margin-right: 5px;
    }

    .btn-purple {
        background: linear-gradient(135deg, #6f42c1 0%, #8a63d2 100%);
        border: none;
        color: white;
    }

    .btn-purple:hover {
        background: linear-gradient(135deg, #5a359c 0%, #7a52c4 100%);
        color: white;
    }

    .btn-purple:disabled {
        background: #6c757d;
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<!-- Incluir los archivos separados -->
@include('scripts.beneficiarios-main')
@include('scripts.beneficiarios-validation')
@include('scripts.beneficiarios-modals')
@include('scripts.resultados-estudios')

@endsection