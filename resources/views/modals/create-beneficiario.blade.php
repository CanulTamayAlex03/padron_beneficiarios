@can('crear beneficiarios')
<div class="modal fade" id="createBeneficiarioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg">
            {{-- Header --}}
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus-fill me-2"></i> Crear Nuevo Beneficiario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            {{-- Formulario --}}
            <form id="createBeneficiarioForm" action="{{ route('beneficiarios.store') }}" method="POST">
                @csrf
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

                    {{-- Contenedor para errores generales --}}
                    <div id="modal-general-error" class="alert alert-danger d-none mb-3"></div>

                    {{-- Datos Personales --}}
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2">Datos Personales</legend>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="create_nombres" class="form-label">Nombres *</label>
                                <input type="text" class="form-control uppercase-no-tildes" id="create_nombres" name="nombres"
                                    pattern="[A-ZÑ\s\.]+"
                                    oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s\.]/g, '')"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="create_primer_apellido" class="form-label">Primer Apellido *</label>
                                <input type="text" class="form-control uppercase-no-tildes" id="create_primer_apellido" name="primer_apellido"
                                    pattern="[A-ZÑ\s\.]+"
                                    oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s\.]/g, '')"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label for="create_segundo_apellido" class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control uppercase-no-tildes" id="create_segundo_apellido" name="segundo_apellido"
                                    pattern="[A-ZÑ\s\.]+"
                                    oninput="this.value = this.value.toUpperCase().replace(/[^A-ZÑ\s\.]/g, '')">
                            </div>
                            <input type="hidden" id="create_apellidos" name="apellidos" value="">
                        </div>
                    </fieldset>

                    {{-- Identificación --}}
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2">Identificación</legend>
                        <div class="row g-3">
                            {{-- Campo CURP --}}
                            <div class="col-md-6">
                                <label for="create_curp" class="form-label">CURP</label>
                                <input type="text" class="form-control" id="create_curp" name="curp"
                                    maxlength="18"
                                    oninput="this.value = this.value.toUpperCase(); validarCurp();"
                                    placeholder="Ej: ABCDEFGH0123456789">
                                <div class="form-text">18 caracteres exactos (opcional)</div>
                                <div id="curp-error" class="text-danger small d-none mt-1">
                                    <i class="bi bi-exclamation-circle"></i> La CURP debe tener exactamente 18 caracteres
                                </div>
                                <div id="curp-status" class="small mt-1 d-none">
                                    <i class="bi bi-check-circle"></i> <span></span>
                                </div>
                            </div>

                            {{-- Campo Confirmar CURP --}}
                            <div class="col-md-6">
                                <label for="create_curp_confirm" class="form-label">Confirmar CURP</label>
                                <input type="text" class="form-control" id="create_curp_confirm"
                                    name="curp_confirm" maxlength="18"
                                    oninput="this.value = this.value.toUpperCase(); validarConfirmacionCurp();"
                                    placeholder="Escriba la CURP nuevamente"
                                    onpaste="return false;" oncopy="return false;" oncut="return false;">
                                <div class="form-text">No se permite copiar/pegar en este campo</div>
                                <div id="curp-confirm-error" class="text-danger small d-none mt-1">
                                    <i class="bi bi-exclamation-circle"></i> <span></span>
                                </div>
                            </div>

                            {{-- ALERTA DE CURP REGISTRADA --}}
                            <div class="col-12">
                                <div id="curp-registrada-alert" class="alert alert-warning d-none mt-2">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        <div>
                                            <strong>La CURP pertenece a: </strong>
                                            <a href="#" id="beneficiario-existente-link" class="fw-bold text-decoration-none">
                                                <span id="beneficiario-existente-info"></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Datos de Nacimiento --}}
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2">Nacimiento</legend>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="create_fecha_nac" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="create_fecha_nac" name="fecha_nac"
                                    max="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="create_edad" class="form-label">Edad</label>
                                <input type="text" class="form-control" id="create_edad" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="create_estado_id" class="form-label">Estado de Nacimiento</label>
                                <select class="form-select" id="create_estado_id" name="estado_id">
                                    <option value="" disabled selected>Seleccione un estado</option>
                                    @foreach($estados as $estado)
                                    <option value="{{ $estado->id_estado }}" {{ $estado->id_estado == 31 ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="create_sexo" class="form-label">Sexo</label>
                                <select class="form-select" id="create_sexo" name="sexo">
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
                            <!--
                            <div class="col-md-6">
                                <input type="hidden" name="discapacidad" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="create_discapacidad" name="discapacidad" value="1">
                                    <label class="form-check-label" for="create_discapacidad">Discapacidad</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" name="indigena" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="create_indigena" name="indigena" value="1">
                                    <label class="form-check-label" for="create_indigena">Indígena</label>
                                </div>
                            </div>
                            -->
                            <div class="col-md-6">
                                <input type="hidden" name="maya_hablante" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="create_maya_hablante" name="maya_hablante" value="1">
                                    <label class="form-check-label" for="create_maya_hablante">Maya hablante</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" name="afromexicano" value="0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="create_afromexicano" name="afromexicano" value="1">
                                    <label class="form-check-label" for="create_afromexicano">Afromexicano</label>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Información adicional --}}
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2">Información adicional</legend>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="create_estado_civil" class="form-label">Estado civil</label>
                                <select class="form-select" id="create_estado_civil" name="estado_civil">
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
                                <label for="create_ocupacion_id" class="form-label">Ocupación</label>
                                <select class="form-select" id="create_ocupacion_id" name="ocupacion_id" required>
                                    <option value="" disabled selected>Seleccione una ocupación</option>
                                    @foreach($ocupaciones as $ocupacion)
                                    <option value="{{ $ocupacion->id }}">{{ $ocupacion->ocupacion }} {{ $ocupacion->puntos}} (pts) </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    {{-- DATOS DE DOMICILIO  --}}
                    <fieldset class="border rounded p-3 mb-3">
                        <legend class="float-none w-auto px-2">Domicilio</legend>

                        {{-- Dirección --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label for="create_calle" class="form-label">Calle</label>
                                <input type="text" class="form-control" id="create_calle" name="calle" placeholder="Nombre de la calle" required>
                            </div>
                            <div class="col-md-2">
                                <label for="create_numero" class="form-label">Número</label>
                                <input type="text" class="form-control" id="create_numero" name="numero" placeholder="123">
                            </div>
                            <div class="col-md-2">
                                <label for="create_letra" class="form-label">Letra</label>
                                <input type="text" class="form-control" id="create_letra" name="letra" placeholder="A" maxlength="5">
                            </div>
                        </div>

                        {{-- Cruzamientos --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="create_cruzamiento_1" class="form-label">Entre calle</label>
                                <input type="text" class="form-control" id="create_cruzamiento_1" name="cruzamiento_1" placeholder="Calle 1">
                            </div>
                            <div class="col-md-6">
                                <label for="create_cruzamiento_2" class="form-label">Y calle</label>
                                <input type="text" class="form-control" id="create_cruzamiento_2" name="cruzamiento_2" placeholder="Calle 2">
                            </div>
                        </div>

                        {{-- Tipo de asentamiento --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="create_tipo_asentamiento" class="form-label">Tipo de asentamiento</label>
                                <select class="form-select" id="create_tipo_asentamiento" name="tipo_asentamiento" required>
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
                                <label for="create_colonia_fracc" class="form-label">Nombre de colonia/fraccionamiento</label>
                                <input type="text" class="form-control" id="create_colonia_fracc" name="colonia_fracc" placeholder="Nombre de la colonia">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="create_estado_viv_id" class="form-label">Estado de residencia</label>
                                <select class="form-select" id="create_estado_viv_id" name="estado_viv_id" required>
                                    <option value="" disabled>Seleccione un estado</option>
                                    @foreach($estados as $estado)
                                    <option value="{{ $estado->id_estado }}"
                                        {{ $estado->id_estado == 31 ? 'selected' : '' }}>
                                        {{ $estado->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="create_municipio_id" class="form-label">Municipio</label>
                                <select class="form-select" id="create_municipio_id" name="municipio_id" required>
                                    <option value="" disabled>Seleccione un municipio</option>
                                    @foreach($municipios as $municipio)
                                    <option value="{{ $municipio->id }}"
                                        data-estado="{{ $municipio->estado_id }}"
                                        {{ $municipio->id == 2343 ? 'selected' : '' }}>
                                        {{ $municipio->descripcion }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Localidad y CP --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="create_localidad" class="form-label">Localidad</label>

                                <input type="text" class="form-control" id="create_localidad_input" name="localidad" 
                                    placeholder="Nombre de la localidad">

                                <select class="form-control d-none" id="create_localidad_select" name="localidad">
                                    <option value="">Seleccione una localidad</option>
                                </select>

                                <div class="form-text">
                                    <span id="create_localidad_hint">Escriba el nombre de la localidad</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="create_cp" class="form-label">Código Postal</label>
                                <input type="text" class="form-control" id="create_cp" name="cp" placeholder="97300" maxlength="5" required>
                            </div>
                        </div>
                        {{-- Contacto --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="create_telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="create_telefono" name="telefono" placeholder="9991234567" maxlength="10" required>
                            </div>
                        </div>

                        {{-- Referencias --}}
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="create_referencias_domicilio" class="form-label">Referencias del domicilio</label>
                                <textarea class="form-control" id="create_referencias_domicilio" name="referencias_domicilio" rows="3" placeholder="Ej: Casa color azul, portón negro, frente a la tienda..." required></textarea>
                            </div>
                        </div>
                    </fieldset>

                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" id="submit-btn">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<!-- Modal de Opciones para Estudio Socioeconómico -->
<div class="modal fade" id="estudioSocioeconomicoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-clipboard-check me-2"></i> Opciones de Estudio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body text-center">
                <!-- Información del beneficiario -->
                <div class="alert alert-info mb-4">
                    <i class="bi bi-person-check me-2"></i>
                    <strong id="beneficiario-info"></strong>
                </div>

                <h6 class="mb-4">¿Qué desea hacer con este beneficiario?</h6>

                <div class="row g-3 mb-4">
                    <!-- Opción 1: Crear Nuevo -->
                    <div class="col-6">
                        <div class="card h-100 border-primary option-card" data-option="nuevo" style="cursor: pointer;">
                            <div class="card-body text-center p-3">
                                <div class="mb-2">
                                    <i class="bi bi-plus-circle display-5 text-primary"></i>
                                </div>
                                <h6 class="card-title mb-1">Crear Nuevo</h6>
                                <p class="card-text small text-muted mb-0">
                                    Estudio desde cero
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Opción 2: Vincular Existente -->
                    <div class="col-6">
                        <div class="card h-100 border-success option-card" data-option="vincular" style="cursor: pointer;">
                            <div class="card-body text-center p-3">
                                <div class="mb-2">
                                    <i class="bi bi-link display-5 text-success"></i>
                                </div>
                                <h6 class="card-title mb-1">Vincular</h6>
                                <p class="card-text small text-muted mb-0">
                                    Usar estudio existente
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección para vincular estudio (se muestra al seleccionar "Vincular") -->
                <div id="seleccion-estudio-container" class="d-none">
                    <div class="card border-success">
                        <div class="card-body p-3">
                            <h6 class="mb-3">
                                <i class="bi bi-search me-2"></i> Seleccionar estudio
                            </h6>
                            <div class="mb-3">
                                <select class="form-select form-select-sm" id="select-estudio-existente">
                                    <option value="">Cargando estudios...</option>
                                </select>
                            </div>
                            <div id="info-estudio-seleccionado" class="p-2 bg-light rounded d-none">
                                <small class="text-muted" id="detalles-estudio"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                
                <button type="button" class="btn btn-success d-none" id="btn-confirmar-vinculacion" disabled>
                    <i class="bi bi-link-45deg"></i> Confirmar Vinculación
                </button>
                <button type="button" class="btn btn-primary d-none" id="btn-crear-estudio">
                    <i class="bi bi-plus-circle"></i> Crear Estudio
                </button>
                
            </div>
        </div>
    </div>
</div>

<style>
    .is-invalid {
        border-color: #dc3545 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6.4.4.4-.4'/%3e%3cpath d='M6 7v2'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .field-error {
        font-size: 0.875rem;
        margin-top: 0.25rem;
        color: #dc3545;
    }

    #curp-status .text-danger {
        color: #dc3545 !important;
        font-weight: 500;
    }

    #curp-status .text-success {
        color: #198754 !important;
        font-weight: 500;
    }

    #create_curp_confirm:disabled {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    #estudioSocioeconomicoModal .modal-content {
        border: none;
        border-radius: 10px;
    }

    #estudioSocioeconomicoModal .modal-header {
        border-radius: 10px 10px 0 0;
    }

    #btn-iniciar-estudio {
        min-width: 120px;
    }

    .uppercase-no-tildes {
        text-transform: uppercase;
    }

    #create_nombres,
    #create_primer_apellido,
    #create_segundo_apellido,
    #edit_nombres,
    #edit_primer_apellido,
    #edit_segundo_apellido {
        text-transform: uppercase;
    }

    #beneficiario-existente-link {
        color: #856404;
        border-bottom: 1px dotted #856404;
        transition: all 0.2s ease;
    }

    #beneficiario-existente-link:hover {
        color: #533f03;
        border-bottom: 1px solid #533f03;
        text-decoration: none;
    }
    .select2-container--default .select2-selection--single {
    height: 38px !important;
    }

    .option-card {
    transition: all 0.2s ease;
    }
    .option-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .option-card.selected {
        border-width: 2px;
        background-color: rgba(13, 110, 253, 0.05);
    }

    
</style>

{{-- Script para el modal de creación --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const createEstadoSelect = document.getElementById('create_estado_viv_id');
    const createMunicipioSelect = document.getElementById('create_municipio_id');
    const createLocalidadInput = document.getElementById('create_localidad_input');
    const createLocalidadSelect = document.getElementById('create_localidad_select');
    const createLocalidadHint = document.getElementById('create_localidad_hint');
    const todosMunicipios = @json($municipios);
    
    // Municipios que tienen localidades (del 2294 al 2399)
    const municipiosConLocalidades = Array.from({length: 106}, (_, i) => 2294 + i);

    $('#createBeneficiarioModal').on('shown.bs.modal', function() {
        
        // Inicializar Select2 para municipio
        $(createMunicipioSelect).select2({
            placeholder: "Seleccione o busque un municipio",
            allowClear: true,
            language: "es",
            width: '100%',
            dropdownParent: $('#createBeneficiarioModal')
        });

        // Inicializar Select2 para estado
        $(createEstadoSelect).select2({
            placeholder: "Seleccione un estado",
            allowClear: false,
            language: "es",
            width: '100%',
            dropdownParent: $('#createBeneficiarioModal')
        });

        // Inicializar Select2 para localidad
        $(createLocalidadSelect).select2({
            placeholder: "Busque o seleccione una localidad",
            allowClear: true,
            language: "es",
            width: '100%',
            dropdownParent: $('#createBeneficiarioModal')
        });

        const estadoDefault = '31';
        filtrarMunicipiosCreacion(estadoDefault);
        
        // Inicializar modo localidad con el municipio por defecto (Mérida)
        toggleModoLocalidadCreacion('2343');
    });

    function filtrarMunicipiosCreacion(estadoId, municipioSeleccionado = null) {

        if (!estadoId) {
            $(createMunicipioSelect).empty().append('<option value="">Seleccione un estado primero</option>');
            $(createMunicipioSelect).prop('disabled', true);
            $(createMunicipioSelect).trigger('change');
            return;
        }

        const municipiosFiltrados = todosMunicipios.filter(m => m.estado_id == estadoId);
        const seleccionActual = $(createMunicipioSelect).val();

        $(createMunicipioSelect).empty().append('<option value="">Seleccionar municipio...</option>');

        municipiosFiltrados.forEach(municipio => {
            const option = new Option(municipio.descripcion, municipio.id, false, false);
            $(createMunicipioSelect).append(option);
        });

        $(createMunicipioSelect).prop('disabled', false);

        if (estadoId === '31' && !municipioSeleccionado) {
            const meridaId = '2343';
            if (municipiosFiltrados.some(m => m.id == meridaId)) {
                $(createMunicipioSelect).val(meridaId).trigger('change');
            }
        } else if (municipioSeleccionado && municipiosFiltrados.some(m => m.id == municipioSeleccionado)) {
            $(createMunicipioSelect).val(municipioSeleccionado).trigger('change');
        } else {
            $(createMunicipioSelect).val('').trigger('change');
        }
    }

    function toggleModoLocalidadCreacion(municipioId) {
        const tieneLocalidades = municipiosConLocalidades.includes(parseInt(municipioId));
        
        
        if (tieneLocalidades) {
            createLocalidadInput.classList.add('d-none');
            createLocalidadInput.readOnly = true;
            createLocalidadSelect.classList.remove('d-none');
            createLocalidadSelect.disabled = false;
            createLocalidadHint.textContent = 'Seleccione una localidad de la lista';
            
            cargarLocalidadesCreacion(municipioId);
        } else {
            createLocalidadInput.classList.remove('d-none');
            createLocalidadInput.readOnly = false;
            createLocalidadSelect.classList.add('d-none');
            createLocalidadSelect.disabled = true;
            createLocalidadHint.textContent = 'Escriba el nombre de la localidad';
            
            // Transferir valor si es necesario
            if (createLocalidadSelect.value && !createLocalidadInput.value) {
                createLocalidadInput.value = $(createLocalidadSelect).select2('data')[0]?.text || '';
            }
            
            // Enfocar el input automáticamente
            setTimeout(() => {
                createLocalidadInput.focus();
            }, 100);
        }
    }

    function cargarLocalidadesCreacion(municipioId) {
        
        if (!municipioId) {
            $(createLocalidadSelect).empty().append('<option value="">Seleccione un municipio primero</option>');
            $(createLocalidadSelect).prop('disabled', true).trigger('change');
            return;
        }
        
        fetch(`/api/municipios/${municipioId}/localidades`)
            .then(response => response.json())
            .then(localidades => {
                
                $(createLocalidadSelect).empty().append('<option value="">Seleccione una localidad</option>');
                
                localidades.forEach(localidad => {
                    const option = new Option(
                        localidad.nom_loc, 
                        localidad.nom_loc, 
                        false, 
                        false
                    );
                    $(createLocalidadSelect).append(option);
                });
                
                $(createLocalidadSelect).prop('disabled', false).trigger('change');
            })
            .catch(error => {
                console.error('❌ Error cargando localidades (creación):', error);
                $(createLocalidadSelect).empty().append('<option value="">Error cargando localidades</option>');
            });
    }
    $(createEstadoSelect).on('change', function() {
        filtrarMunicipiosCreacion(this.value);
    });

    $(createMunicipioSelect).on('change', function() {
        toggleModoLocalidadCreacion(this.value);
    });

    createLocalidadInput.addEventListener('input', function() {
        createLocalidadSelect.value = this.value;
    });

    $(createLocalidadSelect).on('change', function() {
    });

    document.getElementById('createBeneficiarioForm').addEventListener('submit', function() {
        
        if (!createLocalidadSelect.classList.contains('d-none')) {
            createLocalidadInput.value = createLocalidadSelect.value;
        }
    });

    $('#createBeneficiarioModal').on('hidden.bs.modal', function() {
        $(createMunicipioSelect).select2('destroy');
        $(createEstadoSelect).select2('destroy');
        $(createLocalidadSelect).select2('destroy');
        
        createLocalidadInput.classList.remove('d-none');
        createLocalidadInput.readOnly = false;
        createLocalidadSelect.classList.add('d-none');
        createLocalidadSelect.disabled = true;
        createLocalidadInput.value = '';
        createLocalidadSelect.value = '';
        createLocalidadHint.textContent = 'Escriba el nombre de la localidad';
    });
});

</script>
@include('scripts.beneficiarios-vinculados-modal')
