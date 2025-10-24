<div class="card mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-house-door-fill me-2"></i> Integrantes del Hogar
        </h5>
        @if(isset($estudio) && $estudio->id)
        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createIntegranteModal">
            <i class="bi bi-person-plus"></i> Agregar Integrante
        </button>
        @else
        <div class="alert alert-warning py-1 mb-0">
            <small><i class="bi bi-info-circle"></i> Guarde el estudio primero para agregar integrantes</small>
        </div>
        @endif
    </div>

    <div class="card-body">
        @if(isset($estudio) && $estudio->id && $estudio->integrantesHogar->count())
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre completo</th>
                        <th>Edad</th>
                        <th>Parentesco</th>
                        <th>Ingreso mensual</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalIngresos = 0;
                        $totalPersonas = 0;
                    @endphp
                    @foreach($estudio->integrantesHogar as $integrante)
                    @php
                        $totalIngresos += $integrante->ingreso_mensual;
                        $totalPersonas++;
                    @endphp
                    <tr>
                        <td>{{ $integrante->nombres }} {{ $integrante->apellidos }}</td>
                        <td>{{ $integrante->edad }} años</td>
                        <td>{{ $integrante->parentesco->descripcion ?? 'N/A' }}</td>
                        <td>${{ number_format($integrante->ingreso_mensual, 2) }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-warning"
                                data-bs-toggle="modal" data-bs-target="#editIntegranteModal{{ $integrante->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteIntegranteModal{{ $integrante->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Sección de totales -->
        @php
            $lineaCanasta = $totalPersonas > 0 ? $totalIngresos / $totalPersonas : 0;
        @endphp
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total de ingreso mensual en el hogar</h6>
                        <p class="card-text h5 text-primary fw-bold">${{ number_format($totalIngresos, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total de personas que viven en el hogar</h6>
                        <p class="card-text h5 text-dark fw-bold">{{ $totalPersonas }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title">Línea de canasta alimentaría del hogar</h6>
                        <p class="card-text h5 text-success fw-bold">${{ number_format($lineaCanasta, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

       
<!-- Sección de Línea CONEVAL -->
<div class="card mt-4">
    <div class="card-body">
        <!-- Pregunta 1 -->
        <label class="form-label fw-bold">
            ¿El hogar se encuentra debajo de la línea del Bienestar según la corte de marzo 2025, CONEVAL?
        </label>

        <!-- Campos ocultos para almacenar los valores -->
        <input type="hidden" name="linea_coneval_id" id="linea_coneval_id" value="{{ $estudio->linea_coneval_id ?? '' }}">
        <input type="hidden" name="coneval_active" id="coneval_active" value="{{ $estudio->coneval_active ?? '' }}">

        <div class="table-responsive mt-3">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Zona:</th>
                        <th class="text-center">Monto</th>
                        <th class="text-center">Sí (3 pts)</th>
                        <th class="text-center">No</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lineasConeval as $linea)
                    <tr>
                        <td class="fw-bold">{{ $linea->zona }}</td>
                        <td class="text-center">
                            menos de: <strong>${{ number_format($linea->cantidad, 2) }}</strong>
                        </td>
                        <td class="text-center">
                            <input type="radio"
                                   name="linea_coneval_selection"
                                   class="coneval-radio"
                                   data-linea-id="{{ $linea->id }}"
                                   data-active="1"
                                   id="si_{{ $linea->id }}"
                                   value="si_{{ $linea->id }}"
                                   {{ $estudio->linea_coneval_id == $linea->id && $estudio->coneval_active == 1 ? 'checked' : '' }}>
                        </td>
                        <td class="text-center">
                            <input type="radio"
                                   name="linea_coneval_selection"
                                   class="coneval-radio"
                                   data-linea-id="{{ $linea->id }}"
                                   data-active="0"
                                   id="no_{{ $linea->id }}"
                                   value="no_{{ $linea->id }}"
                                   {{ $estudio->linea_coneval_id == $linea->id && $estudio->coneval_active == 0 ? 'checked' : '' }}>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr class="my-4">

        <!-- Preguntas 2 y 3 en la misma fila -->
        <div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">
            ¿Cuenta con algún servicio de salud?
        </label>
        <select class="form-select" name="servicio_salud_id" id="servicio_salud_id">
            <option value="">Seleccione una opción</option>
            @foreach($serviciosSalud as $servicio)
            <option value="{{ $servicio->id }}" 
                @if(old('servicio_salud_id', $estudio->servicio_salud_id ?? '') == $servicio->id) selected @endif>
                {{ $servicio->nombre_servicio }} 
                @if($servicio->puntos > 0)
                <small class="text-muted">({{ $servicio->puntos }} pts)</small>
                @endif
            </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">
            Escolaridad:
        </label>
        <select class="form-select" name="escolaridad_id" id="escolaridad_id">
            <option value="">Seleccione una opción</option>
            @foreach($escolaridades as $escolaridad)
            <option value="{{ $escolaridad->id }}" 
                @if(old('escolaridad_id', $estudio->escolaridad_id ?? '') == $escolaridad->id) selected @endif>
                {{ $escolaridad->nombre_escolaridad }}
                @if($escolaridad->puntos > 0)
                <small class="text-muted">({{ $escolaridad->puntos }} pts)</small>
                @endif
            </option>
            @endforeach
        </select>
    </div>
</div>

<!-- Sección de Conclusiones -->
@if(isset($estudio) && $estudio->id)
<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0">
            <i class="bi bi-graph-up me-2"></i>
            Evaluación y Conclusiones
        </h6>
    </div>
    <div class="card-body">
        @php
            // Calcular puntuación total
            $puntosConeval = $estudio->coneval_active ? 3 : 0;
            $puntosServicioSalud = $estudio->servicioSalud ? $estudio->servicioSalud->puntos : 0;
            $puntosEscolaridad = $estudio->escolaridad ? $estudio->escolaridad->puntos : 0;
            $puntosTotales = $puntosConeval + $puntosServicioSalud + $puntosEscolaridad;
            
            // Determinar nivel de vulnerabilidad
            $nivelVulnerabilidad = '';
            $claseBadge = '';
            
            if ($puntosTotales >= 1 && $puntosTotales <= 3) {
                $nivelVulnerabilidad = 'Leve';
                $claseBadge = 'bg-success';
            } elseif ($puntosTotales >= 4 && $puntosTotales <= 6) {
                $nivelVulnerabilidad = 'Moderada';
                $claseBadge = 'bg-warning text-dark';
            } elseif ($puntosTotales >= 7 && $puntosTotales <= 9) {
                $nivelVulnerabilidad = 'Severa';
                $claseBadge = 'bg-danger';
            } else {
                $nivelVulnerabilidad = 'Sin datos suficientes';
                $claseBadge = 'bg-secondary';
            }
        @endphp

        <!-- Mostrar puntuaciones individuales -->
        <div class="row mb-4">
            <div class="col-md-4 text-center">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">CONEVAL</h6>
                        <p class="card-text h4 {{ $estudio->coneval_active ? 'text-success' : 'text-muted' }}">
                            {{ $puntosConeval }} pts
                        </p>
                        <small class="text-muted">
                            @if($estudio->coneval_active)
                            SÍ está debajo de la línea
                            @else
                            NO está debajo de la línea
                            @endif
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Servicio de Salud</h6>
                        <p class="card-text h4 {{ $puntosServicioSalud > 0 ? 'text-primary' : 'text-muted' }}">
                            {{ $puntosServicioSalud }} pts
                        </p>
                        <small class="text-muted">
                            {{ $estudio->servicioSalud ? $estudio->servicioSalud->nombre_servicio : 'No seleccionado' }}
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Escolaridad</h6>
                        <p class="card-text h4 {{ $puntosEscolaridad > 0 ? 'text-primary' : 'text-muted' }}">
                            {{ $puntosEscolaridad }} pts
                        </p>
                        <small class="text-muted">
                            {{ $estudio->escolaridad ? $estudio->escolaridad->nombre_escolaridad : 'No seleccionado' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mostrar total y conclusión -->
        <div class="row">
            <div class="col-12">
                <div class="alert {{ (strpos($claseBadge, 'bg-danger') !== false) ? 'alert-danger' : (strpos($claseBadge, 'bg-warning') !== false ? 'alert-warning' : 'alert-success') }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="alert-heading mb-1">
                                Puntuación Total: <strong>{{ $puntosTotales }} puntos</strong>
                            </h5>
                            <p class="mb-0">
                                Nivel de vulnerabilidad: 
                                <span class="badge {{ $claseBadge }} fs-6">{{ $nivelVulnerabilidad }}</span>
                            </p>
                        </div>
                        <div class="text-end">
                            <h2 class="display-6 fw-bold mb-0">{{ $puntosTotales }}</h2>
                            <small class="text-muted">puntos totales</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
    </div>
</div>

        @else
        <p class="text-muted">
            @if(isset($estudio) && $estudio->id)
            No hay integrantes del hogar registrados.
            @else
            El estudio debe ser guardado primero para gestionar integrantes del hogar.
            @endif
        </p>
        @endif
    </div>
</div>

<style>
.linea-option:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.linea-option.border-primary {
    border-width: 2px !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INICIANDO SCRIPT INTEGRANTES HOGAR ===');

    // =============================================
    // 0. INICIALIZACIÓN MEJORADA - SOLO UNA VEZ
    // =============================================
    function inicializarConeval() {
        const lineaConevalIdForm = document.getElementById('linea_coneval_id');
        const conevalActiveForm = document.getElementById('coneval_active');
        
        // Solo inicializar si los campos están vacíos
        if (lineaConevalIdForm && !lineaConevalIdForm.value) {
            const estudioLineaId = '{{ $estudio->linea_coneval_id ?? "" }}';
            if (estudioLineaId && estudioLineaId !== 'null') {
                lineaConevalIdForm.value = estudioLineaId;
                console.log('linea_coneval_id_form inicializado:', estudioLineaId);
            }
        }
        
        if (conevalActiveForm && !conevalActiveForm.value) {
            const estudioActive = '{{ $estudio->coneval_active ?? "" }}';
            if (estudioActive && estudioActive !== 'null') {
                conevalActiveForm.value = estudioActive;
                console.log('coneval_active_form actualizado:', estudioActive);
            }
        }
    }

    // Llamar a la inicialización SOLO UNA VEZ al inicio
    inicializarConeval();

    // =============================================
    // 1. CRUD DE INTEGRANTES DEL HOGAR
    // =============================================
    const createForm = document.getElementById('createIntegranteForm');
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Guardando...';
            submitBtn.disabled = true;

            fetch('{{ route("integrantes-hogar.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createIntegranteModal'));
                        modal.hide();
                        location.reload();
                    } else {
                        alert(data.error || 'Error al guardar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al guardar el integrante');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    }

    // Editar integrante
    document.querySelectorAll('.edit-integrantes-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const integranteId = this.getAttribute('data-id');
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Actualizando...';
            submitBtn.disabled = true;

            fetch('{{ route("integrantes-hogar.update", "") }}/' + integranteId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(`editIntegranteModal${integranteId}`));
                        modal.hide();
                        location.reload();
                    } else {
                        alert(data.error || 'Error al actualizar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al actualizar el integrante: ' + error.message);
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    });

    // Eliminar integrante
    document.querySelectorAll('.delete-integrantes-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const integranteId = this.getAttribute('data-id');
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Eliminando...';
            submitBtn.disabled = true;

            fetch('{{ route("integrantes-hogar.destroy", "") }}/' + integranteId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'DELETE'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(`deleteIntegranteModal${integranteId}`));
                        modal.hide();
                        location.reload();
                    } else {
                        alert(data.error || 'Error al eliminar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el integrante: ' + error.message);
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    });

    // =============================================
    // 2. SINCRONIZACIÓN DE LAS 3 PREGUNTAS
    // =============================================

    // A. Sincronizar Servicio de Salud
    const servicioSaludSelect = document.getElementById('servicio_salud_id');
    const servicioSaludHidden = document.getElementById('servicio_salud_id_form');
    
    if (servicioSaludSelect && servicioSaludHidden) {
        // Solo sincronizar valor inicial si está vacío
        if (!servicioSaludHidden.value && servicioSaludSelect.value) {
            servicioSaludHidden.value = servicioSaludSelect.value;
        }
        
        servicioSaludSelect.addEventListener('change', function() {
            servicioSaludHidden.value = this.value;
            console.log('Servicio salud actualizado:', this.value);
        });
    }
    
    // B. Sincronizar Escolaridad
    const escolaridadSelect = document.getElementById('escolaridad_id');
    const escolaridadHidden = document.getElementById('escolaridad_id_form');
    
    if (escolaridadSelect && escolaridadHidden) {
        // Solo sincronizar valor inicial si está vacío
        if (!escolaridadHidden.value && escolaridadSelect.value) {
            escolaridadHidden.value = escolaridadSelect.value;
        }
        
        escolaridadSelect.addEventListener('change', function() {
            escolaridadHidden.value = this.value;
            console.log('Escolaridad actualizada:', this.value);
        });
    }

    // C. Sincronizar CONEVAL
    const conevalRadios = document.querySelectorAll('.coneval-radio');
    const lineaConevalIdForm = document.getElementById('linea_coneval_id');
    const conevalActiveForm = document.getElementById('coneval_active');
    
    console.log('CONEVAL - Estado inicial campos:', {
        lineaValue: lineaConevalIdForm?.value,
        activeValue: conevalActiveForm?.value
    });

    // Event listeners para CONEVAL
    conevalRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                const lineaId = this.getAttribute('data-linea-id');
                const active = this.getAttribute('data-active');
                
                console.log('CONEVAL - Radio cambiado:', {
                    linea_id: lineaId,
                    active: active,
                    radio_id: this.id
                });
                
                // ACTUALIZAR CAMPOS DEL FORMULARIO PRINCIPAL
                if (lineaConevalIdForm) {
                    lineaConevalIdForm.value = lineaId;
                    console.log('linea_coneval_id_form actualizado:', lineaId);
                }
                if (conevalActiveForm) {
                    conevalActiveForm.value = active;
                    console.log('coneval_active_form actualizado:', active);
                }
            }
        });
    });

    // =============================================
    // 3. VERIFICACIÓN DE ENVÍO DE FORMULARIO
    // =============================================
    const formPrincipal = document.getElementById('estudioForm');
    if (formPrincipal) {
        formPrincipal.addEventListener('submit', function(e) {
            // Obtener valores ACTUALES en el momento del envío
            const lineaId = document.getElementById('linea_coneval_id_form')?.value;
            const active = document.getElementById('coneval_active_form')?.value;
            const servicioSalud = document.getElementById('servicio_salud_id_form')?.value;
            const escolaridad = document.getElementById('escolaridad_id_form')?.value;
            
            // Verificar específicamente CONEVAL
            if (!lineaId || !active) {
                console.warn('⚠️ ADVERTENCIA: Campos CONEVAL incompletos');
            } else {
                console.log('✅ CONEVAL completo - Se guardará correctamente');
            }
        });
    }

    // =============================================
    // 4. VERIFICACIÓN ÚNICA AL CARGAR
    // =============================================
    setTimeout(() => {
        console.log(' === VERIFICACIÓN INICIAL DE CAMPOS ===');
        
        const verificarCampo = (id, nombre) => {
            const elemento = document.getElementById(id);
            if (elemento) {
                console.log(` ${nombre}:`, elemento.value || '(vacío)');
                return elemento.value;
            }
            return null;
        };

        console.log(' linea_coneval_id:', verificarCampo('linea_coneval_id_form', 'linea_coneval_id'));
        console.log(' coneval_active:', verificarCampo('coneval_active_form', 'coneval_active'));
        console.log(' servicio_salud_id:', verificarCampo('servicio_salud_id_form', 'servicio_salud_id'));
        console.log(' escolaridad_id:', verificarCampo('escolaridad_id_form', 'escolaridad_id'));
    }, 500);

    console.log('=== SCRIPT INTEGRANTES HOGAR CARGADO ===');
});
</script>
