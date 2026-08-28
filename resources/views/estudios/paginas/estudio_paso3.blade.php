<div class="tab-pane fade" id="paso3" role="tabpanel">
    <fieldset class="border rounded p-3 mb-4">
        <legend class="float-none w-auto px-3 fw-bold text-dark">
            <i class="bi bi-clipboard-check me-2"></i>Evaluación de la seguridad alimentaria
        </legend>

        <div class="card mb-4">
            <div class="card-header text-white">
                <h6 class="mb-0">
                    <i class="bi bi-person me-2"></i>Solo Adultos en el Hogar
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
<div class="row mb-3 align-items-center">
    <div class="col-md-9">
        <label class="form-label mb-0">{{ $pregunta }}</label>
    </div>
    <div class="col-md-3 text-end">
        <div class="btn-group" role="group" style="width: 120px;">
            <input type="radio" class="btn-check" name="{{ $field }}"
                id="{{ $field }}_si" value="1" required
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

            </div>
        </div>

        <div class="card">
            <div class="card-header text-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-people me-2"></i>Adultos y Menores en el Hogar (Si no hay menores, omitir)
                </h6>
                <button type="button" class="btn btn-sm btn-outline-light toggle-bloque" data-bloque="menores">
                    <i class="bi bi-eye-slash"></i> Mostrar
                </button>
            </div>
            <div class="card-body bloque-menores" style="display: none;">
                @foreach([
                'menores_dieta_poco_variada' => '¿Algún menor comió una dieta poco variada por falta de recursos?',
                'menores_comieron_menos' => '¿Algún menor comió menos de lo que debía por falta de recursos?',
                'menores_hambre_sin_comer' => '¿Algún menor sintió hambre, pero no comió por falta de recursos?',
                'menores_sin_comer_dia' => '¿Algún menor se quedó sin comer durante todo un día por falta de recursos?',
                'menores_sin_alimentos_saludables' => '¿Usted dejó de darle alimentos saludables o suficientes a algún menor por falta de dinero?',
                'menores_dieta_alimentos_baratos' => '¿Algún menor tuvo una dieta basada solo en alimentos baratos o poco nutritivos?'
                ] as $field => $pregunta)
                <div class="row mb-3 align-items-center">
                    <div class="col-md-9">
                        <label class="form-label mb-0">{{ $pregunta }}</label>
                    </div>
                    <div class="col-md-3 text-end">
                        <div class="btn-group" role="group" style="width: 120px;">
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
            </div>
        </div>

        @php
        $puntosBloque1 = 0;
        $puntosBloque2 = 0;
        $mostrarResumenBloque1 = false;
        $mostrarResumenBloque2 = false;

        if (isset($estudio) && $estudio->id) {
        $camposBloque1 = [
        'preocupa_sin_alimentos', 'alimentos_no_alcanzaron', 'dieta_poco_variada_adultos',
        'adultos_comieron_menos', 'adultos_hambre_sin_comer', 'adultos_dejaron_comer_dia'
        ];

        $camposBloque2 = [
        'menores_dieta_poco_variada', 'menores_comieron_menos', 'menores_hambre_sin_comer',
        'menores_sin_comer_dia', 'menores_sin_alimentos_saludables', 'menores_dieta_alimentos_baratos'
        ];

        // Calcular puntos de Bloque 1
        foreach ($camposBloque1 as $campo) {
        $puntosBloque1 += $estudio->$campo ? 1 : 0;
        }

        // Verificar si hay respuestas en el Bloque 2
        $hayRespuestasBloque2 = false;
        foreach ($camposBloque2 as $campo) {
        if (!is_null($estudio->$campo)) {
        $hayRespuestasBloque2 = true;
        break;
        }
        }

        // Si hay respuestas en Bloque 2, calcular puntos combinados
        if ($hayRespuestasBloque2) {
        $puntosBloque2Solo = 0;
        foreach ($camposBloque2 as $campo) {
        $puntosBloque2Solo += $estudio->$campo ? 1 : 0;
        }
        $puntosBloque2 = $puntosBloque1 + $puntosBloque2Solo;
        $mostrarResumenBloque2 = true;
        } else {
        // Si no hay respuestas en Bloque 2, mostrar solo Bloque 1
        $mostrarResumenBloque1 = true;
        }
        }

        // Determinar niveles
        if ($mostrarResumenBloque1) {
        $nivelBloque1 = match(true) {
        $puntosBloque1 == 0 => 'Seguridad alimentaria',
        $puntosBloque1 <= 2=> 'Inseguridad alimentaria leve',
            $puntosBloque1 <= 4=> 'Inseguridad alimentaria moderada',
                default => 'Inseguridad alimentaria severa'
                };
                }

                if ($mostrarResumenBloque2) {
                $nivelBloque2 = match(true) {
                $puntosBloque2 == 0 => 'Seguridad alimentaria',
                $puntosBloque2 <= 3=> 'Inseguridad alimentaria leve',
                    $puntosBloque2 <= 7=> 'Inseguridad alimentaria moderada',
                        default => 'Inseguridad alimentaria severa'
                        };
                        }
                        @endphp

                        @if($mostrarResumenBloque1)
                        <div class="card mt-4 bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold">Resumen Final - Solo Adultos</h6>
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
                        @endif

                        @if($mostrarResumenBloque2)
                        <div class="card mt-4 bg-light">
                            <div class="card-body">
                                <h6 class="fw-bold">Resumen Final - Adultos y Menores</h6>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar 
                        @if($puntosBloque2 >= 8) bg-danger
                        @elseif($puntosBloque2 >= 4) bg-warning
                        @elseif($puntosBloque2 >= 1) bg-success
                        @else bg-info @endif"
                                        style="width: {{ ($puntosBloque2 / 12) * 100 }}%">
                                        <strong>{{ $puntosBloque2 }} / 12 puntos</strong>
                                    </div>
                                </div>
                                <div class="mt-2 text-center">
                                    <span class="badge 
                        @if($puntosBloque2 >= 8) bg-danger
                        @elseif($puntosBloque2 >= 4) bg-warning text-dark
                        @elseif($puntosBloque2 >= 1) bg-success
                        @else bg-info @endif">
                                        {{ $nivelBloque2 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    function hayRespuestasMenores() {
        const campos = [
            'menores_dieta_poco_variada', 'menores_comieron_menos', 'menores_hambre_sin_comer',
            'menores_sin_comer_dia', 'menores_sin_alimentos_saludables', 'menores_dieta_alimentos_baratos'
        ];
        
        return campos.some(campo => {
            const si = document.querySelector(`input[name="${campo}"][value="1"]`);
            const no = document.querySelector(`input[name="${campo}"][value="0"]`);
            return (si && si.checked) || (no && no.checked);
        });
    }

    function toggleBloque(bloque) {
        const cuerpo = document.querySelector('.bloque-menores');
        const boton = document.querySelector('.toggle-bloque');
        
        if (cuerpo.style.display === 'none') {
            cuerpo.style.display = 'block';
            boton.innerHTML = '<i class="bi bi-eye-slash-fill"></i> Ocultar';
            boton.classList.remove('btn-outline-light');
            boton.classList.add('btn-light', 'text-dark');
        } else {
            cuerpo.style.display = 'none';
            boton.innerHTML = '<i class="bi bi-eye"></i> Mostrar';
            boton.classList.remove('btn-light', 'text-dark');
            boton.classList.add('btn-outline-light');
        }
    }

    document.querySelector('.toggle-bloque').addEventListener('click', function() {
        toggleBloque('menores');
    });

    if (hayRespuestasMenores()) {
        toggleBloque('menores');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const camposBloque2 = [
        'menores_dieta_poco_variada',
        'menores_comieron_menos',
        'menores_hambre_sin_comer',
        'menores_sin_comer_dia',
        'menores_sin_alimentos_saludables',
        'menores_dieta_alimentos_baratos'
    ];

    const radiosBloque2 = camposBloque2.flatMap(campo => [
        document.querySelector(`input[name="${campo}"][value="1"]`),
        document.querySelector(`input[name="${campo}"][value="0"]`)
    ]);

    function bloque2Iniciado() {
        return camposBloque2.some(campo => {
            const si = document.querySelector(`input[name="${campo}"][value="1"]`);
            const no = document.querySelector(`input[name="${campo}"][value="0"]`);
            return (si && si.checked) || (no && no.checked);
        });
    }

    function actualizarRequired() {
        const activar = bloque2Iniciado();
        camposBloque2.forEach(campo => {
            const si = document.querySelector(`input[name="${campo}"][value="1"]`);
            const no = document.querySelector(`input[name="${campo}"][value="0"]`);

            if (activar) {
                si?.setAttribute('required', true);
            } else {
                si?.removeAttribute('required');
            }
        });
    }

    radiosBloque2.forEach(radio => {
        if (radio) {
            radio.addEventListener('change', actualizarRequired);
        }
    });

    const form = document.getElementById('estudioForm');
    form.addEventListener('submit', function (e) {
        const iniciado = bloque2Iniciado();

        if (iniciado) {
            const incompletos = camposBloque2.filter(campo => {
                const si = document.querySelector(`input[name="${campo}"][value="1"]`);
                const no = document.querySelector(`input[name="${campo}"][value="0"]`);
                return !(si.checked || no.checked);
            });

            if (incompletos.length > 0) {
                e.preventDefault();
                alert('Por favor responde todas las preguntas del bloque "Adultos y Menores" si decides completarlo.');
            }
        }
    });
});


</script>