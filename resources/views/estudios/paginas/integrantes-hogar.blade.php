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
                        <td>{{ $integrante->parentesco }}</td>
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
                <div class="alert {{ $claseBadge == 'bg-danger' ? 'alert-danger' : ($claseBadge == 'bg-warning' ? 'alert-warning' : 'alert-success') }}">
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

        // Editar integrante - CORREGIDO
        document.querySelectorAll('.edit-integrantes-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const integranteId = this.getAttribute('data-id');
                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Actualizando...';
                submitBtn.disabled = true;

                // Usar la ruta con nombre de Laravel para UPDATE
                fetch('{{ route("integrantes-hogar.update", "") }}/' + integranteId, {
                        method: 'POST', // Laravel requiere POST para simular PUT con _method
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-HTTP-Method-Override': 'PUT' // Método alternativo
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

        // Eliminar integrante - CORREGIDO
        document.querySelectorAll('.delete-integrantes-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const integranteId = this.getAttribute('data-id');
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Eliminando...';
                submitBtn.disabled = true;

                // Usar la ruta con nombre de Laravel para DELETE
                fetch('{{ route("integrantes-hogar.destroy", "") }}/' + integranteId, {
                        method: 'POST', // Laravel requiere POST para simular DELETE con _method
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-HTTP-Method-Override': 'DELETE' // Método alternativo
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

        // Manejar selección de líneas CONEVAL
document.addEventListener('DOMContentLoaded', function() {
    const conevalRadios = document.querySelectorAll('.coneval-radio');
    const lineaConevalIdInput = document.getElementById('linea_coneval_id');
    const conevalActiveInput = document.getElementById('coneval_active');
    const estudioId = {{ $estudio->id ?? 'null' }};
    
    conevalRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                const lineaId = this.getAttribute('data-linea-id');
                const active = this.getAttribute('data-active');
                
                // Actualizar campos ocultos
                lineaConevalIdInput.value = lineaId;
                conevalActiveInput.value = active;
                
                console.log('Línea CONEVAL seleccionada:', {
                    linea_id: lineaId,
                    active: active
                });
                
                // Actualizar automáticamente en el servidor si hay un estudio
                if (estudioId) {
                    actualizarConevalEnServidor(lineaId, active);
                }
            }
        });
    });

    // Función para actualizar CONEVAL en el servidor
    function actualizarConevalEnServidor(lineaId, active) {
        fetch(`/estudios/${estudioId}/update-coneval`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                linea_coneval_id: lineaId,
                coneval_active: active
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recargar para actualizar la comparación
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                console.error('Error al actualizar CONEVAL:', data.message);
                alert('Error al guardar la selección: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            alert('Error de conexión al guardar la selección');
        });
    }
});
});
</script>

