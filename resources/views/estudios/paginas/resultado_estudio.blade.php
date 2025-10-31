@if(isset($estudio) && $estudio->res_estudio_1 && $estudio->res_estudio_2 && $estudio->res_estudio_3)
@php
    // Función auxiliar para definir color según el nivel
    function colorResultado($valor) {
        switch (strtolower($valor)) {
            case 'leve':
                return 'text-success';
            case 'moderada':
                return 'text-warning';
            case 'severa':
                return 'text-danger';
            default:
                return 'text-secondary';
        }
    }

    // Calcular puntos
    $puntosEstudios = [
        'Leve' => 1,
        'Moderada' => 2,
        'Severa' => 3
    ];

    $puntosEstudio1 = $puntosEstudios[$estudio->res_estudio_1] ?? 0;
    $puntosEstudio2 = $puntosEstudios[$estudio->res_estudio_2] ?? 0;
    
    // Determinar puntos para estudio 3 (seguridad alimentaria) según el resumen que se mostró
    $puntosEstudio3 = 0;
    $textoEstudio3 = 'No evaluado';
    
    // Verificar si hay respuestas en el bloque de menores para determinar qué resumen se usó
    $camposBloque2 = [
        'menores_dieta_poco_variada', 'menores_comieron_menos', 'menores_hambre_sin_comer',
        'menores_sin_comer_dia', 'menores_sin_alimentos_saludables', 'menores_dieta_alimentos_baratos'
    ];
    
    $hayMenores = false;
    foreach ($camposBloque2 as $campo) {
        if (!is_null($estudio->$campo)) {
            $hayMenores = true;
            break;
        }
    }
    
    if ($hayMenores) {
        // Se usó el resumen de Adultos y Menores (máximo 12 puntos)
        $camposBloque1 = [
            'preocupa_sin_alimentos', 'alimentos_no_alcanzaron', 'dieta_poco_variada_adultos',
            'adultos_comieron_menos', 'adultos_hambre_sin_comer', 'adultos_dejaron_comer_dia'
        ];
        $camposBloque2 = [
            'menores_dieta_poco_variada', 'menores_comieron_menos', 'menores_hambre_sin_comer',
            'menores_sin_comer_dia', 'menores_sin_alimentos_saludables', 'menores_dieta_alimentos_baratos'
        ];
        
        $puntosBloque1 = 0;
        foreach ($camposBloque1 as $campo) {
            $puntosBloque1 += $estudio->$campo ? 1 : 0;
        }
        
        $puntosBloque2Solo = 0;
        foreach ($camposBloque2 as $campo) {
            $puntosBloque2Solo += $estudio->$campo ? 1 : 0;
        }
        
        $puntosTotalesAlimentaria = $puntosBloque1 + $puntosBloque2Solo;
        
        // Convertir a escala de 1-3 puntos para el estudio 3
        if ($puntosTotalesAlimentaria >= 8) {
            $puntosEstudio3 = 3; // Severa
            $textoEstudio3 = 'Severa';
        } elseif ($puntosTotalesAlimentaria >= 4) {
            $puntosEstudio3 = 2; // Moderada
            $textoEstudio3 = 'Moderada';
        } elseif ($puntosTotalesAlimentaria >= 1) {
            $puntosEstudio3 = 1; // Leve
            $textoEstudio3 = 'Leve';
        } else {
            $puntosEstudio3 = 0; // Seguridad alimentaria
            $textoEstudio3 = 'Seguridad';
        }
    } else {
        // Se usó el resumen de Solo Adultos (máximo 6 puntos)
        $camposBloque1 = [
            'preocupa_sin_alimentos', 'alimentos_no_alcanzaron', 'dieta_poco_variada_adultos',
            'adultos_comieron_menos', 'adultos_hambre_sin_comer', 'adultos_dejaron_comer_dia'
        ];
        
        $puntosBloque1 = 0;
        foreach ($camposBloque1 as $campo) {
            $puntosBloque1 += $estudio->$campo ? 1 : 0;
        }
        
        // Convertir a escala de 1-3 puntos para el estudio 3
        if ($puntosBloque1 >= 5) {
            $puntosEstudio3 = 3; // Severa
            $textoEstudio3 = 'Severa';
        } elseif ($puntosBloque1 >= 3) {
            $puntosEstudio3 = 2; // Moderada
            $textoEstudio3 = 'Moderada';
        } elseif ($puntosBloque1 >= 1) {
            $puntosEstudio3 = 1; // Leve
            $textoEstudio3 = 'Leve';
        } else {
            $puntosEstudio3 = 0; // Seguridad alimentaria
            $textoEstudio3 = 'Seguridad';
        }
    }
    
    $puntosOcupacion = $estudio->beneficiario && $estudio->beneficiario->ocupacion
        ? min((int)$estudio->beneficiario->ocupacion->puntos, 3)
        : 0;
    $puntosTotales = $puntosEstudio1 + $puntosEstudio2 + $puntosEstudio3 + $puntosOcupacion;
    
    // Determinar resultado total basado en puntos
    if ($puntosTotales >= 10) {
        $resultadoTotal = 'Severa';
    } elseif ($puntosTotales >= 7) {
        $resultadoTotal = 'Moderada';
    } else {
        $resultadoTotal = 'Leve';
    }
