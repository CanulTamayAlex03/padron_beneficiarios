<div class="tab-pane fade show active" id="paso1" role="tabpanel">
    <fieldset class="border rounded p-3 mb-4">
        <legend class="float-none w-auto px-3 fw-bold text-dark">
            <i class="bi bi-person-circle me-2"></i>Evaluación Económica y Familiar
        </legend>

    
        @include('estudios.paginas.integrantes-hogar')

        
    </fieldset>

    <div class="d-flex justify-content-between">
        <div></div>
        <button type="button" class="btn btn-primary" onclick="siguientePaso(2)">
            Siguiente <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</div>