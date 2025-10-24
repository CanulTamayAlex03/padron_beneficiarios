<div class="modal fade" id="editFamiliarModal{{ $familiar->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-pencil"></i> Editar Auxiliar
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
                            <input type="text" name="nombres" class="form-control"
                                value="{{ $familiar->nombres }}" required
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s]/g, '')">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Apellido paterno</label>
                            <input type="text" name="primer_apellido" class="form-control"
                                value="{{ $familiar->primer_apellido }}" required
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s]/g, '')">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Apellido materno</label>
                            <input type="text" name="segundo_apellido" class="form-control"
                                value="{{ $familiar->segundo_apellido }}"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s]/g, '')">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>CURP</label>
                            <input type="text" name="curp" class="form-control text-uppercase"
                                value="{{ $familiar->curp }}"
                                maxlength="18"
                                pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9A-Z]{2}"
                                title="La CURP debe tener 18 caracteres (4 letras, 6 números, 6 letras, 2 alfanuméricos)"
                                oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ0-9]/g, '')"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Teléfono</label>
                            <input type="tel" name="telefono" class="form-control"
                                value="{{ $familiar->telefono }}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                maxlength="10"
                                pattern="[0-9]{10}"
                                title="El teléfono debe tener exactamente 10 dígitos numéricos"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Relación o parentesco</label>
                            <select name="parentesco_id" class="form-select" required>
                                <option value="" selected disabled>Seleccione un parentesco...</option>
                                @foreach($parentescos->where('id', '!=', 24) as $parentesco)
                                <option value="{{ $parentesco->id }}"
                                    {{ $familiar->parentesco_id == $parentesco->id ? 'selected' : '' }}>
                                    {{ $parentesco->descripcion }}
                                </option>
                                @endforeach
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