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
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

                    {{-- Contenedor para errores generales --}}
                    <div id="modal-edit-error" class="alert alert-danger d-none mb-3"></div>

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
                                <input type="text" class="form-control" id="edit_curp" name="curp"
                                    maxlength="18" oninput="validarCurpEdit()"
                                    placeholder="Ej: ABCDEFGH0123456789">
                                <div class="form-text">18 caracteres exactos (opcional)</div>
                                <div id="edit-curp-error" class="text-danger small d-none mt-1">
                                    <i class="bi bi-exclamation-circle"></i> La CURP debe tener exactamente 18 caracteres
                                </div>
                                <div id="edit-curp-status" class="small mt-1 d-none">
                                    <i class="bi bi-check-circle"></i> <span></span>
                                </div>
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
                                <label for="edit_estado_id" class="form-label">Estado de Nacimiento</label>
                                <select class="form-select" id="edit_estado_id" name="estado_id">
                                    <option value="" disabled>Seleccione un estado</option>
                                    @foreach($estados as $estado)
                                    <option value="{{ $estado->id_estado }}">{{ $estado->nombre }}</option>
                                    @endforeach
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

                    {{-- Información adicional --}}
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2">Información adicional</legend>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_estado_civil" class="form-label">Estado civil</label>
                                <select class="form-select" id="edit_estado_civil" name="estado_civil">
                                    <option value="">Seleccione</option>
                                    <option value="Soltero/a">Soltero/a</option>
                                    <option value="Casado/a">Casado/a</option>
                                    <option value="Unión libre">Unión libre</option>
                                    <option value="Separado/a">Separado/a</option>
                                    <option value="Divorciado/a">Divorciado/a</option>
                                    <option value="Viudo/a">Viudo/a</option>
                                    <option value="No aplica">No aplica</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_ocupacion_id" class="form-label">Ocupación</label>
                                <select class="form-select" id="edit_ocupacion_id" name="ocupacion_id" required>
                                    <option value="" disabled>Seleccione una ocupación</option>
                                    @foreach($ocupaciones as $ocupacion)
                                    <option value="{{ $ocupacion->id }}">{{ $ocupacion->ocupacion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    {{-- DATOS DE DOMICILIO (NUEVOS CAMPOS) --}}
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2">Domicilio</legend>
                        
                        {{-- Dirección --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label for="edit_calle" class="form-label">Calle</label>
                                <input type="text" class="form-control" id="edit_calle" name="calle" placeholder="Nombre de la calle">
                            </div>
                            <div class="col-md-2">
                                <label for="edit_numero" class="form-label">Número</label>
                                <input type="text" class="form-control" id="edit_numero" name="numero" placeholder="123">
                            </div>
                            <div class="col-md-2">
                                <label for="edit_letra" class="form-label">Letra</label>
                                <input type="text" class="form-control" id="edit_letra" name="letra" placeholder="A" maxlength="5">
                            </div>
                        </div>

                        {{-- Cruzamientos --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_cruzamiento_1" class="form-label">Entre calle</label>
                                <input type="text" class="form-control" id="edit_cruzamiento_1" name="cruzamiento_1" placeholder="Calle 1">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_cruzamiento_2" class="form-label">Y calle</label>
                                <input type="text" class="form-control" id="edit_cruzamiento_2" name="cruzamiento_2" placeholder="Calle 2">
                            </div>
                        </div>

                        {{-- Tipo de asentamiento --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_tipo_asentamiento" class="form-label">Tipo de asentamiento</label>
                                <select class="form-select" id="edit_tipo_asentamiento" name="tipo_asentamiento">
                                    <option value="">Seleccione</option>
                                    <option value="Colonia">Colonia</option>
                                    <option value="Fraccionamiento">Fraccionamiento</option>
                                    <option value="Unidad habitacional">Unidad habitacional</option>
                                    <option value="Barrio">Barrio</option>
                                    <option value="Condominio">Condominio</option>
                                    <option value="Ejido">Ejido</option>
                                    <option value="Pueblo">Pueblo</option>
                                    <option value="Ranchería">Ranchería</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_colonia_fracc" class="form-label">Nombre de colonia/fraccionamiento</label>
                                <input type="text" class="form-control" id="edit_colonia_fracc" name="colonia_fracc" placeholder="Nombre de la colonia">
                            </div>
                        </div>

                        {{-- Estado y Municipio --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_estado_viv_id" class="form-label">Estado</label>
                                <select class="form-select" id="edit_estado_viv_id" name="estado_viv_id">
                                    <option value="" disabled>Seleccione un estado</option>
                                    @foreach($estados as $estado)
                                    <option value="{{ $estado->id_estado }}">{{ $estado->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_municipio_id" class="form-label">Municipio</label>
                                <select class="form-select" id="edit_municipio_id" name="municipio_id">
                                    <option value="" disabled>Seleccione un municipio</option>
                                    @foreach($municipios as $municipio)
                                    <option value="{{ $municipio->id }}" data-estado="{{ $municipio->estado_id }}">
                                        {{ $municipio->descripcion }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Localidad y CP --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_localidad" class="form-label">Localidad</label>
                                <input type="text" class="form-control" id="edit_localidad" name="localidad" placeholder="Nombre de la localidad">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_cp" class="form-label">Código Postal</label>
                                <input type="text" class="form-control" id="edit_cp" name="cp" placeholder="97300" maxlength="5">
                            </div>
                        </div>

                        {{-- Contacto --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="edit_telefono" name="telefono" placeholder="9991234567" maxlength="15">
                            </div>
                        </div>

                        {{-- Referencias --}}
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="edit_referencias_domicilio" class="form-label">Referencias del domicilio</label>
                                <textarea class="form-control" id="edit_referencias_domicilio" name="referencias_domicilio" rows="3" placeholder="Ej: Casa color azul, portón negro, frente a la tienda..."></textarea>
                            </div>
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