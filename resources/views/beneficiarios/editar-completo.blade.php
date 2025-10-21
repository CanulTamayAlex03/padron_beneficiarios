@can('editar beneficiarios')
@extends('layouts.app')

@section('title', 'Editar Estudio Socioeconómico')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="bi bi-clipboard-data me-2"></i>
                            Editar Estudio Socioeconómico
                        </h3>
                        <a href="{{ route('beneficiarios') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Información del Beneficiario -->
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading">
                            <i class="bi bi-person-circle me-2"></i>Datos del Beneficiario
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Nombre completo:</strong>
                                    {{ $beneficiario->nombres }} {{ $beneficiario->primer_apellido }} {{ $beneficiario->segundo_apellido }}
                                </p>
                                <p class="mb-1"><strong>CURP:</strong> {{ $beneficiario->curp }}</p>
                                <p class="mb-1"><strong>Fecha de nacimiento:</strong>
                                    {{ $beneficiario->fecha_nac->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>ID Beneficiario:</strong> {{ $beneficiario->id }}</p>
                                <p class="mb-1"><strong>Edad:</strong> {{ $beneficiario->fecha_nac->age }} años</p>
                                <p class="mb-0"><strong>Fecha registro:</strong>
                                    {{ $beneficiario->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Inclusión de la sección de editar beneficiarios -->
                    @include('estudios.paginas.beneficiario-edit', ['estudio' => $estudio])

                    <!-- Inclusión de la sección de acompañantes -->
                    @include('estudios.paginas.acompanantes')

                    <!-- UN SOLO FORMULARIO PARA TODO -->
                    <form action="{{ route('estudios.update', $estudio->id) }}" method="POST" id="estudioForm">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="beneficiario_id" value="{{ $beneficiario->id }}">

                        <input type="hidden" name="linea_coneval_id" id="linea_coneval_id_form" 
                            value="{{ $estudio->linea_coneval_id ?? '' }}">
                        <input type="hidden" name="coneval_active" id="coneval_active_form" 
                            value="{{ $estudio->coneval_active ?? '' }}">
                        <input type="hidden" name="servicio_salud_id" id="servicio_salud_id_form" 
                            value="{{ $estudio->servicio_salud_id ?? '' }}">
                        <input type="hidden" name="escolaridad_id" id="escolaridad_id_form" 
                            value="{{ $estudio->escolaridad_id ?? '' }}">

                        <!-- Sección de datos básicos del estudio -->
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="bi bi-card-checklist me-2"></i>
                                        Editar Estudio
                                    </h5>

                                    <!-- Select de navegación entre estudios -->
                                    <div class="d-flex align-items-center">
                                        <span class="text-white me-2">Estudio:</span>
                                        <select class="form-select form-select-sm w-auto" id="selectorEstudios"
                                            onchange="if(this.value) window.location.href = this.value;">
                                            @foreach($estudios as $est)
                                            <option value="{{ route('beneficiarios.estudios.editar', [
                                'beneficiario' => $beneficiario->id, 
                                'estudio' => $est->id
                            ]) }}"
                                                {{ $est->id == $estudio->id ? 'selected' : '' }}>

                                                @if($est->folio)
                                                ({{ $est->folio }})
                                                @endif
                                                - {{ $est->created_at->format('d/m/Y') }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <a href="{{ route('estudios.create', $beneficiario->id) }}"
                                        class="btn btn-success btn-sm ms-2">
                                        <i class="bi bi-plus-circle"></i> Nuevo Estudio
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="folio" class="form-label">Folio </label>
                                        <input type="text" class="form-control" id="folio" name="folio"
                                            value="{{ $estudio->folio }}" readonly>
                                        <div class="form-text">El folio no se puede modificar</div>
                                        @error('folio')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="fecha_solicitud" class="form-label">Fecha de Solicitud *</label>
                                        <input type="date" class="form-control" id="fecha_solicitud" name="fecha_solicitud"
                                            value="{{ $estudio->fecha_solicitud->format('Y-m-d') }}">
                                        @error('fecha_solicitud')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="region_id" class="form-label">Región *</label>
                                        <select class="form-select" id="region_id" name="region_id">
                                            <option value="">Seleccionar región...</option>
                                            @foreach($regiones as $region)
                                            <option value="{{ $region->id }}"
                                                {{ $estudio->region_id == $region->id ? 'selected' : '' }}>
                                                {{ $region->nombre_region }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('region_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="solicitud_id" class="form-label">Tipo de Solicitud *</label>
                                        <select class="form-select" id="solicitud_id" name="solicitud_id">
                                            <option value="">Seleccionar solicitud...</option>
                                            @foreach($solicitudes as $solicitud)
                                            <option value="{{ $solicitud->id }}"
                                                {{ $estudio->solicitud_id == $solicitud->id ? 'selected' : '' }}>
                                                {{ $solicitud->nombre_solicitud }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('solicitud_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="programa_id" class="form-label">Programa *</label>
                                        <select class="form-select" id="programa_id" name="programa_id">
                                            <option value="">Seleccionar programa...</option>
                                            @foreach($programas as $programa)
                                            <option value="{{ $programa->id }}"
                                                {{ $estudio->programa_id == $programa->id ? 'selected' : '' }}
                                                data-tipos='@json($programa->tiposPrograma)'>
                                                {{ $programa->nombre_programa }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('programa_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_programa_id" class="form-label">Tipo de Programa *</label>
                                        <select class="form-select" id="tipo_programa_id" name="tipo_programa_id">
                                            <option value="">Cargando tipos...</option>
                                        </select>
                                        @error('tipo_programa_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('beneficiarios') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-check-circle"></i> Actualizar Estudio
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Navegación por pasos -->
                        <ul class="nav nav-pills nav-justified mb-4" id="estudioTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="paso1-tab" data-bs-toggle="pill" data-bs-target="#paso1" type="button" role="tab">
                                    <i class="bi bi-1-circle me-1"></i>Evaluación Económica y Familiar
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="paso2-tab" data-bs-toggle="pill" data-bs-target="#paso2" type="button" role="tab">
                                    <i class="bi bi-2-circle me-1"></i>Evaluación de espacios y servicios
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="paso3-tab" data-bs-toggle="pill" data-bs-target="#paso3" type="button" role="tab">
                                    <i class="bi bi-3-circle me-1"></i>Evaluación de seguridad alimentaria
                                </button>
                            </li>
                        </ul>

                        <!-- CONTENIDO DE LOS PASOS DENTRO DEL MISMO FORMULARIO -->
                        <div class="tab-content" id="estudioTabsContent">
                            <!-- PASO 1: Integrantes del hogar -->
                            @include('estudios.paginas.estudio_paso1')
            
                            <!-- PASO 2: Evaluación Economica y Familiar -->
                            @include('estudios.paginas.estudio_paso2')

                            <!-- PASO 3: Necesidades y Observaciones -->
                            @include('estudios.paginas.estudio_paso3')
                        </div>
                    </form>
                    @include('estudios.paginas.resultado_estudio')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALES FUERA DEL FORMULARIO PRINCIPAL -->
@include('estudios.familiares-modals.create')

@foreach($beneficiario->familiares as $familiar)
<!-- Incluir modales individuales -->
@include('estudios.familiares-modals.edit', ['familiar' => $familiar])
@include('estudios.familiares-modals.delete', ['familiar' => $familiar])
@endforeach

<!-- MODALES DE INTEGRANTES DEL HOGAR FUERA DEL FORMULARIO -->
@include('estudios.integrantes-hogar-modals.create-modal')
@include('estudios.integrantes-hogar-modals.edit-modal')
@include('estudios.integrantes-hogar-modals.delete-modal')
@endcan

<style>
    fieldset {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    fieldset:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
    }

    legend {
        font-size: 1.1rem;
        color: #222222ff;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-check {
        margin-bottom: 0.5rem;
    }

    .card-header {
        background: linear-gradient(135deg, #222222ff 0%, #222222ff 100%);
    }

    .alert-info {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border: 1px solid #bee5eb;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #222222ff 0%, #222222ff 100%);
        border: none;
    }

    .nav-pills .nav-link {
        color: #495057;
        border: 1px solid #dee2e6;
        margin: 0 2px;
    }

    .card-header {
        background: linear-gradient(135deg, #222222ff 0%, #222222ff 100%);
    }

    .alert-info {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border: 1px solid #bee5eb;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const programaSelect = document.getElementById('programa_id');
        const tipoProgramaSelect = document.getElementById('tipo_programa_id');
        
        function cargarTiposPrograma(programaId, tipoSeleccionado = null) {
            console.log('Cargando tipos para programa:', programaId, 'Tipo seleccionado:', tipoSeleccionado);

            if (!programaId) {
                tipoProgramaSelect.innerHTML = '<option value="">Primero seleccione un programa</option>';
                tipoProgramaSelect.disabled = true;
                return;
            }

            tipoProgramaSelect.innerHTML = '<option value="">Cargando tipos...</option>';

            const programaOption = programaSelect.querySelector(`option[value="${programaId}"]`);

            if (!programaOption) {
                console.error('No se encontró la opción del programa:', programaId);
                tipoProgramaSelect.innerHTML = '<option value="">Error al cargar tipos</option>';
                return;
            }

            const tiposData = programaOption.getAttribute('data-tipos');
            console.log('Datos de tipos:', tiposData);

            if (!tiposData) {
                tipoProgramaSelect.innerHTML = '<option value="">No hay tipos disponibles</option>';
                tipoProgramaSelect.disabled = false;
                return;
            }

            try {
                const tiposDisponibles = JSON.parse(tiposData);
                console.log('Tipos disponibles:', tiposDisponibles);

                tipoProgramaSelect.innerHTML = '<option value="">Seleccionar tipo de programa...</option>';

                tiposDisponibles.forEach(tipo => {
                    const option = document.createElement('option');
                    option.value = tipo.id;
                    option.textContent = tipo.nombre_tipo_programa || tipo.nombre || 'Sin nombre';

                    // Seleccionar si coincide con el tipo guardado
                    if (tipoSeleccionado && parseInt(tipoSeleccionado) === parseInt(tipo.id)) {
                        option.selected = true;
                        console.log('Tipo seleccionado:', tipo.id);
                    }

                    tipoProgramaSelect.appendChild(option);
                });

                tipoProgramaSelect.disabled = false;

                // Si no se seleccionó automáticamente, intentar seleccionar por el valor del estudio
                if (tipoSeleccionado && !tipoProgramaSelect.value) {
                    setTimeout(() => {
                        tipoProgramaSelect.value = tipoSeleccionado;
                        console.log('Forzando selección:', tipoSeleccionado);
                    }, 100);
                }

            } catch (error) {
                console.error('Error al parsear tipos:', error);
                tipoProgramaSelect.innerHTML = '<option value="">Error en datos de tipos</option>';
            }
        }

        // INICIALIZACIÓN AUTOMÁTICA AL CARGAR LA PÁGINA
        console.log('Inicializando formulario de edición...');
        console.log('Estudio programa_id:', '{{ $estudio->programa_id }}');
        console.log('Estudio tipo_programa_id:', '{{ $estudio->tipo_programa_id }}');

        // Establecer el programa y cargar sus tipos inmediatamente
        if ('{{ $estudio->programa_id }}') {
            programaSelect.value = '{{ $estudio->programa_id }}';
            console.log('Programa establecido:', programaSelect.value);

            // Cargar tipos inmediatamente
            cargarTiposPrograma('{{ $estudio->programa_id }}', '{{ $estudio->tipo_programa_id }}');
        }

        // Event listener para cambios en programa
        programaSelect.addEventListener('change', function() {
            console.log('Programa cambiado:', this.value);
            cargarTiposPrograma(this.value);
        });

        // Verificar después de un breve tiempo si se cargó correctamente
        setTimeout(() => {
            if (tipoProgramaSelect.value !== '{{ $estudio->tipo_programa_id }}' && '{{ $estudio->tipo_programa_id }}') {
                console.log('Reintentando establecer tipo_programa_id...');
                tipoProgramaSelect.value = '{{ $estudio->tipo_programa_id }}';
            }
        }, 500);
    });

    function siguientePaso(paso) {
        const nextTab = new bootstrap.Tab(document.getElementById(`paso${paso}-tab`));
        nextTab.show();
    }

    function anteriorPaso(paso) {
        const prevTab = new bootstrap.Tab(document.getElementById(`paso${paso}-tab`));
        prevTab.show();
    }
</script>
@endsection