<div class="modal fade" id="createFamiliarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus"></i> Agregar Acompañante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('familiares.store', ['beneficiario' => $beneficiario->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="beneficiario_id" value="{{ $beneficiario->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nombre(s)</label>
                            <input type="text" name="nombres" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Primer apellido</label>
                            <input type="text" name="primer_apellido" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>CURP</label>
                            <input type="text" name="curp" class="form-control" 
                                   maxlength="18"
                                   pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9A-Z]{2}"
                                   title="La CURP debe tener 18 caracteres (4 letras, 6 números, 6 letras, 2 alfanuméricos)" required>
                            <div class="form-text">18 caracteres máximo</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Teléfono</label>
                            <input type="tel" name="telefono" class="form-control" 
                                   maxlength="12"
                                   pattern="[0-9]{10,12}"
                                   title="El teléfono debe tener entre 10 y 12 dígitos" required>
                            <div class="form-text">12 dígitos máximo</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Relación o parentesco</label>
                            <select name="relacion_parentezco" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="Padre/Madre">Padre/Madre</option>
                                <option value="Hijo/Hija">Hijo/Hija</option>
                                <option value="Hermano/Hermana">Hermano/Hermana</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
