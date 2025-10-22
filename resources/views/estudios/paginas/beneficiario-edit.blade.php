<div class="card mb-4">
    <div class="card-header bg-dark d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-light">
            <i class="bi bi-person-fill me-2 text-light"></i> Edición Completa de Beneficiario
        </h5>
    </div>

    <div class="card-body">
        <form action="{{ route('beneficiarios.update', $beneficiario->id) }}" method="POST" id="editBeneficiarioForm">
            @csrf
            @method('PUT')

            @if(isset($estudio) && $estudio->exists)
            <input type="hidden" name="estudio_actual" value="{{ $estudio->id }}">
            @endif

            {{-- Contenedor para errores generales --}}
            <div id="modal-edit-error" class="alert alert-danger d-none mb-3"></div>



            {{-- Datos Personales y CURP (Optimizado a 4 columnas) --}}
            <fieldset class="border rounded p-3 mb-3">
                <legend class="float-none w-auto px-2"><strong>Datos Personales e Identificación</strong></legend>
                <div class="row g-3">
                    <div class="col-md-3"> {{-- 4 columnas --}}
                        <label for="edit_nombres" class="form-label">Nombres *</label>
                        <input type="text" class="form-control uppercase-no-tildes" id="edit_nombres" name="nombres"
                        value="{{ old('nombres', $beneficiario->nombres) }}"
                        pattern="[A-ZÑ\s\.]+" 
                        oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s\.]/g, '')" 
                        required>
                    </div>
                    <div class="col-md-3"> {{-- 4 columnas --}}
                        <label for="edit_primer_apellido" class="form-label">Primer Apellido *</label>
                        <input type="text" class="form-control uppercase-no-tildes" id="edit_primer_apellido" name="primer_apellido"
                        value="{{ old('primer_apellido', $beneficiario->primer_apellido) }}"
                        pattern="[A-ZÑ\s\.]+" 
                        oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s\.]/g, '')" 
                        required>
                    </div>
                    <div class="col-md-3"> {{-- 4 columnas --}}
                        <label for="edit_segundo_apellido" class="form-label">Segundo Apellido</label>
                        <input type="text" class="form-control uppercase-no-tildes" id="edit_segundo_apellido" name="segundo_apellido"
                        value="{{ old('segundo_apellido', $beneficiario->segundo_apellido) }}"
                        pattern="[A-ZÑ\s\.]+" 
                        oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s\.]/g, '')">
                    </div>
                    <div class="col-md-3"> 
                        <label for="edit_curp" class="form-label">CURP</label>
                        <input type="text" class="form-control" id="edit_curp" name="curp"
                            maxlength="18" oninput="validarCurpEdit()"
                            placeholder="Ej: ABCDEFGH0123456789"
                            value="{{ old('curp', $beneficiario->curp) }}">
                        <div class="form-text">18 caracteres (opcional)</div>
                        <div id="edit-curp-error" class="text-danger small d-none mt-1">
                            <i class="bi bi-exclamation-circle"></i> La CURP debe tener exactamente 18 caracteres
                        </div>
                        <div id="edit-curp-status" class="small mt-1 d-none">
                            <i class="bi bi-check-circle"></i> <span></span>
                        </div>
                    </div>
                    <input type="hidden" id="edit_apellidos" name="apellidos" value="">
                </div>
            </fieldset>

            {{-- Nacimiento e Información Adicional (Optimizado a 3 columnas) --}}
            <fieldset class="border rounded p-3 mb-3">
                <legend class="float-none w-auto px-2"><strong>Nacimiento y Otros Datos</strong></legend>
                <div class="row g-3">
                    <div class="col-md-3"> {{-- Fecha de Nacimiento - 3 columnas --}}
                        <label for="edit_fecha_nac" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="edit_fecha_nac" name="fecha_nac"
                            value="{{ old('fecha_nac', $beneficiario->fecha_nac->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3"> {{-- Estado de Nacimiento - 3 columnas --}}
                        <label for="edit_estado_id" class="form-label">Estado de Nacimiento</label>
                        <select class="form-select" id="edit_estado_id" name="estado_id">
                            <option value="" disabled>Seleccione un estado</option>
                            @foreach($estados as $estado)
                            <option value="{{ $estado->id_estado }}"
                                {{ old('estado_id', $beneficiario->estado_id) == $estado->id_estado ? 'selected' : '' }}>
                                {{ $estado->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2"> {{-- Sexo - 3 columnas --}}
                        <label for="edit_sexo" class="form-label">Sexo</label>
                        <select class="form-select" id="edit_sexo" name="sexo">
                            <option value="">Seleccione</option>
                            <option value="M" {{ old('sexo', $beneficiario->sexo) == 'M' ? 'selected' : '' }}>M</option>
                            <option value="F" {{ old('sexo', $beneficiario->sexo) == 'F' ? 'selected' : '' }}>F</option>
                            <option value="O" {{ old('sexo', $beneficiario->sexo) == 'O' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-2"> {{-- Estado Civil - 3 columnas --}}
                        <label for="edit_estado_civil" class="form-label">Estado civil</label>
                        <select class="form-select" id="edit_estado_civil" name="estado_civil">
                            <option value="">Seleccione</option>
                            <option value="Soltero/a" {{ old('estado_civil', $beneficiario->estado_civil) == 'Soltero/a' ? 'selected' : '' }}>Soltero/a</option>
                            <option value="Casado/a" {{ old('estado_civil', $beneficiario->estado_civil) == 'Casado/a' ? 'selected' : '' }}>Casado/a</option>
                            <option value="Unión libre" {{ old('estado_civil', $beneficiario->estado_civil) == 'Unión libre' ? 'selected' : '' }}>Unión libre</option>
                            <option value="Separado/a" {{ old('estado_civil', $beneficiario->estado_civil) == 'Separado/a' ? 'selected' : '' }}>Separado/a</option>
                            <option value="Divorciado/a" {{ old('estado_civil', $beneficiario->estado_civil) == 'Divorciado/a' ? 'selected' : '' }}>Divorciado/a</option>
                            <option value="Viudo/a" {{ old('estado_civil', $beneficiario->estado_civil) == 'Viudo/a' ? 'selected' : '' }}>Viudo/a</option>
                            <option value="No aplica" {{ old('estado_civil', $beneficiario->estado_civil) == 'No aplica' ? 'selected' : '' }}>No aplica</option>
                        </select>
                    </div>
                    <div class="col-md-2"> {{-- Ocupación - 3 columnas --}}
                        <label for="edit_ocupacion_id" class="form-label">Ocupación</label>
                        <select class="form-select" id="edit_ocupacion_id" name="ocupacion_id" required>
                            <option value="" disabled>Seleccione</option>
                            @foreach($ocupaciones as $ocupacion)
                            <option value="{{ $ocupacion->id }}"
                                {{ old('ocupacion_id', $beneficiario->ocupacion_id) == $ocupacion->id ? 'selected' : '' }}>
                                {{ $ocupacion->ocupacion }} ({{ $ocupacion->puntos }} pts)
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </fieldset>

            {{-- Características (Optimizado a 4 columnas) --}}
            <fieldset class="border rounded p-3 mb-3">
                <legend class="float-none w-auto px-2"><strong>Características Especiales</strong></legend>
                <div class="row g-3">
                    <div class="col-md-3 col-6"> {{-- 4 columnas --}}
                        <input type="hidden" name="discapacidad" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_discapacidad" name="discapacidad" value="1"
                                {{ old('discapacidad', $beneficiario->discapacidad) ? 'checked' : '' }}>
                            <label class="form-check-label" for="edit_discapacidad">Discapacidad</label>
                        </div>
                    </div>
                    <div class="col-md-3 col-6"> {{-- 4 columnas --}}
                        <input type="hidden" name="indigena" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_indigena" name="indigena" value="1"
                                {{ old('indigena', $beneficiario->indigena) ? 'checked' : '' }}>
                            <label class="form-check-label" for="edit_indigena">Indígena</label>
                        </div>
                    </div>
                    <div class="col-md-3 col-6"> {{-- 4 columnas --}}
                        <input type="hidden" name="maya_hablante" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_maya_hablante" name="maya_hablante" value="1"
                                {{ old('maya_hablante', $beneficiario->maya_hablante) ? 'checked' : '' }}>
                            <label class="form-check-label" for="edit_maya_hablante">Maya hablante</label>
                        </div>
                    </div>
                    <div class="col-md-3 col-6"> {{-- 4 columnas --}}
                        <input type="hidden" name="afromexicano" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_afromexicano" name="afromexicano" value="1"
                                {{ old('afromexicano', $beneficiario->afromexicano) ? 'checked' : '' }}>
                            <label class="form-check-label" for="edit_afromexicano">Afromexicano</label>
                        </div>
                    </div>
                </div>
            </fieldset>


            <fieldset class="border rounded p-3 mb-3">
                <legend class="float-none w-auto px-2"><strong>Domicilio y Contacto</strong></legend>

                {{-- Calle y Número (Optimizado a 4 columnas) --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6 col-lg-5"> {{-- Calle --}}
                        <label for="edit_calle" class="form-label">Calle</label>
                        <input type="text" class="form-control" id="edit_calle" name="calle"
                            placeholder="Nombre de la calle" value="{{ old('calle', $beneficiario->calle) }}">
                    </div>
                    <div class="col-md-3 col-lg-2"> {{-- Número --}}
                        <label for="edit_numero" class="form-label">Número</label>
                        <input type="text" class="form-control" id="edit_numero" name="numero"
                            placeholder="123" value="{{ old('numero', $beneficiario->numero) }}">
                    </div>
                    <div class="col-md-3 col-lg-2"> {{-- Letra --}}
                        <label for="edit_letra" class="form-label">Letra</label>
                        <input type="text" class="form-control" id="edit_letra" name="letra"
                            placeholder="A" maxlength="5" value="{{ old('letra', $beneficiario->letra) }}">
                    </div>
                    <div class="col-md-6 col-lg-3"> {{-- Código Postal --}}
                        <label for="edit_cp" class="form-label">Código Postal</label>
                        <input type="text" class="form-control" id="edit_cp" name="cp"
                            placeholder="97300" maxlength="5" value="{{ old('cp', $beneficiario->cp) }}">
                    </div>
                </div>

                {{-- Cruzamientos (2 columnas) --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="edit_cruzamiento_1" class="form-label">Entre calle</label>
                        <input type="text" class="form-control" id="edit_cruzamiento_1" name="cruzamiento_1"
                            placeholder="Calle 1" value="{{ old('cruzamiento_1', $beneficiario->cruzamiento_1) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="edit_cruzamiento_2" class="form-label">Y calle</label>
                        <input type="text" class="form-control" id="edit_cruzamiento_2" name="cruzamiento_2"
                            placeholder="Calle 2" value="{{ old('cruzamiento_2', $beneficiario->cruzamiento_2) }}">
                    </div>
                </div>

                {{-- Asentamiento y Colonia (Optimizado a 3 columnas) --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="edit_tipo_asentamiento" class="form-label">Tipo de asentamiento</label>
                        <select class="form-select" id="edit_tipo_asentamiento" name="tipo_asentamiento">
                            <option value="">Seleccione</option>
                            <option value="Colonia" {{ old('tipo_asentamiento', $beneficiario->tipo_asentamiento) == 'Colonia' ? 'selected' : '' }}>Colonia</option>
                            <option value="Fraccionamiento" {{ old('tipo_asentamiento', $beneficiario->tipo_asentamiento) == 'Fraccionamiento' ? 'selected' : '' }}>Fraccionamiento</option>
                            <option value="Unidad habitacional" {{ old('tipo_asentamiento', $beneficiario->tipo_asentamiento) == 'Unidad habitacional' ? 'selected' : '' }}>Unidad hab.</option>
                            <option value="Barrio" {{ old('tipo_asentamiento', $beneficiario->tipo_asentamiento) == 'Barrio' ? 'selected' : '' }}>Barrio</option>
                            <option value="Condominio" {{ old('tipo_asentamiento', $beneficiario->tipo_asentamiento) == 'Condominio' ? 'selected' : '' }}>Condominio</option>
                            <option value="Ejido" {{ old('tipo_asentamiento', $beneficiario->tipo_asentamiento) == 'Ejido' ? 'selected' : '' }}>Ejido</option>
                            <option value="Pueblo" {{ old('tipo_asentamiento', $beneficiario->tipo_asentamiento) == 'Pueblo' ? 'selected' : '' }}>Pueblo</option>
                            <option value="Ranchería" {{ old('tipo_asentamiento', $beneficiario->tipo_asentamiento) == 'Ranchería' ? 'selected' : '' }}>Ranchería</option>
                            <option value="Otro" {{ old('tipo_asentamiento', $beneficiario->tipo_asentamiento) == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label for="edit_colonia_fracc" class="form-label">Nombre de colonia/fraccionamiento</label>
                        <input type="text" class="form-control" id="edit_colonia_fracc" name="colonia_fracc"
                            placeholder="Nombre de la colonia" value="{{ old('colonia_fracc', $beneficiario->colonia_fracc) }}">
                    </div>
                </div>

                {{-- Ubicación Geográfica (Optimizado a 3 columnas) --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4"> {{-- Estado --}}
                        <label for="edit_estado_viv_id" class="form-label">Estado</label>
                        <select class="form-select" id="edit_estado_viv_id" name="estado_viv_id">
                            <option value="" disabled>Seleccione un estado</option>
                            @foreach($estados as $estado)
                            <option value="{{ $estado->id_estado }}"
                                {{ old('estado_viv_id', $beneficiario->estado_viv_id) == $estado->id_estado ? 'selected' : '' }}>
                                {{ $estado->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4"> {{-- Municipio --}}
                        <label for="edit_municipio_id" class="form-label">Municipio</label>
                        <select class="form-select" id="edit_municipio_id" name="municipio_id">
                            <option value="" disabled>Seleccione un municipio</option>
                            @foreach($municipios as $municipio)
                            <option value="{{ $municipio->id }}" data-estado="{{ $municipio->estado_id }}"
                                {{ old('municipio_id', $beneficiario->municipio_id) == $municipio->id ? 'selected' : '' }}>
                                {{ $municipio->descripcion }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4"> {{-- Localidad --}}
                        <label for="edit_localidad" class="form-label">Localidad</label>
                        <input type="text" class="form-control" id="edit_localidad" name="localidad"
                            placeholder="Nombre de la localidad" value="{{ old('localidad', $beneficiario->localidad) }}">
                    </div>
                </div>

                {{-- Teléfono y Referencias (2 columnas) --}}
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="edit_telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="edit_telefono" name="telefono"
                            placeholder="9991234567" maxlength="15" value="{{ old('telefono', $beneficiario->telefono) }}">
                    </div>
                    <div class="col-md-8">
                        <label for="edit_referencias_domicilio" class="form-label">Referencias del domicilio</label>
                        <textarea class="form-control" id="edit_referencias_domicilio" name="referencias_domicilio"
                            rows="3" placeholder="Ej: Casa color azul, portón negro, frente a la tienda...">{{ old('referencias_domicilio', $beneficiario->referencias_domicilio) }}</textarea>
                    </div>
                </div>
            </fieldset>

            {{-- Botón de envío (único) --}}
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-warning" id="submitForm">
                    <i class="bi bi-save"></i> Actualizar Beneficiario
                </button>
            </div>
        </form>
    </div>
</div>
<style>
    #edit_curp{
    text-transform: uppercase;
    }
    .uppercase-no-tildes {
    text-transform: uppercase;
}

#edit_nombres, #edit_primer_apellido, #edit_segundo_apellido {
    text-transform: uppercase;
}
</style>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById("editBeneficiarioForm");
        const errorDiv = document.getElementById('modal-edit-error');

        window.validarCurpEdit = function() {
            const curpInput = document.getElementById('edit_curp');
            const curpError = document.getElementById('edit-curp-error');

            if (curpInput.value.trim() !== "" && curpInput.value.length !== 18) {
                curpError.classList.remove('d-none');
                curpInput.classList.add('is-invalid');
            } else {
                curpError.classList.add('d-none');
                curpInput.classList.remove('is-invalid');
            }
        }

        form.addEventListener('submit', (event) => {
            let valid = true;
            errorDiv.classList.add('d-none');
            errorDiv.textContent = '';

            validarCurpEdit();

            const requiredInputs = form.querySelectorAll('input[required], select[required]');
            requiredInputs.forEach(input => {
                if (input.value.trim() === "") {
                    input.classList.add('is-invalid');
                    valid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (form.querySelector('.is-invalid') || !valid) {
                event.preventDefault();
                errorDiv.textContent = 'Por favor, corrija los campos marcados en rojo antes de actualizar.';
                errorDiv.classList.remove('d-none');
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const estadoSelect = document.getElementById('edit_estado_viv_id');
        const municipioSelect = document.getElementById('edit_municipio_id');

        // Cargar todos los municipios al inicio (pasados desde el controlador)
        const todosMunicipios = @json($municipios);

        function filtrarMunicipios(estadoId, municipioSeleccionado = null) {
            console.log('Filtrando municipios para estado:', estadoId);

            if (!estadoId) {
                municipioSelect.innerHTML = '<option value="">Seleccione un estado primero</option>';
                municipioSelect.disabled = true;
                return;
            }

            const municipiosFiltrados = todosMunicipios.filter(m => m.estado_id == estadoId);

            municipioSelect.innerHTML = '<option value="">Seleccionar municipio...</option>';

            municipiosFiltrados.forEach(municipio => {
                const option = document.createElement('option');
                option.value = municipio.id;
                option.textContent = municipio.descripcion;

                if (municipioSeleccionado && parseInt(municipioSeleccionado) === parseInt(municipio.id)) {
                    option.selected = true;
                }

                municipioSelect.appendChild(option);
            });

            municipioSelect.disabled = false;
        }

        const estadoActual = '{{ $beneficiario->estado_viv_id }}';
        const municipioActual = '{{ $beneficiario->municipio_id }}';

        if (estadoActual) {
            filtrarMunicipios(estadoActual, municipioActual);
        }

        estadoSelect.addEventListener('change', function() {
            filtrarMunicipios(this.value);
        });
    });
</script>