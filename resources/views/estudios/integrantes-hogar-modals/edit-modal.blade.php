@if(isset($estudio) && $estudio->id)
@foreach($estudio->integrantesHogar as $integrante)
<div class="modal fade" id="editIntegranteModal{{ $integrante->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Integrante del Hogar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="edit-integrantes-form" data-id="{{ $integrante->id }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nombre(s) *</label>
                            <input type="text" name="nombres" class="form-control uppercase-edit"
                                value="{{ $integrante->nombres }}"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s]/g, '')">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control uppercase-edit"
                                value="{{ $integrante->apellidos }}"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s]/g, '')">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Edad *</label>
                            <input type="number" name="edad" class="form-control" min="0" max="120" value="{{ $integrante->edad }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Parentesco *</label>
                            <select name="parentesco_id" class="form-select" required>
                                <option value="">Seleccione...</option>
                                @foreach($parentescos as $parentesco)
                                <option value="{{ $parentesco->id }}"
                                    {{ $integrante->parentesco_id == $parentesco->id ? 'selected' : '' }}>
                                    {{ $parentesco->descripcion }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Ingreso mensual *</label>
                            <input type="number" name="ingreso_mensual" class="form-control" step="0.01" min="0" value="{{ $integrante->ingreso_mensual }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .uppercase-edit {
        text-transform: uppercase;
    }
</style>
@endforeach
@endif