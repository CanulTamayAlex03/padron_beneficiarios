<div class="tab-pane fade" id="paso2" role="tabpanel">
    <fieldset class="border rounded p-3 mb-4">
        <legend class="float-none w-auto px-3 fw-bold text-dark">
            <i class="bi bi-people-fill me-2"></i>Evaluación de la calidad, espacios y servicios de vivienda
        </legend>

        <div class="row">
            <!-- NUEVA PREGUNTA: Energía eléctrica -->
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">
                    <i class="bi bi-lightning-charge me-2"></i>¿El hogar cuenta con energía eléctrica?
                </label>
                <div class="mt-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="electricidad" id="electricidad_si"
                            value="1" {{ old('electricidad', $estudio->electricidad ?? '') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="electricidad_si">
                            Sí (0 pts)
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="electricidad" id="electricidad_no"
                            value="0" {{ old('electricidad', $estudio->electricidad ?? '') == '0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="electricidad_no">
                            No (1 pts)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Pregunta 1: Piso -->
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">
                    <i class="bi bi-house-door me-2"></i>Piso, ¿La mayor parte del piso de la vivienda es de?
                </label>
                <select class="form-select" name="tipo_piso" id="tipo_piso">
                    <option value="">Seleccione una opción</option>
                    <option value="Tierra"
                        @if(old('tipo_piso', $estudio->tipo_piso ?? '') == 'Tierra') selected @endif>
                        Tierra (3 pts)
                    </option>
                    <option value="Cemento"
                        @if(old('tipo_piso', $estudio->tipo_piso ?? '') == 'Cemento') selected @endif>
                        Cemento (2 pts)
                    </option>
                    <option value="Mosaico, madera, otro"
                        @if(old('tipo_piso', $estudio->tipo_piso ?? '') == 'Mosaico, madera, otro') selected @endif>
                        Mosaico, madera, otro (1 pts)
                    </option>
                </select>
            </div>

            <!-- Pregunta 2: Techo -->
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">
                    <i class="bi bi-house me-2"></i>Techo, ¿La mayor parte del techo de la vivienda es de?
                </label>
                <select class="form-select" name="tipo_techo" id="tipo_techo">
                    <option value="">Seleccione una opción</option>
                    <option value="Cantón, llantas, huano"
                        @if(old('tipo_techo', $estudio->tipo_techo ?? '') == 'Cantón, llantas, huano') selected @endif>
                        Cantón, llantas, huano (3 pts)
                    </option>
                    <option value="Asbesto, madera, lamina"
                        @if(old('tipo_techo', $estudio->tipo_techo ?? '') == 'Asbesto, madera, lamina') selected @endif>
                        Asbesto, madera, lamina (2 pts)
                    </option>
                    <option value="Cemento, piedra, block"
                        @if(old('tipo_techo', $estudio->tipo_techo ?? '') == 'Cemento, piedra, block') selected @endif>
                        Cemento, piedra, block (1 pts)
                    </option>
                </select>
            </div>

            <!-- Pregunta 3: Agua -->
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">
                    <i class="bi bi-droplet me-2"></i>Agua, ¿Qué agua utiliza para preparar alimentos?
                </label>
                <select class="form-select" name="agua_alimentos" id="agua_alimentos">
                    <option value="">Seleccione una opción</option>
                    <option value="Pozo"
                        @if(old('agua_alimentos', $estudio->agua_alimentos ?? '') == 'Pozo') selected @endif>
                        Pozo (3 pts)
                    </option>
                    <option value="De la llave"
                        @if(old('agua_alimentos', $estudio->agua_alimentos ?? '') == 'De la llave') selected @endif>
                        De la llave (2 pts)
                    </option>
                    <option value="Purificada"
                        @if(old('agua_alimentos', $estudio->agua_alimentos ?? '') == 'Purificada') selected @endif>
                        Purificada (1 pts)
                    </option>
                </select>
            </div>

            <!-- Pregunta 4: Medio de cocina -->
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">
                    <i class="bi bi-fire me-2"></i>¿Qué medio usan para cocinar los alimentos?
                </label>
                <select class="form-select" name="medio_cocina" id="medio_cocina">
                    <option value="">Seleccione una opción</option>
                    <option value="Carbón, leña"
                        @if(old('medio_cocina', $estudio->medio_cocina ?? '') == 'Carbón, leña') selected @endif>
                        Carbón, leña (3 pts)
                    </option>
                    <option value="Gas"
                        @if(old('medio_cocina', $estudio->medio_cocina ?? '') == 'Gas') selected @endif>
                        Gas (2 pts)
                    </option>
                    <option value="Parrilla eléctrica, otra"
                        @if(old('medio_cocina', $estudio->medio_cocina ?? '') == 'Parrilla eléctrica, otra') selected @endif>
                        Parrilla eléctrica, otra (1 pts)
                    </option>
                </select>
            </div>

            <!-- Pregunta 5: Tenencia -->
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">
                    <i class="bi bi-house-check me-2"></i>Tenencia, ¿La vivienda es?
                </label>
                <select class="form-select" name="vivienda" id="vivienda">
                    <option value="">Seleccione una opción</option>
                    <option value="Rentada"
                        @if(old('vivienda', $estudio->vivienda ?? '') == 'Rentada') selected @endif>
                        Rentada (3 pts)
                    </option>
                    <option value="Prestada"
                        @if(old('vivienda', $estudio->vivienda ?? '') == 'Prestada') selected @endif>
                        Prestada (2 pts)
                    </option>
                    <option value="Propia o familiar"
                        @if(old('vivienda', $estudio->vivienda ?? '') == 'Propia o familiar') selected @endif>
                        Propia o familiar (1 pts)
                    </option>
                </select>
            </div>

            <!-- Pregunta 6: Servicio sanitario -->
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">
                    <i class="bi bi-bucket me-2"></i>¿Cuenta con servicio sanitario?
                </label>
                <select class="form-select" name="servicio_sanitario" id="servicio_sanitario">
                    <option value="">Seleccione una opción</option>
                    <option value="Ningún servicio"
                        @if(old('servicio_sanitario', $estudio->servicio_sanitario ?? '') == 'Ningún servicio') selected @endif>
                        Ningún servicio (3 pts)
                    </option>
                    <option value="Letrina"
                        @if(old('servicio_sanitario', $estudio->servicio_sanitario ?? '') == 'Letrina') selected @endif>
                        Letrina (2 pts)
                    </option>
                    <option value="Inodoro"
                        @if(old('servicio_sanitario', $estudio->servicio_sanitario ?? '') == 'Inodoro') selected @endif>
                        Inodoro (1 pts)
                    </option>
                </select>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    Relación personas/cuarto
                </h6>

                @php
                // Obtener datos base
                $numPersonas = $totalPersonas ?? 0;
                $cuartosDormir = $estudio->cuartos_dormir ?? 0;
                $razon = ($cuartosDormir > 0) ? round($numPersonas / $cuartosDormir, 2) : 0;
                @endphp

                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Número de personas en el hogar:</label>
                        <input type="number" class="form-control" value="{{ $numPersonas }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Número de cuartos para dormir:</label>
                        <input type="number" class="form-control"
                            name="cuartos_dormir"
                            id="cuartos_dormir"
                            value="{{ old('cuartos_dormir', $estudio->cuartos_dormir ?? '') }}"
                            min="0">
                    </div>

                    <div class="col-md-4">
                    <label class="form-label fw-bold">Razón (personas/cuarto):</label>
                    <input type="text" class="form-control bg-light fw-bold"
                        id="razon_calculada"
                        value="{{ $razon }}"
                        readonly>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-bold">¿La razón es mayor a 2.5?</label>
                    <select class="form-select" name="razon_mayor" id="razon_mayor">
                        <option value="">Seleccione</option>
                        <option value="1" {{ old('razon_mayor', $estudio->razon_mayor ?? '') == 1 ? 'selected' : '' }}>Sí (2 pts)</option>
                        <option value="0" {{ old('razon_mayor', $estudio->razon_mayor ?? '') == 0 ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <small class="text-muted d-block mt-2">
                    * Si la razón es mayor a 2.5 y selecciona "Sí", se suman 2 puntos.
                </small>
            </div>
        </div>

        <!-- Sección de Resumen de Puntos (ACTUALIZADA) -->
        @if(isset($estudio) && $estudio->id)
        <div class="card mt-4">
            <div class="card-header text-light">
                <h6 class="mb-0">
                    <i class="bi bi-calculator me-2"></i>Resumen de Puntos - Vivienda
                </h6>
            </div>
            <div class="card-body">
                @php
                // Función para calcular puntos según la opción seleccionada
                function calcularPuntosVivienda($valor) {
                $puntos = [
                // Piso (máximo 3 puntos)
                'Tierra' => 3, 'Cemento' => 2, 'Mosaico, madera, otro' => 1,
                // Techo (máximo 3 puntos)
                'Cantón, llantas, huano' => 3, 'Asbesto, madera, lamina' => 2, 'Cemento, piedra, block' => 1,
                // Agua (máximo 3 puntos)
                'Pozo' => 3, 'De la llave' => 2, 'Purificada' => 1,
                // Cocina (máximo 3 puntos)
                'Carbón, leña' => 3, 'Gas' => 2, 'Parrilla eléctrica, otra' => 1,
                // Tenencia vivienda (máximo 3 puntos)
                'Rentada' => 3, 'Prestada' => 2, 'Propia o familiar' => 1,
                // Servicio sanitario (máximo 3 puntos)
                'Ningún servicio' => 3, 'Letrina' => 2, 'Inodoro' => 1
                ];
                return $puntos[$valor] ?? 0;
                }

                // Calcular puntos para cada campo
                $puntosElectricidad = $estudio->electricidad == 0 ? 1 : 0;
                $puntosPiso = calcularPuntosVivienda($estudio->tipo_piso);
                $puntosTecho = calcularPuntosVivienda($estudio->tipo_techo);
                $puntosAgua = calcularPuntosVivienda($estudio->agua_alimentos);
                $puntosCocina = calcularPuntosVivienda($estudio->medio_cocina);
                $puntosVivienda = calcularPuntosVivienda($estudio->vivienda);
                $puntosSanitario = calcularPuntosVivienda($estudio->servicio_sanitario);
                $puntosRazon = ($estudio->razon_mayor == 1) ? 2 : 0;

                $totalVivienda = $puntosElectricidad + $puntosPiso + $puntosTecho + $puntosAgua + $puntosCocina + $puntosVivienda + $puntosSanitario + $puntosRazon;
                $maximoPuntos = 21;
                @endphp

                <div class="row">
                    <div class="col-md-3 mb-2">
                        <small><strong>Energía eléctrica:</strong> {{ $puntosElectricidad }} pts</small>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small><strong>Piso:</strong> {{ $puntosPiso }} pts</small>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small><strong>Techo:</strong> {{ $puntosTecho }} pts</small>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small><strong>Agua:</strong> {{ $puntosAgua }} pts</small>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small><strong>Cocina:</strong> {{ $puntosCocina }} pts</small>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small><strong>Tenencia:</strong> {{ $puntosVivienda }} pts</small>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small><strong>Sanitario:</strong> {{ $puntosSanitario }} pts</small>
                    </div>
                    <div class="col-md-3 mb-2">
                        <small><strong>Razón mayor:</strong> {{ $puntosRazon }} pts</small>
                    </div>
                </div>

                <div class="progress mt-2" style="height: 25px;">
                    <div class="progress-bar 
                @if($totalVivienda >= 16) bg-danger
                @elseif($totalVivienda >= 11) bg-warning
                @elseif($totalVivienda >= 6) bg-info
                @else bg-success
                @endif"
                        role="progressbar"
                        style="width: {{ ($totalVivienda / $maximoPuntos) * 100 }}%"
                        aria-valuenow="{{ $totalVivienda }}"
                        aria-valuemin="0"
                        aria-valuemax="{{ $maximoPuntos }}">
                        <strong>{{ $totalVivienda }} / {{ $maximoPuntos }} puntos</strong>
                    </div>
                </div>

                <div class="mt-2 text-center">
                    <small class="text-muted">
                        @if($totalVivienda >= 16)
                        <span class="badge bg-danger">Nivel Severo</span>
                        @elseif($totalVivienda >= 11)
                        <span class="badge bg-warning text-dark">Nivel Moderado</span>
                        @elseif($totalVivienda >= 6)
                        <span class="badge bg-success">Nivel Leve</span>
                        @else
                        <span class="badge bg-success">Condiciones óptimas</span>
                        @endif
                    </small>
                </div>
            </div>
        </div>
        @endif
    </fieldset>

    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" onclick="anteriorPaso(1)">
            <i class="bi bi-arrow-left"></i> Anterior
        </button>
        <button type="button" class="btn btn-primary" onclick="siguientePaso(3)">
            Siguiente <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputCuartos = document.getElementById('cuartos_dormir');
    const razonField = document.getElementById('razon_calculada');
    const razonSelect = document.getElementById('razon_mayor');

    // Valor PHP insertado correctamente
    const numPersonas = {{ $numPersonas ?? 0 }};

    if (inputCuartos) {
        inputCuartos.addEventListener('input', function() {
            const cuartos = parseFloat(this.value) || 0;
            let razon = 0;

            if (cuartos > 0) {
                razon = (numPersonas / cuartos).toFixed(2);
            }

            razonField.value = razon;

            // Selección automática según la razón
            if (razon > 2.5) {
                razonSelect.value = "1"; // Sí
            } else {
                razonSelect.value = "0"; // No
            }
        });
    }
});
</script>