@can('editar beneficiarios')
<div class="modal fade" id="editBeneficiarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg">
            
            {{-- Header --}}
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i> Editar Beneficiario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            {{-- Formulario --}}
            <form id="editBeneficiarioForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    
                    {{-- Datos Personales --}}
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2">Datos Personales</legend>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_nombres" class="form-label">Nombres *</label>
                                <input type="text" class="form-control" id="edit_nombres" name="nombres" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_primer_apellido" class="form-label">Primer Apellido *</label>
                                <input type="text" class="form-control" id="edit_primer_apellido" name="primer_apellido" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_segundo_apellido" class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" id="edit_segundo_apellido" name="segundo_apellido">
                            </div>
                            <input type="hidden" id="edit_apellidos" name="apellidos" value="">
                        </div>
                    </fieldset>

                    {{-- Identificación --}}
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2">Identificación</legend>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_curp" class="form-label">CURP</label>
                                <input type="text" class="form-control" id="edit_curp" name="curp" maxlength="18">
                                <div class="form-text">18 caracteres exactos (opcional)</div>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Datos de Nacimiento --}}
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2">Nacimiento</legend>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_fecha_nac" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="edit_fecha_nac" name="fecha_nac">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_estado_nac" class="form-label">Estado de Nacimiento</label>
                                <select class="form-select" id="edit_estado_nac" name="estado_nac">
                                    <option value="">Seleccione</option>
                                    <option value="Aguascalientes">Aguascalientes</option>
                                    <option value="Baja California">Baja California</option>
                                    <option value="Baja California Sur">Baja California Sur</option>
                                    <option value="Campeche">Campeche</option>
                                    <option value="Chiapas">Chiapas</option>
                                    <option value="Chihuahua">Chihuahua</option>
                                    <option value="Ciudad de México">Ciudad de México</option>
                                    <option value="Coahuila">Coahuila</option>
                                    <option value="Colima">Colima</option>
                                    <option value="Durango">Durango</option>
                                    <option value="Estado de México">Estado de México</option>
                                    <option value="Guanajuato">Guanajuato</option>
                                    <option value="Guerrero">Guerrero</option>
                                    <option value="Hidalgo">Hidalgo</option>
                                    <option value="Jalisco">Jalisco</option>
                                    <option value="Michoacán">Michoacán</option>
                                    <option value="Morelos">Morelos</option>
                                    <option value="Nayarit">Nayarit</option>
                                    <option value="Nuevo León">Nuevo León</option>
                                    <option value="Oaxaca">Oaxaca</option>
                                    <option value="Puebla">Puebla</option>
                                    <option value="Querétaro">Querétaro</option>
                                    <option value="Quintana Roo">Quintana Roo</option>
                                    <option value="San Luis Potosí">San Luis Potosí</option>
                                    <option value="Sinaloa">Sinaloa</option>
                                    <option value="Sonora">Sonora</option>
                                    <option value="Tabasco">Tabasco</option>
                                    <option value="Tamaulipas">Tamaulipas</option>
                                    <option value="Tlaxcala">Tlaxcala</option>
                                    <option value="Veracruz">Veracruz</option>
                                    <option value="Yucatán">Yucatán</option>
                                    <option value="Zacatecas">Zacatecas</option>
                                    <option value="Extranjero">Extranjero</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_sexo" class="form-label">Sexo</label>
                                <select class="form-select" id="edit_sexo" name="sexo">
                                    <option value="">Seleccione</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                    <option value="O">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_ocupacion" class="form-label">Ocupación</label>
                                <input type="text" class="form-control" id="edit_ocupacion" name="ocupacion">
                            </div>
                        </div>
                    </fieldset>

                    {{-- Características --}}
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2">Características</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" name="discapacidad" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_discapacidad" name="discapacidad" value="1">
                                    <label class="form-check-label" for="edit_discapacidad">Discapacidad</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" name="indigena" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_indigena" name="indigena" value="1">
                                    <label class="form-check-label" for="edit_indigena">Indígena</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" name="maya_hablante" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_maya_hablante" name="maya_hablante" value="1">
                                    <label class="form-check-label" for="edit_maya_hablante">Maya hablante</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" name="afromexicano" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_afromexicano" name="afromexicano" value="1">
                                    <label class="form-check-label" for="edit_afromexicano">Afromexicano</label>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Estado Civil --}}
                    <fieldset class="border rounded p-3">
                        <legend class="float-none w-auto px-2">Estado Civil</legend>
                        <div class="col-md-6">
                            <select class="form-select" id="edit_estado_civil" name="estado_civil">
                                <option value="">Seleccione</option>
                                <option value="Soltero/a">Soltero/a</option>
                                <option value="Casado/a">Casado/a</option>
                                <option value="Unión libre">Unión libre</option>
                                <option value="Separado/a">Separado/a</option>
                                <option value="Divorciado/a">Divorciado/a</option>
                                <option value="Viudo/a">Viudo/a</option>
                            </select>
                        </div>
                    </fieldset>

                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
