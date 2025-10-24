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
                            <input type="text" name="nombres" class="form-control uppercase-input"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s]/g, '')">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control uppercase-input"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s]/g, '')">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Edad *</label>
                            <input type="number" name="edad" class="form-control"
                                min="0" max="125"
                                maxlength="3"
                                oninput="this.value = this.value.slice(0, 3); this.value = this.value.replace(/[^0-9]/g, ''); if(this.value > 125) this.value = 125;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Parentesco *</label>
                            <select name="parentesco_id" class="form-select" required>
                                <option value="">Seleccione...</option>
                                @foreach($parentescos as $parentesco)
                                <option value="{{ $parentesco->id }}">{{ $parentesco->descripcion }}</option>
                                @endforeach
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
<style>
    .uppercase-input {
        text-transform: uppercase;
    }
</style>
@endif