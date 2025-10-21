<div class="tab-pane fade" id="paso3" role="tabpanel">
    <fieldset class="border rounded p-3 mb-4">
        <legend class="float-none w-auto px-3 fw-bold text-dark">
            <i class="bi bi-clipboard-check me-2"></i>Evaluación de la seguridad alimentaria
        </legend>

        <!-- Bloque 1: Solo Adultos -->
        <div class="card mb-4">
            <div class="card-header text-white">
                <h6 class="mb-0">
                    <i class="bi bi-person me-2"></i>Bloque 1 - Solo Adultos en el Hogar
                </h6>
            </div>
            <div class="card-body">
                @foreach([
                'preocupa_sin_alimentos' => '¿Se preocupó por quedarse sin alimentos por falta de dinero o recursos?',
                'alimentos_no_alcanzaron' => '¿Los alimentos que compró no le alcanzaron y no tuvo dinero para comprar más?',
                'dieta_poco_variada_adultos' => '¿Comió una dieta poco variada por falta de dinero o recursos?',
                'adultos_comieron_menos' => '¿Algún adulto comió menos de lo que debía por falta de dinero?',
                'adultos_hambre_sin_comer' => '¿Algún adulto sintió hambre, pero no comió por falta de dinero?',
                'adultos_dejaron_comer_dia' => '¿Algún adulto dejó de comer durante todo un día por falta de dinero o recursos?'
                ] as $field => $pregunta)
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label">{{ $pregunta }}</label>
                    </div>
                    <div class="col-md-4">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="{{ $field }}"
                                id="{{ $field }}_si" value="1"
                                {{ old($field, $estudio->$field ?? '') == '1' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="{{ $field }}_si">Sí</label>

                            <input type="radio" class="btn-check" name="{{ $field }}"
                                id="{{ $field }}_no" value="0"
                                {{ old($field, $estudio->$field ?? '') == '0' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary" for="{{ $field }}_no">No</label>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Resumen Bloque 1 -->
                <div class="card mt-3 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold">Resumen Bloque 1 - Solo Adultos</h6>
                        @php
                        $puntosBloque1 = 0;
                        if(isset($estudio) && $estudio->id) {
                        $camposBloque1 = [
                        'preocupa_sin_alimentos', 'alimentos_no_alcanzaron', 'dieta_poco_variada_adultos',
                        'adultos_comieron_menos', 'adultos_hambre_sin_comer', 'adultos_dejaron_comer_dia'
                        ];
                        foreach($camposBloque1 as $campo) {
                        $puntosBloque1 += $estudio->$campo ? 1 : 0;
                        }
                        }

                        $nivelBloque1 = match(true) {
                        $puntosBloque1 == 0 => 'Seguridad alimentaria',
                        $puntosBloque1 <= 2=> 'Inseguridad alimentaria leve',
                            $puntosBloque1 <= 4=> 'Inseguridad alimentaria moderada',
                                default => 'Inseguridad alimentaria severa'
                                };
                                @endphp
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar 
                                @if($puntosBloque1 >= 5) bg-danger
                                @elseif($puntosBloque1 >= 3) bg-warning
                                @elseif($puntosBloque1 >= 1) bg-success
                                @else bg-info @endif"
                                        style="width: {{ ($puntosBloque1 / 6) * 100 }}%">
                                        <strong>{{ $puntosBloque1 }} / 6 puntos</strong>
                                    </div>
                                </div>
                                <div class="mt-2 text-center">
                                    <span class="badge 
                                @if($puntosBloque1 >= 5) bg-danger
                                @elseif($puntosBloque1 >= 3) bg-warning text-dark
                                @elseif($puntosBloque1 >= 1) bg-success
                                @else bg-info @endif">
                                        {{ $nivelBloque1 }}
                                    </span>
                                </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bloque 2: Adultos y Menores -->
        <div class="card">
            <div class="card-header text-light">
                <h6 class="mb-0">
                    <i class="bi bi-people me-2"></i>Bloque 2 - Adultos y Menores en el Hogar (Si no hay menores, omitir)
                </h6>
            </div>
            <div class="card-body">
                @foreach([
                'menores_dieta_poco_variada' => '¿Algún menor comió una dieta poco variada por falta de recursos?',
                'menores_comieron_menos' => '¿Algún menor comió menos de lo que debía por falta de recursos?',
                'menores_hambre_sin_comer' => '¿Algún menor sintió hambre, pero no comió por falta de recursos?',
                'menores_sin_comer_dia' => '¿Algún menor se quedó sin comer durante todo un día por falta de recursos?',
                'menores_sin_alimentos_saludables' => '¿Usted dejó de darle alimentos saludables o suficientes a algún menor por falta de dinero?',
                'menores_dieta_alimentos_baratos' => '¿Algún menor tuvo una dieta basada solo en alimentos baratos o poco nutritivos?'
                ] as $field => $pregunta)
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label">{{ $pregunta }}</label>
                    </div>
                    <div class="col-md-4">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="{{ $field }}"
                                id="{{ $field }}_si" value="1"
                                {{ old($field, $estudio->$field ?? '') == '1' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary" for="{{ $field }}_si">Sí</label>

                            <input type="radio" class="btn-check" name="{{ $field }}"
                                id="{{ $field }}_no" value="0"
                                {{ old($field, $estudio->$field ?? '') == '0' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary" for="{{ $field }}_no">No</label>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Bloque 2: Adultos y Menores - CORREGIDO -->

                @php
                $puntosBloque2 = 0;
                $mostrarResumenBloque2 = false; // 🔹 Controla si mostrar u ocultar el bloque 2

                if (isset($estudio) && $estudio->id) {
                // Bloque 1 (solo adultos)
                $camposBloque1 = [
                'preocupa_sin_alimentos', 'alimentos_no_alcanzaron', 'dieta_poco_variada_adultos',
                'adultos_comieron_menos', 'adultos_hambre_sin_comer', 'adultos_dejaron_comer_dia'
                ];

                // Bloque 2 (adultos y menores)
                $camposBloque2 = [
                'menores_dieta_poco_variada', 'menores_comieron_menos', 'menores_hambre_sin_comer',
                'menores_sin_comer_dia', 'menores_sin_alimentos_saludables', 'menores_dieta_alimentos_baratos'
                ];

                // Calcular puntos de Bloque 1
                $puntosBloque1 = 0;
                foreach ($camposBloque1 as $campo) {
                $puntosBloque1 += $estudio->$campo ? 1 : 0;
                }

                // Verificar si todas las respuestas del Bloque 2 son null
                $todasNulasBloque2 = true;
                foreach ($camposBloque2 as $campo) {
                if (!is_null($estudio->$campo)) {
                $todasNulasBloque2 = false;
                break;
                }
                }

                // Si el bloque 2 tiene respuestas (0 o 1), hacemos la suma
                if (!$todasNulasBloque2) {
                $puntosBloque2Solo = 0;
                foreach ($camposBloque2 as $campo) {
                $puntosBloque2Solo += $estudio->$campo ? 1 : 0;
                }
                $puntosBloque2 = $puntosBloque1 + $puntosBloque2Solo;
                $mostrarResumenBloque2 = true;
                }
                }

                // Evaluación total (máx 12 puntos)
                if ($mostrarResumenBloque2) {
                $nivelBloque2 = match (true) {
                $puntosBloque2 == 0 => [
                'texto' => 'Seguridad Alimentaria',
                'descripcion' => 'No se identificaron problemas de acceso a alimentos en el hogar',
                'color' => 'success',
                'icono' => 'bi-check-circle'
                ],
                $puntosBloque2 >= 1 && $puntosBloque2 <= 3=> [
                    'texto' => 'Inseguridad Alimentaria Leve',
                    'descripcion' => 'Preocupación o problemas marginales en la alimentación del hogar',
                    'color' => 'info',
                    'icono' => 'bi-exclamation-circle'
                    ],
                    $puntosBloque2 >= 4 && $puntosBloque2 <= 7=> [
                        'texto' => 'Inseguridad Alimentaria Moderada',
                        'descripcion' => 'Reducción en la calidad y cantidad de alimentos para el hogar',
                        'color' => 'warning',
                        'icono' => 'bi-exclamation-triangle'
                        ],
                        $puntosBloque2 >= 8 => [
                        'texto' => 'Inseguridad Alimentaria Severa',
                        'descripcion' => 'Problemas graves de alimentación que afectan a todo el hogar',
                        'color' => 'danger',
                        'icono' => 'bi-x-circle'
                        ],
                        };
                        }
                        @endphp

                        @if($mostrarResumenBloque2)
                        <div class="card mt-3 bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold">Resumen Bloque 2 - Adultos y Menores en el Hogar</h6>

                                <!-- Barra de Progreso Mejorada -->
                                <div class="progress mb-3" style="height: 30px;">
                                    <div class="progress-bar bg-{{ $nivelBloque2['color'] }} 
                @if($nivelBloque2['color'] == 'warning') text-dark @endif"
                                        role="progressbar"
                                        style="width: {{ ($puntosBloque2 / 12) * 100 }}%"
                                        aria-valuenow="{{ $puntosBloque2 }}"
                                        aria-valuemin="0"
                                        aria-valuemax="12">
                                        <strong>{{ $puntosBloque2 }} / 12 puntos</strong>
                                    </div>
                                </div>

                                
                                

                                <!-- Desglose de Puntos -->
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <strong>Desglose:</strong>
                                        {{ $puntosBloque2 - ($puntosBloque2 - array_sum(array_map(function($campo) use ($estudio) { 
                    return $estudio->$campo ? 1 : 0; 
                }, $camposBloque1))) }} pts (Bloque 1) +
                                        {{ array_sum(array_map(function($campo) use ($estudio) { 
                    return $estudio->$campo ? 1 : 0; 
                }, $camposBloque2)) }} pts (Bloque 2)
                                    </small>
                                </div>

                                <!-- Indicadores Visuales Adicionales -->
                                <div class="row mt-3 text-center">
                                    <div class="col-3">
                                        <div class="border rounded p-2 
                    @if($puntosBloque2 == 0) border-success bg-info text-white @else border-secondary @endif">
                                            <small><strong>0 pts</strong><br>Seguridad</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded p-2 
                    @if($puntosBloque2 >= 1 && $puntosBloque2 <= 3) border-info bg-success text-white @else border-secondary @endif">
                                            <small><strong>1-3 pts</strong><br>Leve</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded p-2 
                    @if($puntosBloque2 >= 4 && $puntosBloque2 <= 7) border-warning bg-warning text-dark @else border-secondary @endif">
                                            <small><strong>4-7 pts</strong><br>Moderada</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded p-2 
                    @if($puntosBloque2 >= 8) border-danger bg-danger text-white @else border-secondary @endif">
                                            <small><strong>8-12 pts</strong><br>Severa</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
            </div>
        </div>
    </fieldset>

    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" onclick="anteriorPaso(2)">
            <i class="bi bi-arrow-left"></i> Anterior
        </button>
        <button type="submit" class="btn btn-success" id="guardarEstudioCompleto">
            <i class="bi bi-check-circle me-1"></i> Guardar estudio completo
        </button>
    </div>
</div>