@can('editar beneficiarios')
@extends('layouts.app')

@section('title', 'Acceder Estudio Socioeconómico Vinculado')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="bi bi-share me-2"></i>
                            Acceder Estudio Socioeconómico Vinculado
                        </h3>
                        <a href="{{ route('beneficiarios') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="alert alert-warning mb-4">
                        <h5 class="alert-heading">
                            <i class="bi bi-person-check me-2"></i>Usted está accediendo como beneficiario vinculado
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Nombre:</strong>
                                    {{ $beneficiarioVinculado->nombres }} {{ $beneficiarioVinculado->primer_apellido }} {{ $beneficiarioVinculado->segundo_apellido }}
                                </p>
                                <p class="mb-1"><strong>CURP:</strong> {{ $beneficiarioVinculado->curp }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Estudio vinculado de:</strong>
                                    {{ $beneficiarioPrincipal->nombres }} {{ $beneficiarioPrincipal->primer_apellido }}
                                </p>
                                <p class="mb-1"><strong>Folio del estudio:</strong> {{ $estudio->folio }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-pencil-square me-2"></i>
                                Editar Sus Datos Personales
                            </h5>
                        </div>
                        <div class="card-body">

                            @include('estudios.paginas.beneficiario-edit', [
                                'beneficiario' => $beneficiarioVinculado,
                                'estudio' => $estudio,
                                'esVinculado' => true,
                                'estados' => $estados,
                                'ocupaciones' => $ocupaciones,
                                'municipios' => $municipios,
                                'parentescos' => $parentescos
                            ])
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-card-checklist me-2"></i>
                                    Estudio Socioeconómico (Vista)
                                </h5>
                                <span class="badge bg-warning">
                                    <i class="bi bi-eye"></i> Solo lectura
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><strong>Folio</strong></label>
                                    <p class="form-control-plaintext border-bottom">{{ $estudio->folio }}</p>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><strong>Fecha de Solicitud</strong></label>
                                    <p class="form-control-plaintext border-bottom">
                                        {{ $estudio->fecha_solicitud->format('d/m/Y') }}
                                    </p>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><strong>Región</strong></label>
                                    <p class="form-control-plaintext border-bottom">
                                        {{ $estudio->region->nombre_region ?? 'N/A' }}
                                    </p>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Tipo de Solicitud</strong></label>
                                    <p class="form-control-plaintext border-bottom">
                                        {{ $estudio->solicitud->nombre_solicitud ?? 'N/A' }}
                                    </p>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Programa</strong></label>
                                    <p class="form-control-plaintext border-bottom">
                                        {{ $estudio->programa->nombre_programa ?? 'N/A' }}
                                    </p>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Tipo de Programa</strong></label>
                                    <p class="form-control-plaintext border-bottom">
                                        {{ $estudio->tipoPrograma->nombre_tipo_programa ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-pills nav-justified mb-4" id="estudioTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="paso1-tab" data-bs-toggle="pill" data-bs-target="#paso1" type="button" role="tab">
                                <i class="bi bi-1-circle me-1"></i>Paso 1 - Integrantes
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="paso2-tab" data-bs-toggle="pill" data-bs-target="#paso2" type="button" role="tab">
                                <i class="bi bi-2-circle me-1"></i>Paso 2 - Evaluación
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="paso3-tab" data-bs-toggle="pill" data-bs-target="#paso3" type="button" role="tab">
                                <i class="bi bi-3-circle me-1"></i>Paso 3 - Necesidades
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="estudioTabsContent">
                        <div class="tab-pane fade show active" id="paso1" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Integrantes del Hogar</h5>
                                </div>
                                <div class="card-body">
                                    @if($estudio->integrantesHogar->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Parentesco</th>
                                                        <th>Edad</th>
                                                        <th>Escolaridad</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($estudio->integrantesHogar as $integrante)
                                                    <tr>
                                                        <td>{{ $integrante->nombres }} {{ $integrante->primer_apellido }}</td>
                                                        <td>{{ $integrante->parentesco->descripcion ?? 'N/A' }}</td>
                                                        <td>{{ $integrante->edad }}</td>
                                                        <td>{{ $integrante->escolaridad->descripcion ?? 'N/A' }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center">No hay integrantes del hogar registrados.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="paso2" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-house me-2"></i>Evaluación de Vivienda</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Tipo de piso:</strong> {{ $estudio->tipo_piso ?? 'N/A' }}</p>
                                            <p><strong>Tipo de techo:</strong> {{ $estudio->tipo_techo ?? 'N/A' }}</p>
                                            <p><strong>Agua para alimentos:</strong> {{ $estudio->agua_alimentos ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Medio de cocina:</strong> {{ $estudio->medio_cocina ?? 'N/A' }}</p>
                                            <p><strong>Vivienda:</strong> {{ $estudio->vivienda ?? 'N/A' }}</p>
                                            <p><strong>Servicio sanitario:</strong> {{ $estudio->servicio_sanitario ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="paso3" role="tabpanel">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-clipboard-heart me-2"></i>Necesidades Alimentarias</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Preocupación por alimentos:</strong> 
                                                {{ $estudio->preocupa_sin_alimentos ? 'Sí' : 'No' }}
                                            </p>
                                            <p><strong>Alimentos no alcanzaron:</strong> 
                                                {{ $estudio->alimentos_no_alcanzaron ? 'Sí' : 'No' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Dieta poco variada adultos:</strong> 
                                                {{ $estudio->dieta_poco_variada_adultos ? 'Sí' : 'No' }}
                                            </p>
                                            <p><strong>Adultos comieron menos:</strong> 
                                                {{ $estudio->adultos_comieron_menos ? 'Sí' : 'No' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('estudios.paginas.resultado_estudio', ['estudio' => $estudio])

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('beneficiarios') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver al listado
                        </a>
                        
                        @can('editar beneficiarios')
                        <a href="{{ route('beneficiarios.estudios.editar', [
                            'beneficiario' => $beneficiarioPrincipal->id, 
                            'estudio' => $estudio->id
                        ]) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square"></i> Ir al estudio principal
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #222222ff 0%, #222222ff 100%);
        border: none;
    }

    .nav-pills .nav-link {
        color: #495057;
        border: 1px solid #dee2e6;
        margin: 0 2px;
    }
    .alert-warning {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 1px solid #ffeaa7;
    }

    .form-control-plaintext {
        padding: 0.375rem 0;
        min-height: 38px;
    }

    .badge.bg-warning {
        color: #000;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Vista de estudio vinculado cargada - Modo solo lectura');
    });
</script>
@endsection
@endcan