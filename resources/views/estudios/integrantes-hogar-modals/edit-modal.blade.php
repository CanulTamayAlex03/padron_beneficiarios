
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
                            <input type="text" name="nombres" class="form-control" value="{{ $integrante->nombres }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Apellidos *</label>
                            <input type="text" name="apellidos" class="form-control" value="{{ $integrante->apellidos }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Edad *</label>
                            <input type="number" name="edad" class="form-control" min="0" max="120" value="{{ $integrante->edad }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Parentesco *</label>
                            <select name="parentesco" class="form-select">
                                <option value="">Seleccione...</option>
                                <option value="Jefe(a) de familia" {{ $integrante->parentesco == 'Jefe(a) de familia' ? 'selected' : '' }}>Jefe(a) de familia</option>
                                <option value="Cónyuge" {{ $integrante->parentesco == 'Cónyuge' ? 'selected' : '' }}>Cónyuge</option>
                                <option value="Hijo(a)" {{ $integrante->parentesco == 'Hijo(a)' ? 'selected' : '' }}>Hijo(a)</option>
                                <option value="Padre" {{ $integrante->parentesco == 'Padre' ? 'selected' : '' }}>Padre</option>
                                <option value="Madre" {{ $integrante->parentesco == 'Madre' ? 'selected' : '' }}>Madre</option>
                                <option value="Hermano(a)" {{ $integrante->parentesco == 'Hermano(a)' ? 'selected' : '' }}>Hermano(a)</option>
                                <option value="Otro familiar" {{ $integrante->parentesco == 'Otro familiar' ? 'selected' : '' }}>Otro familiar</option>
                                <option value="No familiar" {{ $integrante->parentesco == 'No familiar' ? 'selected' : '' }}>No familiar</option>
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
@endforeach
@endif