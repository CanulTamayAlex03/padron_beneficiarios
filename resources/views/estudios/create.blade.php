@can('crear beneficiarios')
@extends('layouts.app')

@section('title', 'Crear Estudio Socioeconómico')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="bi bi-clipboard-data me-2"></i>
                            Nuevo Estudio Socioeconómico
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

                    <!-- Inclusión de la sección de acompañantes -->
                    @include('estudios.paginas.acompanantes') 

                    <!-- Formulario del Estudio Socioeconómico -->
                    <form action="{{ route('estudios.store') }}" method="POST" id="estudioForm">
                        @csrf

                        <input type="hidden" name="beneficiario_id" value="{{ $beneficiario->id }}">

                        <!-- Sección de datos básicos del estudio -->
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-card-checklist me-2"></i>
                                    Datos del Estudio Socioeconómico
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="folio" class="form-label">Folio *</label>
                                        <input type="text" class="form-control" id="folio" name="folio"
                                            value="{{ old('folio') }}" required>
                                        @error('folio')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="fecha_solicitud" class="form-label">Fecha de Solicitud *</label>
                                        <input type="date" class="form-control" id="fecha_solicitud" name="fecha_solicitud"
                                            value="{{ old('fecha_solicitud', date('Y-m-d')) }}" required>
                                        @error('fecha_solicitud')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="region_id" class="form-label">Región *</label>
                                        <select class="form-select" id="region_id" name="region_id" required>
                                            <option value="">Seleccionar región...</option>
                                            @foreach($regiones as $region)
                                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
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
                                        <select class="form-select" id="solicitud_id" name="solicitud_id" required>
                                            <option value="">Seleccionar solicitud...</option>
                                            @foreach($solicitudes as $solicitud)
                                            <option value="{{ $solicitud->id }}" {{ old('solicitud_id') == $solicitud->id ? 'selected' : '' }}>
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
                                        <select class="form-select" id="programa_id" name="programa_id" required>
                                            <option value="">Seleccionar programa...</option>
                                            @foreach($programas as $programa)
                                            <option value="{{ $programa->id }}" {{ old('programa_id') == $programa->id ? 'selected' : '' }}
                                                data-tipos='@json($programa->tiposPrograma->map(fn($t) => [
                                "id" => $t->id,
                                "nombre" => $t->nombre_tipo_programa
                            ]))'>
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
                                        <select class="form-select" id="tipo_programa_id" name="tipo_programa_id" required disabled>
                                            <option value="">Primero seleccione un programa</option>
                                        </select>
                                        @error('tipo_programa_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end pe-3 pb-3">
                                <button type="submit" class="btn btn-success"
                                    onclick="return confirm('¿Deseas guardar los datos iniciales del estudio socioeconómico?')">
                                    <i class="bi bi-save"></i> Guardar Datos Principales
                                </button>
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
                                    <i class="bi bi-3-circle me-1"></i>Evaluación de la seguridad alimentaria
                                </button>
                            </li>
                        </ul>

                        
                            <div class="tab-content" id="estudioTabsContent">

                                <!-- PASO 1: Editar datos del beneficiario -->
                                @include('estudios.paginas.estudio_paso1')

                                <!-- PASO 2: Evaluación Economica y Familiar -->
                                @include('estudios.paginas.estudio_paso2')

                                <!-- PASO 3: Necesidades y Observaciones -->
                                <div class="tab-pane fade" id="paso3" role="tabpanel">
                                    <fieldset class="border rounded p-3 mb-4">
                                        <legend class="float-none w-auto px-3 fw-bold text-dark">
                                            <i class="bi bi-clipboard-check me-2"></i>Evaluación de la seguridad alimentaria
                                        </legend>

                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Necesidades Prioritarias Identificadas</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="alimentacion" id="alimentacion">
                                                            <label class="form-check-label" for="alimentacion">Alimentación</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="salud" id="salud">
                                                            <label class="form-check-label" for="salud">Salud</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="educacion" id="educacion">
                                                            <label class="form-check-label" for="educacion">Educación</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="vivienda" id="vivienda">
                                                            <label class="form-check-label" for="vivienda">Vivienda</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="empleo" id="empleo">
                                                            <label class="form-check-label" for="empleo">Empleo</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="vestimenta" id="vestimenta">
                                                            <label class="form-check-label" for="vestimenta">Vestimenta</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label for="observaciones" class="form-label">Observaciones Generales</label>
                                                <textarea class="form-control" id="observaciones" rows="3"
                                                    placeholder="Describa la situación socioeconómica general del beneficiario y su familia..."></textarea>
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label for="recomendaciones" class="form-label">Recomendaciones y Apoyos Sugeridos</label>
                                                <textarea class="form-control" id="recomendaciones" rows="2"
                                                    placeholder="Sugerencias de apoyos o programas que podrían beneficiar al solicitante..."></textarea>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary" onclick="anteriorPaso(2)">
                                            <i class="bi bi-arrow-left"></i> Anterior
                                        </button>
                                        <button type="submit" class="btn btn-success"
                                            onclick="return confirm('¿Estás seguro de guardar el estudio socioeconómico?')">
                                            <i class="bi bi-save"></i> Guardar Estudio
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal crear -->
@include('estudios.familiares-modals.create')

@foreach($beneficiario->familiares as $familiar)
<!-- Incluir modales individuales -->
@include('estudios.familiares-modals.edit', ['familiar' => $familiar])
@include('estudios.familiares-modals.delete', ['familiar' => $familiar])
@endforeach
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

        if (programaSelect && tipoProgramaSelect) {
            programaSelect.addEventListener('change', function() {
                const programaId = this.value;
                tipoProgramaSelect.innerHTML = '<option value="">Seleccionar tipo de programa...</option>';
                tipoProgramaSelect.disabled = !programaId;

                if (programaId) {
                    const programaOption = programaSelect.querySelector(`option[value="${programaId}"]`);
                    const tiposData = programaOption.getAttribute('data-tipos');

                    if (tiposData) {
                        const tiposDisponibles = JSON.parse(tiposData);

                        tiposDisponibles.forEach(tipo => {
                            const option = document.createElement('option');
                            option.value = tipo.id;
                            option.textContent = tipo.nombre;
                            tipoProgramaSelect.appendChild(option);
                        });

                        tipoProgramaSelect.disabled = false;
                    }
                }
            });

            // Restaurar valores si hubo error en validación
            @if(old('programa_id'))
            programaSelect.value = '{{ old('
            programa_id ') }}';
            programaSelect.dispatchEvent(new Event('change'));

            @if(old('tipo_programa_id'))
            setTimeout(() => {
                tipoProgramaSelect.value = '{{ old('
                tipo_programa_id ') }}';
            }, 100);
            @endif
            @endif
        }
    });

    // Funciones de navegación entre pasos
    function siguientePaso(paso) {
        const nextTab = new bootstrap.Tab(document.getElementById(`paso${paso}-tab`));
        nextTab.show();
    }

    function anteriorPaso(paso) {
        const prevTab = new bootstrap.Tab(document.getElementById(`paso${paso}-tab`));
        prevTab.show();
    }


    document.addEventListener('DOMContentLoaded', function() {
    const programaSelect = document.getElementById('programa_id');
    const tipoProgramaSelect = document.getElementById('tipo_programa_id');

    function cargarTiposPrograma(programaId, tipoSeleccionado = null) {
        tipoProgramaSelect.innerHTML = '<option value="">Cargando tipos...</option>';
        
        if (programaId) {
            const programaOption = programaSelect.querySelector(`option[value="${programaId}"]`);
            const tiposData = programaOption.getAttribute('data-tipos');

            if (tiposData) {
                const tiposDisponibles = JSON.parse(tiposData);
                tipoProgramaSelect.innerHTML = '<option value="">Seleccionar tipo de programa...</option>';
                
                tiposDisponibles.forEach(tipo => {
                    const option = document.createElement('option');
                    option.value = tipo.id;
                    option.textContent = tipo.nombre;
                    option.selected = (tipoSeleccionado && tipoSeleccionado == tipo.id);
                    tipoProgramaSelect.appendChild(option);
                });

                tipoProgramaSelect.disabled = false;
            }
        } else {
            tipoProgramaSelect.innerHTML = '<option value="">Primero seleccione un programa</option>';
            tipoProgramaSelect.disabled = true;
        }
    }

    // Si es edición, cargar los tipos automáticamente
    @if(isset($estudio) && $estudio->exists)
        cargarTiposPrograma({{ $estudio->programa_id }}, {{ $estudio->tipo_programa_id }});
    @endif

    // Event listener para cambios en programa
    programaSelect.addEventListener('change', function() {
        cargarTiposPrograma(this.value);
    });
});
</script>
@endsection