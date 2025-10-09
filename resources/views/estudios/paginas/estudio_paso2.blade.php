<div class="tab-pane fade" id="paso2" role="tabpanel">
    <fieldset class="border rounded p-3 mb-4">
        <legend class="float-none w-auto px-3 fw-bold text-dark">
            <i class="bi bi-people-fill me-2"></i>Evaluación de la calidad, espacios y servicios de vivienda
        </legend>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="total_familia" class="form-label">Total de Integrantes</label>
                <input type="number" class="form-control" id="total_familia"
                    min="1" value="1" placeholder="Ej: 5">
            </div>

            <div class="col-md-4 mb-3">
                <label for="menores_edad" class="form-label">Menores de Edad</label>
                <input type="number" class="form-control" id="menores_edad"
                    min="0" value="0" placeholder="Ej: 2">
            </div>

            <div class="col-md-4 mb-3">
                <label for="adultos_mayores" class="form-label">Adultos Mayores</label>
                <input type="number" class="form-control" id="adultos_mayores"
                    min="0" value="0" placeholder="Ej: 1">
            </div>

            <div class="col-md-6 mb-3">
                <label for="escolaridad_jefe" class="form-label">Escolaridad del Jefe de Familia</label>
                <select class="form-select" id="escolaridad_jefe">
                    <option value="">Seleccionar escolaridad...</option>
                    <option value="ninguna">Ninguna</option>
                    <option value="primaria">Primaria</option>
                    <option value="secundaria">Secundaria</option>
                    <option value="preparatoria">Preparatoria/Bachillerato</option>
                    <option value="universidad">Universidad</option>
                    <option value="posgrado">Posgrado</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label for="ocupacion_jefe" class="form-label">Ocupación del Jefe de Familia</label>
                <input type="text" class="form-control" id="ocupacion_jefe"
                    placeholder="Ej: Empleado, Comerciante, etc.">
            </div>
        </div>
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