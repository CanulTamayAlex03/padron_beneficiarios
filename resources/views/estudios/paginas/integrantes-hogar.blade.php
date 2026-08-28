<div class="card mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-house-door-fill me-2"></i> Integrantes del Hogar
        </h5>
        @if(isset($estudio) && $estudio->id)
        <button type="button" class="btn btn-light btn-sm" id="btnAgregarIntegrante">
            <i class="bi bi-person-plus"></i> Agregar Integrante
        </button>
        @else
        <div class="alert alert-warning py-1 mb-0">
            <small><i class="bi bi-info-circle"></i> Guarde el estudio primero para agregar integrantes</small>
        </div>
        @endif
    </div>

    <div class="card-body">
        @if(isset($estudio) && $estudio->id)
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle" id="tablaIntegrantes">
                <thead class="table-light">
                    <tr>
                        <th style="width: 30%;">Integrante</th>
                        <th style="width: 50%;">Ingreso Mensual</th>
                        <th style="width: 20%;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbodyIntegrantes">
                    @php
                    $totalIngresos = 0;
                    $totalPersonas = 0;
                    @endphp

                    @foreach($estudio->integrantesHogar->sortBy('integrante') as $integrante)
                    @php
                    $totalIngresos += $integrante->ingreso_mensual;
                    $totalPersonas++;
                    @endphp
                    <tr data-id="{{ $integrante->id }}" data-integrante="{{ $integrante->integrante }}">
                        <td>
                            <strong>
                                Integrante {{ $integrante->integrante }}
                                @if($integrante->integrante == 1)
                                    <span class="badge bg-primary ms-2">Beneficiario</span>
                                @endif
                            </strong>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                    class="form-control ingreso-input" 
                                    value="{{ $integrante->ingreso_mensual }}"
                                    step="0.01" 
                                    min="0"
                                    data-id="{{ $integrante->id }}"
                                    placeholder="0.00"
                                    @if($integrante->integrante == 1) data-beneficiario="true" @endif>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($integrante->integrante == 1)
                                <span class="text-muted small">
                                    <i class="bi bi-lock"></i> No se puede eliminar
                                </span>
                            @else
                                <button type="button" class="btn btn-sm btn-danger eliminar-integrante" data-id="{{ $integrante->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Sección de totales -->
        @php
        $ingresoPerCapita = $totalPersonas > 0 ? $totalIngresos / $totalPersonas : 0;
        @endphp
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total de ingreso mensual</h6>
                        <p class="card-text h5 text-primary fw-bold" id="totalIngresos">${{ number_format($totalIngresos, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total de personas en el hogar</h6>
                        <p class="card-text h5 text-dark fw-bold" id="totalPersonas">{{ $totalPersonas }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title">Ingreso per cápita</h6>
                        <p class="card-text h5 text-success fw-bold" id="ingresoPerCapita">${{ number_format($ingresoPerCapita, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Línea CONEVAL -->
        <div class="card mt-4">
            <div class="card-body">
                @php
                $fechaFormateada = \Carbon\Carbon::parse($lineasConeval->first()->periodo)->locale('es')->translatedFormat('F Y');
                @endphp
                <label class="form-label fw-bold">
                    ¿El hogar se encuentra debajo de la línea del Bienestar según la corte de {{ $fechaFormateada }}, CONEVAL?
                </label>
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
                            <tr class="{{ !$linea->activo ? 'table-warning' : '' }}">
                                <td class="fw-bold">
                                    {{ $linea->zona }}
                                    @if(!$linea->activo)
                                    @endif
                                </td>
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
                                        {{ $estudio->linea_coneval_id == $linea->id && $estudio->coneval_active == 1 ? 'checked' : '' }}
                                        {{ !$linea->activo && $estudio->linea_coneval_id != $linea->id ? 'disabled' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input type="radio"
                                        name="linea_coneval_selection"
                                        class="coneval-radio"
                                        data-linea-id="{{ $linea->id }}"
                                        data-active="0"
                                        id="no_{{ $linea->id }}"
                                        value="no_{{ $linea->id }}"
                                        {{ $estudio->linea_coneval_id == $linea->id && $estudio->coneval_active == 0 ? 'checked' : '' }}
                                        {{ !$linea->activo && $estudio->linea_coneval_id != $linea->id ? 'disabled' : '' }}>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">

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

                @if(isset($estudio) && $estudio->id)
                <div class="card mt-4">
                    <div class="card-header bg-info text-white py-2">
                        <h6 class="mb-0">
                            <i class="bi bi-graph-up me-2"></i>Evaluación de Vulnerabilidad
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        @php
                        // Calcular puntuación total
                        $puntosConeval = $estudio->coneval_active ? 3 : 0;
                        $puntosServicioSalud = $estudio->servicioSalud ? $estudio->servicioSalud->puntos : 0;
                        $puntosEscolaridad = $estudio->escolaridad ? $estudio->escolaridad->puntos : 0;
                        $puntosTotales = $puntosConeval + $puntosServicioSalud + $puntosEscolaridad;

                        // Determinar nivel de vulnerabilidad
                        if ($puntosTotales >= 1 && $puntosTotales <= 3) {
                            $nivelVulnerabilidad='Nivel Leve' ;
                            $claseBadge='bg-success' ;
                            $claseProgress='bg-success' ;
                            } elseif ($puntosTotales>= 4 && $puntosTotales <= 6) {
                                $nivelVulnerabilidad='Nivel Moderado' ;
                                $claseBadge='bg-warning text-dark' ;
                                $claseProgress='bg-warning' ;
                                } elseif ($puntosTotales>= 7 && $puntosTotales <= 9) {
                                    $nivelVulnerabilidad='Nivel Severo' ;
                                    $claseBadge='bg-danger' ;
                                    $claseProgress='bg-danger' ;
                                    } else {
                                    $nivelVulnerabilidad='Sin datos' ;
                                    $claseBadge='bg-secondary' ;
                                    $claseProgress='bg-secondary' ;
                                    }

                                    $maximoPuntos=9;
                                    $porcentaje=($puntosTotales / $maximoPuntos) * 100;
                                    @endphp

                                    <!-- Puntuaciones individuales en línea -->
                                    <div class="row text-center mb-3">
                                        <div class="col-4 border-end">
                                            <small class="text-dark d-block"><strong>CONEVAL</strong></small>
                                            <small class="{{ $puntosConeval > 0 ? 'text-dark' : 'text-muted' }}">
                                                {{ $puntosConeval }} pts
                                            </small>
                                        </div>
                                        <div class="col-4 border-end">
                                            <small class="text-dark d-block"><strong>Salud</strong></small>
                                            <small class="{{ $puntosServicioSalud > 0 ? 'text-dark' : 'text-muted' }}">
                                                {{ $puntosServicioSalud }} pts
                                            </small>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-dark d-block"><strong>Escolaridad</strong></small>
                                            <small class="{{ $puntosEscolaridad > 0 ? 'text-dark' : 'text-muted' }}">
                                                {{ $puntosEscolaridad }} pts
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Barra de progreso compacta -->
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar {{ $claseProgress }}"
                                            role="progressbar"
                                            style="width: {{ $porcentaje }}%"
                                            aria-valuenow="{{ $puntosTotales }}"
                                            aria-valuemin="0"
                                            aria-valuemax="{{ $maximoPuntos }}">
                                            <strong>{{ $puntosTotales }}/{{ $maximoPuntos }}</strong>
                                        </div>
                                    </div>

                                    <!-- Resultado final compacto -->
                                    <div class="text-center">
                                        <span class="badge {{ $claseBadge }} ">{{ $nivelVulnerabilidad }}</span>
                                    </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @else
        <p class="text-muted text-center py-3">
            El estudio debe ser guardado primero para gestionar integrantes del hogar.
        </p>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== INICIANDO SCRIPT INTEGRANTES DINÁMICOS ===');

        function inicializarConeval() {
            const lineaConevalIdForm = document.getElementById('linea_coneval_id');
            const conevalActiveForm = document.getElementById('coneval_active');

            if (lineaConevalIdForm && !lineaConevalIdForm.value) {
                const estudioLineaId = '{{ $estudio->linea_coneval_id ?? "" }}';
                if (estudioLineaId && estudioLineaId !== 'null') {
                    lineaConevalIdForm.value = estudioLineaId;
                }
            }

            if (conevalActiveForm && !conevalActiveForm.value) {
                const estudioActive = '{{ $estudio->coneval_active ?? "" }}';
                if (estudioActive && estudioActive !== 'null') {
                    conevalActiveForm.value = estudioActive;
                }
            }
        }

        inicializarConeval();

        document.getElementById('btnAgregarIntegrante')?.addEventListener('click', function() {
            const tbody = document.getElementById('tbodyIntegrantes');
            const filas = tbody.querySelectorAll('tr:not([data-nuevo])');

            let maxIntegrante = 1;
            filas.forEach(fila => {
                const num = parseInt(fila.getAttribute('data-integrante'));
                if (num > maxIntegrante) maxIntegrante = num;
            });
            const nuevoNumero = maxIntegrante + 1;

            const nuevaFila = document.createElement('tr');
            nuevaFila.setAttribute('data-integrante', nuevoNumero);
            nuevaFila.setAttribute('data-nuevo', 'true');
            nuevaFila.innerHTML = `
            <td>
                <strong>Integrante ${nuevoNumero}</strong>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">$</span>
                    <input type="number" 
                           class="form-control ingreso-input-nuevo" 
                           step="0.01" 
                           min="0"
                           placeholder="0.00">
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-success guardar-nuevo">
                    <i class="bi bi-check-lg"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger cancelar-nuevo">
                    <i class="bi bi-x-lg"></i>
                </button>
            </td>
        `;

            tbody.appendChild(nuevaFila);
            nuevaFila.querySelector('.ingreso-input-nuevo').focus();
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.guardar-nuevo')) {
                const fila = e.target.closest('tr');
                const input = fila.querySelector('.ingreso-input-nuevo');
                const ingreso = parseFloat(input.value) || 0;

                if (ingreso < 0) {
                    alert('El ingreso no puede ser negativo');
                    return;
                }

                const integranteNum = parseInt(fila.getAttribute('data-integrante'));

                const formData = new FormData();
                formData.append('estudio_socioeconomico_id', '{{ $estudio->id }}');
                formData.append('integrante', integranteNum);
                formData.append('ingreso_mensual', ingreso);

                const btn = e.target.closest('.guardar-nuevo');
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-hourglass"></i>';
                btn.disabled = true;

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
                            location.reload();
                        } else {
                            alert(data.error || 'Error al guardar');
                            btn.innerHTML = originalHtml;
                            btn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al guardar el integrante. La página se recargará para mostrar los cambios.');
                        location.reload();
                    });
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.cancelar-nuevo')) {
                const fila = e.target.closest('tr');
                if (confirm('¿Cancelar la adición de este integrante?')) {
                    fila.remove();
                }
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.guardar-ingreso')) {
                const btn = e.target.closest('.guardar-ingreso');
                const id = btn.getAttribute('data-id');
                const fila = btn.closest('tr');
                const input = fila.querySelector('.ingreso-input');
                const ingreso = parseFloat(input.value) || 0;

                if (ingreso < 0) {
                    alert('El ingreso no puede ser negativo');
                    return;
                }

                const formData = new FormData();
                formData.append('ingreso_mensual', ingreso);
                formData.append('_method', 'PUT');

                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-hourglass"></i>';
                btn.disabled = true;

                fetch('{{ route("integrantes-hogar.update", "") }}/' + id, {
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
                            actualizarTotales();
                            btn.innerHTML = '<i class="bi bi-check-lg"></i>';
                            setTimeout(() => {
                                btn.innerHTML = originalHtml;
                            }, 2000);
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            alert(data.error || 'Error al actualizar');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al actualizar el ingreso');
                    })
                    .finally(() => {
                        btn.disabled = false;
                    });
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.eliminar-integrante')) {
                const btn = e.target.closest('.eliminar-integrante');
                const id = btn.getAttribute('data-id');
                const fila = btn.closest('tr');
                const integranteNum = parseInt(fila.getAttribute('data-integrante'));
                
                if (integranteNum === 1) {
                    alert('El Beneficiario Principal no puede ser eliminado.');
                    return;
                }
                
                if (!confirm('¿Eliminar este integrante del hogar?')) return;

                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-hourglass"></i>';
                btn.disabled = true;

                fetch('{{ route("integrantes-hogar.destroy", "") }}/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'DELETE'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Error al eliminar');
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el integrante');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                });
            }
        });

        function actualizarTotales() {
            const filas = document.querySelectorAll('#tbodyIntegrantes tr:not([data-nuevo])');
            let totalIngresos = 0;
            let totalPersonas = 0;

            filas.forEach(fila => {
                const input = fila.querySelector('.ingreso-input');
                if (input) {
                    const ingreso = parseFloat(input.value) || 0;
                    totalIngresos += ingreso;
                    totalPersonas++;
                }
            });

            const ingresoPerCapita = totalPersonas > 0 ? totalIngresos / totalPersonas : 0;

            const totalIngresosEl = document.getElementById('totalIngresos');
            const totalPersonasEl = document.getElementById('totalPersonas');
            const ingresoPerCapitaEl = document.getElementById('ingresoPerCapita');

            if (totalIngresosEl) {
                totalIngresosEl.textContent = '$' + totalIngresos.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            if (totalPersonasEl) {
                totalPersonasEl.textContent = totalPersonas;
            }
            if (ingresoPerCapitaEl) {
                ingresoPerCapitaEl.textContent = '$' + ingresoPerCapita.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const target = e.target;
                if (target.classList.contains('ingreso-input-nuevo')) {
                    const fila = target.closest('tr');
                    const btn = fila.querySelector('.guardar-nuevo');
                    if (btn) btn.click();
                } else if (target.classList.contains('ingreso-input')) {
                    const fila = target.closest('tr');
                    const btn = fila.querySelector('.guardar-ingreso');
                    if (btn) btn.click();
                }
            }
        });

        const conevalRadios = document.querySelectorAll('.coneval-radio');
        const lineaConevalIdForm = document.getElementById('linea_coneval_id');
        const conevalActiveForm = document.getElementById('coneval_active');

        conevalRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    const lineaId = this.getAttribute('data-linea-id');
                    const active = this.getAttribute('data-active');

                    if (lineaConevalIdForm) {
                        lineaConevalIdForm.value = lineaId;
                    }
                    if (conevalActiveForm) {
                        conevalActiveForm.value = active;
                    }
                }
            });
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('ingreso-input')) {
                const input = e.target;
                const id = input.getAttribute('data-id');
                const fila = input.closest('tr');
                const ingreso = parseFloat(input.value) || 0;
                
                if (ingreso < 0) {
                    alert('El ingreso no puede ser negativo');
                    input.value = 0;
                    return;
                }

                const tdAcciones = fila.querySelector('td:last-child');
                const loadingIndicator = document.createElement('span');
                loadingIndicator.className = 'text-muted small ms-2';
                loadingIndicator.id = 'loading-indicator-' + id;
                loadingIndicator.innerHTML = '<i class="bi bi-hourglass"></i> Guardando...';
                tdAcciones.appendChild(loadingIndicator);

                const formData = new FormData();
                formData.append('ingreso_mensual', ingreso);
                formData.append('_method', 'PUT');

                fetch('{{ route("integrantes-hogar.update", "") }}/' + id, {
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
                        location.reload();
                    } else {
                        const indicator = document.getElementById('loading-indicator-' + id);
                        if (indicator) indicator.remove();
                        alert(data.error || 'Error al actualizar');
                        input.classList.add('is-invalid');
                        setTimeout(() => {
                            input.classList.remove('is-invalid');
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const indicator = document.getElementById('loading-indicator-' + id);
                    if (indicator) indicator.remove();
                    alert('Error al actualizar el ingreso');
                    input.classList.add('is-invalid');
                    setTimeout(() => {
                        input.classList.remove('is-invalid');
                    }, 2000);
                });
            }
        });

    });
</script>