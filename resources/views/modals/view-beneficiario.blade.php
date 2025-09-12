<div class="modal fade" id="viewBeneficiarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge-fill me-2"></i> Detalles del Beneficiario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="row g-3">
                    <!-- Columna izquierda -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-primary fw-bold mb-3">Datos Personales</h6>
                                <p><strong>ID:</strong> <span id="view-id"></span></p>
                                <p><strong>Nombres:</strong> <span id="view-nombres"></span></p>
                                <p><strong>Primer Apellido:</strong> <span id="view-primer_apellido"></span></p>
                                <p><strong>Segundo Apellido:</strong> <span id="view-segundo_apellido"></span></p>
                                <p><strong>CURP:</strong> <span id="view-curp"></span></p>
                                <p><strong>Fecha Nac.:</strong> <span id="view-fecha_nac"></span></p>
                                <p><strong>Edad:</strong> <span id="view-edad"></span></p>
                                <p><strong>Estado Nac.:</strong> <span id="view-estado_nac"></span></p>
                                <p><strong>Sexo:</strong> <span id="view-sexo"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-primary fw-bold mb-3">Otros Datos</h6>
                                <p><strong>Ocupación:</strong> <span id="view-ocupacion"></span></p>
                                <p><strong>Estado Civil:</strong> <span id="view-estado_civil"></span></p>
                                <p><strong>Discapacidad:</strong> <span id="view-discapacidad"></span></p>
                                <p><strong>Indígena:</strong> <span id="view-indigena"></span></p>
                                <p><strong>Maya hablante:</strong> <span id="view-maya_hablante"></span></p>
                                <p><strong>Afromexicano:</strong> <span id="view-afromexicano"></span></p>
                                <hr>
                                <p><strong>Fecha Registro:</strong> <span id="view-created"></span></p>
                                <p><strong>Última Actualización:</strong> <span id="view-updated"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