@endphp

<div class="card mt-4 border-0 shadow-lg animate__animated animate__fadeInUp">
    <div class="card-header bg-dark bg-gradient text-white d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            <i class="bi bi-clipboard2-check-fill me-2"></i> Resultado Final del Estudio
        </h5>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark fs-6 shadow-sm px-3 py-2">
                <i class="bi bi-trophy-fill me-1"></i> {{ $resultadoTotal }}
            </span>
            <span class="badge text-white fs-6 shadow-sm px-3 py-2">
                <i class="bi bi-calculator me-1"></i> {{ $puntosTotales }}/12 pts
            </span>
        </div>
    </div>

    <div class="card-body p-4 bg-light">
        <div class="row text-center gy-3">
            <!-- Ocupación -->
            <div class="col-md-2-4 col-6">
                <div class="p-3 border rounded-3 bg-white shadow-sm h-100">
                    <i class="bi bi-briefcase-fill fs-3 text-secondary mb-2"></i>
                    <h6 class="fw-bold text-muted mb-1">Ocupación</h6>
                    <small class="text-muted">{{ $puntosOcupacion }} punto(s)</small>
                </div>
            </div>

            <!-- Económico -->
            <div class="col-md-2-4 col-6">
                <div class="p-3 border rounded-3 bg-white shadow-sm h-100">
                    <i class="bi bi-cash-coin fs-3 text-secondary mb-2"></i>
                    <h6 class="fw-bold text-muted mb-1">Económico</h6>
                    <span class="fs-5 fw-semibold {{ colorResultado($estudio->res_estudio_1) }} d-block mb-1">
                        {{ $estudio->res_estudio_1 }}
                    </span>
                    <small class="text-muted">{{ $puntosEstudio1 }} punto(s)</small>
                </div>
            </div>

            <!-- Vivienda -->
            <div class="col-md-2-4 col-6">
                <div class="p-3 border rounded-3 bg-white shadow-sm h-100">
                    <i class="bi bi-house-door-fill fs-3 text-secondary mb-2"></i>
                    <h6 class="fw-bold text-muted mb-1">Vivienda</h6>
                    <span class="fs-5 fw-semibold {{ colorResultado($estudio->res_estudio_2) }} d-block mb-1">
                        {{ $estudio->res_estudio_2 }}
                    </span>
                    <small class="text-muted">{{ $puntosEstudio2 }} punto(s)</small>
                </div>
            </div>

            <!-- Alimentaria -->
            <div class="col-md-2-4 col-6">
                <div class="p-3 border rounded-3 bg-white shadow-sm h-100">
                    <i class="bi bi-basket-fill fs-3 text-secondary mb-2"></i>
                    <h6 class="fw-bold text-muted mb-1">Alimentaria</h6>
                    <span class="fs-5 fw-semibold {{ colorResultado($textoEstudio3) }} d-block mb-1">
                        {{ $textoEstudio3 }}
                    </span>
                    <small class="text-muted">{{ $puntosEstudio3 }} punto(s)</small>
                    
                </div>
            </div>

            <!-- Total -->
            <div class="col-md-2-4 col-6">
                <div class="p-3 border-2 border-success rounded-3 bg-light shadow-sm h-100">
                    <i class="bi bi-award-fill fs-3 {{ colorResultado($resultadoTotal) }} mb-2"></i>
                    <h6 class="fw-bold {{ colorResultado($resultadoTotal) }} mb-1">Total</h6>
                    <span class="fs-4 fw-bold {{ colorResultado($resultadoTotal) }} d-block mb-1">
                        {{ $resultadoTotal }}
                    </span>
                    <small class="text-muted">{{ $puntosTotales }} puntos totales</small>
                </div>
            </div>
        </div>

        <!-- Barra de progreso simple -->
        <div class="mt-4">
            <div class="d-flex justify-content-between mb-1">
                <small class="text-muted">Progreso total: {{ $puntosTotales }}/12 puntos</small>
                <small class="text-muted">
                    @if($puntosTotales >= 10)
                    <span class="text-danger">Severa</span>
                    @elseif($puntosTotales >= 7)
                    <span class="text-warning">Moderada</span>
                    @else
                    <span class="text-success">Leve</span>
                    @endif
                </small>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar 
                    @if($puntosTotales >= 10) bg-danger
                    @elseif($puntosTotales >= 7) bg-warning
                    @else bg-success @endif"
                    role="progressbar"
                    style="width: {{ ($puntosTotales / 12) * 100 }}%"
                    aria-valuenow="{{ $puntosTotales }}"
                    aria-valuemin="0"
                    aria-valuemax="12">
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.col-md-2-4 {
    flex: 0 0 auto;
    width: 20%;
}
@media (max-width: 768px) {
    .col-md-2-4 {
        width: 50%;
    }
}
@media (max-width: 576px) {
    .col-md-2-4 {
        width: 100%;
    }
}
.border-2 {
    border-width: 2px !important;
}
</style>
@endif