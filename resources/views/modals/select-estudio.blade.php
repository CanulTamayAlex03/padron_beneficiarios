<div class="modal fade" id="selectEstudioModal" tabindex="-1" aria-labelledby="selectEstudioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-light">
                <h5 class="modal-title" id="selectEstudioModalLabel">Seleccionar Estudio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6>Beneficiario: <span id="beneficiario-nombre"></span></h6>
                    <p class="text-muted">Total de estudios: <strong><span id="total-estudios"></span></strong></p>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped" id="estudios-table">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Fecha de Creación</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
