<div class="modal fade" id="searchBeneficiarioModal" tabindex="-1" aria-labelledby="searchBeneficiarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchBeneficiarioModalLabel">
                    <i class="bi bi-search me-2"></i>Búsqueda Avanzada de Beneficiarios
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('beneficiarios') }}" method="GET" id="searchBeneficiarioForm">
                <div class="modal-body">
                    <!-- Búsqueda por Nombre -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="search_nombre_completo" class="form-label">
                                <i class="bi bi-person me-1"></i>Nombre Completo
                            </label>
                            <input type="text" class="form-control" id="search_nombre_completo" name="nombre_completo" 
                                   value="{{ request('nombre_completo') }}" 
                                   placeholder="Ej: Juan Carlos Pérez López">
                            <small class="text-muted">
                                Buscar en nombres, primer apellido y segundo apellido
                            </small>
                        </div>
                    </div>
                    
                    <!-- Búsqueda por CURP -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="search_curp" class="form-label">
                                <i class="bi bi-card-text me-1"></i>CURP
                            </label>
                            <input type="text" class="form-control" id="search_curp" name="curp" 
                                   value="{{ request('curp') }}" placeholder="Ej: XXXX000000XXXXXX00" 
                                   maxlength="18" style="text-transform: uppercase;">
                        </div>
                    </div>

                    <!-- Búsqueda por Programa y Tipo de Programa -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="search_programa_id" class="form-label">
                                <i class="bi bi-diagram-3 me-1"></i>Programa
                            </label>
                            <select class="form-select" id="search_programa_id" name="programa_id">
                                <option value="">Todos los programas</option>
                                @foreach($programas as $programa)
                                    <option value="{{ $programa->id }}" 
                                        {{ request('programa_id') == $programa->id ? 'selected' : '' }}
                                        data-tipos='@json($programa->tiposPrograma)'>
                                        {{ $programa->nombre_programa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="search_tipo_programa_id" class="form-label">
                                <i class="bi bi-funnel me-1"></i>Tipo de Programa
                            </label>
                            <select class="form-select" id="search_tipo_programa_id" name="tipo_programa_id">
                                <option value="">Todos los tipos</option>
                                @if(request('programa_id'))
                                    @php
                                        $programaSeleccionado = $programas->firstWhere('id', request('programa_id'));
                                    @endphp
                                    @if($programaSeleccionado)
                                        @foreach($programaSeleccionado->tiposPrograma as $tipo)
                                            <option value="{{ $tipo->id }}" 
                                                {{ request('tipo_programa_id') == $tipo->id ? 'selected' : '' }}>
                                                {{ $tipo->nombre_tipo_programa }}
                                            </option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                            <small class="text-muted">
                                Seleccione primero un programa
                            </small>
                        </div>
                    </div>

                    <!-- Filtro de Estudios -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                <i class="bi bi-clipboard-data me-1"></i>Filtrar por Estudios
                            </label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="con_estudios" id="con_estudios_todos" 
                                           value="" {{ !request('con_estudios') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="con_estudios_todos">
                                        Todos
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="con_estudios" id="con_estudios_si" 
                                           value="1" {{ request('con_estudios') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="con_estudios_si">
                                        Con estudios
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="con_estudios" id="con_estudios_no" 
                                           value="0" {{ request('con_estudios') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="con_estudios_no">
                                        Sin estudios
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <a href="{{ route('beneficiarios') }}" class="btn btn-outline-danger">
                        <i class="bi bi-arrow-clockwise"></i> Limpiar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    #searchBeneficiarioModal .modal-header {
        background: linear-gradient(135deg, #1b1b1b 0%, #2d2d2d 100%);
        color: white;
    }
    
    #searchBeneficiarioModal .form-label {
        font-weight: 500;
        color: #333;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchBeneficiarioForm');
    const searchModal = document.getElementById('searchBeneficiarioModal');
    const programaSelect = document.getElementById('search_programa_id');
    const tipoProgramaSelect = document.getElementById('search_tipo_programa_id');
    
    // Auto-cerrar modal después de enviar búsqueda
    searchForm.addEventListener('submit', function() {
        const modal = bootstrap.Modal.getInstance(searchModal);
        modal.hide();
    });
    
    // Convertir CURP a mayúsculas
    const curpInput = document.getElementById('search_curp');
    if (curpInput) {
        curpInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Dinámica entre Programa y Tipo de Programa
    if (programaSelect && tipoProgramaSelect) {
        programaSelect.addEventListener('change', function() {
            const programaId = this.value;
            tipoProgramaSelect.innerHTML = '<option value="">Cargando tipos...</option>';
            
            if (programaId) {
                const selectedOption = programaSelect.querySelector(`option[value="${programaId}"]`);
                const tiposData = selectedOption.getAttribute('data-tipos');
                
                if (tiposData) {
                    const tipos = JSON.parse(tiposData);
                    tipoProgramaSelect.innerHTML = '<option value="">Todos los tipos</option>';
                    
                    tipos.forEach(tipo => {
                        const option = document.createElement('option');
                        option.value = tipo.id;
                        option.textContent = tipo.nombre_tipo_programa;
                        tipoProgramaSelect.appendChild(option);
                    });
                    
                    tipoProgramaSelect.disabled = false;
                }
            } else {
                tipoProgramaSelect.innerHTML = '<option value="">Todos los tipos</option>';
                tipoProgramaSelect.disabled = false;
            }
        });
        
        // Inicializar si ya hay un programa seleccionado
        if (programaSelect.value) {
            programaSelect.dispatchEvent(new Event('change'));
            
            // Establecer el tipo de programa si ya estaba seleccionado
            setTimeout(() => {
                if ('{{ request('tipo_programa_id') }}') {
                    tipoProgramaSelect.value = '{{ request('tipo_programa_id') }}';
                }
            }, 100);
        }
    }
    
    searchModal.addEventListener('shown.bs.modal', function () {
        document.getElementById('search_nombre_completo').focus();
    });
});
</script>