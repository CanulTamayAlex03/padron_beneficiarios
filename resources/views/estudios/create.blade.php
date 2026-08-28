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

                    @include('estudios.paginas.acompanantes') 

                    <form action="{{ route('estudios.store') }}" method="POST" id="estudioForm">
                        @csrf

                        <input type="hidden" name="beneficiario_id" value="{{ $beneficiario->id }}">

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
                                        <input type="text" 
                                            class="form-control" 
                                            id="folio" 
                                            name="folio"
                                            value="{{ old('folio') }}" 
                                            maxlength="6"
                                            pattern="[0-9]*"
                                            inputmode="numeric"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            required>
                                        
                                        <div id="folio-status" class="small mt-1 d-none">
                                            <i class="bi"></i> <span></span>
                                        </div>
                                        
                                        <div id="folio-existente-alert" class="alert alert-warning d-none mt-2">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-exclamation-triangle me-2"></i>
                                                <div>
                                                    <small>
                                                    <a href="#" id="estudio-existente-link" class=" text-decoration-none">
                                                        <span id="estudio-existente-info"></span>
                                                    </a>
                                                    </small>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar me-1"></i> 
                                                        Creado el: <span id="estudio-fecha-creacion"></span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        
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
                                        <label class="form-label">Municipio y Región</label>
                                        <div class="form-control bg-light" style="min-height: 38px; display: flex; align-items: center;">
                                            <strong>
                                                {{ $beneficiario->municipio->descripcion ?? 'No asignado' }}
                                                <span class="badge bg-primary ms-2">
                                                    Región {{ $beneficiario->municipio->region ?? 'N/A' }}
                                                </span>
                                            </strong>
                                        </div>
                                        <input type="hidden" name="municipio_id" value="{{ $beneficiario->municipio_id ?? '' }}">
                                        <input type="hidden" name="region" value="{{ $beneficiario->municipio->region ?? '' }}">
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

    @if(isset($estudio) && $estudio->exists)
        cargarTiposPrograma({{ $estudio->programa_id }}, {{ $estudio->tipo_programa_id }});
    @endif

    programaSelect.addEventListener('change', function() {
        cargarTiposPrograma(this.value);
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const folioInput = document.getElementById('folio');
    const folioStatus = document.getElementById('folio-status');
    const folioAlert = document.getElementById('folio-existente-alert');
    const estudioInfo = document.getElementById('estudio-existente-info');
    const estudioLink = document.getElementById('estudio-existente-link');
    const estudioFecha = document.getElementById('estudio-fecha-creacion');

    let folioCheckTimeout = null;
    let folioValido = true;

    function verificarFolio() {
        const folio = folioInput.value.trim();

        if (folioCheckTimeout) {
            clearTimeout(folioCheckTimeout);
        }

        const esNumero = /^[0-9]+$/.test(folio);
        const esLongitudValida = folio.length <= 6;

        if (folio === '' || !esNumero || !esLongitudValida) {
            folioStatus.classList.add('d-none');
            folioAlert.classList.add('d-none');
            folioValido = false;
            
            if (folio !== '' && !esNumero) {
                folioStatus.classList.remove('d-none');
                folioStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Solo se permiten números</span>';
                folioInput.classList.add('is-invalid');
            } else if (folio !== '' && !esLongitudValida) {
                folioStatus.classList.remove('d-none');
                folioStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Máximo 6 dígitos</span>';
                folioInput.classList.add('is-invalid');
            } else {
                folioInput.classList.remove('is-invalid');
                folioValido = true;
            }
            
            actualizarEstadoBoton();
            return;
        }

        folioInput.classList.remove('is-invalid');
        folioValido = true;

        folioStatus.classList.remove('d-none');
        folioStatus.innerHTML = '<span class="text-warning"><i class="bi bi-hourglass-split"></i> Verificando...</span>';
        folioAlert.classList.add('d-none');

        folioCheckTimeout = setTimeout(() => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch(`{{ route('estudios.check-folio') }}?folio=${encodeURIComponent(folio)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.exists) {
                    const estudio = data.estudio;

                    folioStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Folio ya registrado</span>';
                    folioValido = false;

                    estudioInfo.textContent = estudio.texto_link;
                    estudioLink.href = estudio.ruta_edicion;
                    estudioFecha.textContent = estudio.fecha_creacion;
                    folioAlert.classList.remove('d-none');

                    folioInput.classList.add('is-invalid');
                } else {
                    folioStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Folio disponible</span>';
                    folioValido = true;
                    folioAlert.classList.add('d-none');
                    folioInput.classList.remove('is-invalid');
                }

                actualizarEstadoBoton();
            })
            .catch(error => {
                console.error('Error al verificar folio:', error);
                folioStatus.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Error al verificar</span>';
                folioValido = true;
                folioAlert.classList.add('d-none');
                actualizarEstadoBoton();
            });
        }, 800);
    }

    function actualizarEstadoBoton() {
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = !folioValido;
        }
    }

    folioInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
        }
        
        verificarFolio();
    });
    
    folioInput.addEventListener('blur', function() {
        if (this.value.trim() !== '') {
            verificarFolio();
        }
    });

    document.getElementById('estudioForm').addEventListener('submit', function(e) {
        const folio = folioInput.value.trim();

        if (folio === '') {
            e.preventDefault();
            showAlert('El folio es obligatorio', 'danger');
            return;
        }

        if (!/^[0-9]+$/.test(folio)) {
            e.preventDefault();
            showAlert('El folio solo debe contener números', 'danger');
            return;
        }

        if (folio.length > 6) {
            e.preventDefault();
            showAlert('El folio no puede tener más de 6 dígitos', 'danger');
            return;
        }

        if (!folioValido) {
            e.preventDefault();
            showAlert('El folio ya está registrado. Por favor use otro.', 'danger');
            return;
        }
    });

    if (typeof showAlert !== 'function') {
        window.showAlert = function(message, type = 'success') {
            const container = document.querySelector('.card-body') || document.body;
            const wrapper = document.createElement('div');
            wrapper.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <strong>${type === 'danger' ? 'Error:' : ''}</strong>
                    <span class="ms-1">${message}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            container.prepend(wrapper.firstElementChild);

            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) {
                    bootstrap.Alert.getOrCreateInstance(alert).close();
                }
            }, 5000);
        };
    }
    
    actualizarEstadoBoton();
});
</script>
@endsection