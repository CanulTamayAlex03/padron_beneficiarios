@if(isset($estudio) && $estudio->id)
<!-- Modal para crear integrante -->
<div class="modal fade" id="createIntegranteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus"></i> Agregar Integrante del Hogar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createIntegranteForm">
                @csrf
                <input type="hidden" name="estudio_socioeconomico_id" value="{{ $estudio->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nombre(s) *</label>
                            <input type="text" name="nombres" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Edad *</label>
                            <input type="number" name="edad" class="form-control" min="0" max="120">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Parentesco *</label>
                            <select name="parentesco" class="form-select">
                                <option value="">Seleccione...</option>
                                <option value="Jefe(a) de familia">Jefe(a) de familia</option>
                                <option value="Cónyuge">Cónyuge</option>
                                <option value="Hijo(a)">Hijo(a)</option>
                                <option value="Padre">Padre</option>
                                <option value="Madre">Madre</option>
                                <option value="Hermano(a)">Hermano(a)</option>
                                <option value="Otro familiar">Otro familiar</option>
                                <option value="No familiar">No familiar</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Ingreso mensual *</label>
                            <input type="number" name="ingreso_mensual" class="form-control" step="0.01" min="0">
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
@endif