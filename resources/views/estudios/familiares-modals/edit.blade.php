<div class="modal fade" id="editFamiliarModal{{ $familiar->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-pencil"></i> Editar Acompañante
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('familiares.update', $familiar->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nombre(s)</label>
                            <input type="text" name="nombres" class="form-control" value="{{ $familiar->nombres }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Apellido paterno</label>
                            <input type="text" name="primer_apellido" class="form-control" value="{{ $familiar->primer_apellido }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Apellido materno</label>
                            <input type="text" name="segundo_apellido" class="form-control" value="{{ $familiar->segundo_apellido }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>CURP</label>
                            <input type="text" name="curp" class="form-control" value="{{ $familiar->curp }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ $familiar->telefono }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Relación o parentesco</label>
                            <select name="relacion_parentezco" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="Padre/Madre" {{ $familiar->relacion_parentezco == 'Padre/Madre' ? 'selected' : '' }}>Padre/Madre</option>
                                <option value="Hijo/Hija" {{ $familiar->relacion_parentezco == 'Hijo/Hija' ? 'selected' : '' }}>Hijo/Hija</option>
                                <option value="Hermano/Hermana" {{ $familiar->relacion_parentezco == 'Hermano/Hermana' ? 'selected' : '' }}>Hermano/Hermana</option>
                                <option value="Otro" {{ $familiar->relacion_parentezco == 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
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
