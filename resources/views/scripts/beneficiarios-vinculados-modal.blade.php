<script>
document.addEventListener('DOMContentLoaded', function() {
    let beneficiarioIdActual = null;
    let estudiosDisponibles = [];

    function mostrarOpcionesEstudio(beneficiarioId, beneficiarioInfo) {
        beneficiarioIdActual = beneficiarioId;
        
        document.getElementById('beneficiario-info').textContent = beneficiarioInfo;
        
        resetearModal();
        
        cargarEstudiosDisponibles();
        
        const modal = new bootstrap.Modal(document.getElementById('estudioSocioeconomicoModal'));
        modal.show();
    }

    function resetearModal() {
        document.querySelectorAll('.option-card').forEach(card => {
            card.classList.remove('selected');
        });

        document.getElementById('seleccion-estudio-container').classList.add('d-none');
        document.getElementById('btn-crear-estudio').classList.add('d-none');
        document.getElementById('btn-confirmar-vinculacion').classList.add('d-none');

        if ($('#select-estudio-existente').hasClass('select2-hidden-accessible')) {
            $('#select-estudio-existente').select2('destroy');
        }
        document.getElementById('select-estudio-existente').innerHTML = '<option value="">Cargando estudios disponibles...</option>';
        document.getElementById('btn-confirmar-vinculacion').disabled = true;
        document.getElementById('info-estudio-seleccionado').classList.add('d-none');
    }

    function cargarEstudiosDisponibles() {
        fetch('/api/estudios/disponibles-para-vincular')
            .then(response => response.json())
            .then(data => {
                estudiosDisponibles = data.estudios || [];
                actualizarSelectEstudios();
            })
            .catch(error => {
                console.error('Error cargando estudios:', error);
                document.getElementById('select-estudio-existente').innerHTML = 
                    '<option value="">Error cargando estudios</option>';
            });
    }

    // Actualizar el select con estudios disponibles
function actualizarSelectEstudios() {
    const select = document.getElementById('select-estudio-existente');
    select.innerHTML = '<option value="">Seleccione un estudio...</option>';
    
    if (estudiosDisponibles.length === 0) {
        select.innerHTML = '<option value="">No hay estudios disponibles</option>';
        return;
    }

    estudiosDisponibles.forEach(estudio => {
        const option = document.createElement('option');
        option.value = estudio.id;
        option.textContent = `Folio: ${estudio.folio} - ${estudio.beneficiario_nombre} (${estudio.fecha_creacion}) - ${estudio.programa_nombre}`;
        option.setAttribute('data-info', JSON.stringify(estudio));
        select.appendChild(option);
    });

    // Inicializar Select2 después de cargar las opciones
    inicializarSelect2Estudios();
}

    // Función para inicializar Select2
    function inicializarSelect2Estudios() {
        $('#select-estudio-existente').select2({
            placeholder: "Buscar estudio por folio, beneficiario o programa...",
            allowClear: true,
            language: "es",
            width: '100%',
            dropdownParent: $('#estudioSocioeconomicoModal'), // IMPORTANTE para que funcione en modal
            templateResult: function(estudio) {
                if (!estudio.id) return estudio.text;

                const data = $(estudio.element).data('info') || JSON.parse(estudio.element.getAttribute('data-info') || '{}');

                if (data.folio) {
                    return $(
                        `<div>
                            <strong>${data.folio}</strong><br>
                            <small class="text-muted">
                                ${data.beneficiario_nombre} | ${data.fecha_creacion} | ${data.programa_nombre}
                            </small>
                        </div>`
                    );
                }
                return estudio.text;
            },
            templateSelection: function(estudio) {
                if (!estudio.id) return estudio.text;

                const data = $(estudio.element).data('info') || JSON.parse(estudio.element.getAttribute('data-info') || '{}');

                if (data.folio) {
                    return `${data.folio} - ${data.beneficiario_nombre}`;
                }
                return estudio.text;
            }
        });

        // Re-asignar el event listener para el cambio
        $('#select-estudio-existente').off('change').on('change', function() {
            const estudioId = this.value;
            const btnVincular = document.getElementById('btn-confirmar-vinculacion');
            const infoContainer = document.getElementById('info-estudio-seleccionado');
            const detalles = document.getElementById('detalles-estudio');

            if (estudioId) {
                const estudio = estudiosDisponibles.find(e => e.id == estudioId);
                if (estudio) {
                    detalles.innerHTML = `
                        <strong>Folio:</strong> ${estudio.folio}<br>
                        <strong>Beneficiario principal:</strong> ${estudio.beneficiario_nombre}<br>
                        <strong>Fecha:</strong> ${estudio.fecha_creacion}<br>
                        <strong>Programa:</strong> ${estudio.programa_nombre}
                    `;
                    infoContainer.classList.remove('d-none');
                    btnVincular.disabled = false;
                    btnVincular.classList.remove('d-none');
                }
            } else {
                infoContainer.classList.add('d-none');
                btnVincular.disabled = true;
                btnVincular.classList.remove('d-none');
            }
        });
    }

    document.querySelectorAll('.option-card').forEach(card => {
        card.addEventListener('click', function() {
            const opcion = this.getAttribute('data-option');
            
            document.querySelectorAll('.option-card').forEach(c => {
                c.classList.remove('selected');
            });
            
            this.classList.add('selected');
            
            const btnCrear = document.getElementById('btn-crear-estudio');
            const btnVincular = document.getElementById('btn-confirmar-vinculacion');
            const selectorContainer = document.getElementById('seleccion-estudio-container');
            
            switch(opcion) {
                case 'nuevo':
                    selectorContainer.classList.add('d-none');
                    btnCrear.classList.remove('d-none');
                    btnVincular.classList.add('d-none');
                    break;
                    
                case 'vincular':
                    selectorContainer.classList.remove('d-none');
                    btnCrear.classList.add('d-none');
                    btnVincular.classList.add('d-none'); // Oculto hasta seleccionar estudio
                    btnVincular.disabled = true;
                    break;
                    
                case 'despues':
                    selectorContainer.classList.add('d-none');
                    btnCrear.classList.add('d-none');
                    btnVincular.classList.add('d-none');

                    const modal = bootstrap.Modal.getInstance(document.getElementById('estudioSocioeconomicoModal'));
                    if (modal) {
                        modal.hide();
                    }
                    break;
            }
        });
    });

    // Event listener para crear estudio
    document.getElementById('btn-crear-estudio').addEventListener('click', function() {
        if (beneficiarioIdActual) {
            window.location.href = `/estudios/create/${beneficiarioIdActual}`;
        }
    });

    // Event listener para vincular estudio
    document.getElementById('btn-confirmar-vinculacion').addEventListener('click', function() {
        const estudioId = document.getElementById('select-estudio-existente').value;
        
        if (beneficiarioIdActual && estudioId) {
            vincularBeneficiarioAEstudio(beneficiarioIdActual, estudioId);
        }
    });

    function vincularBeneficiarioAEstudio(beneficiarioId, estudioId) {
        const btnVincular = document.getElementById('btn-confirmar-vinculacion');
        const originalText = btnVincular.innerHTML;
        
        btnVincular.innerHTML = '<i class="bi bi-hourglass-split"></i> Vinculando...';
        btnVincular.disabled = true;

        fetch(`/api/beneficiarios/${beneficiarioId}/vincular-estudio`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                estudio_id: estudioId
            })
        })
        .then(async response => {
            const text = await response.text();

            try {
                const data = JSON.parse(text);

                if (!response.ok) {
                    throw new Error(data.message || `Error ${response.status}: ${response.statusText}`);
                }

                return data;
            } catch (e) {
                if (!response.ok) {
                    throw new Error(`Error ${response.status}: ${response.statusText}`);
                }
                throw new Error('Respuesta inesperada del servidor');
            }
        })
        .then(data => {
            if (data.success) {
                mostrarMensajeExito(data.message);

                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('estudioSocioeconomicoModal'));
                    if (modal) {
                        modal.hide();
                    }

                    window.location.reload();

                }, 1500);
            } else {
                mostrarMensajeError(data.message || 'Error al vincular el beneficiario.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarMensajeError('Error de conexión: ' + error.message);
        })
        .finally(() => {
            btnVincular.innerHTML = originalText;
            btnVincular.disabled = false;
        });
    }    

    function mostrarMensajeExito(mensaje) {
        alert('✅ ' + mensaje);
    }

    function mostrarMensajeError(mensaje) {
        alert('❌ ' + mensaje);
    }

    // Hacer la función global para que pueda ser llamada desde otros scripts
    window.mostrarOpcionesEstudio = mostrarOpcionesEstudio;

    // Delegación de eventos para botones que se agregan dinámicamente
    document.addEventListener('click', function(e) {
        if (e.target.closest('.opciones-estudio-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const btn = e.target.closest('.opciones-estudio-btn');
            const beneficiarioId = btn.getAttribute('data-beneficiario-id');
            const beneficiarioNombre = btn.getAttribute('data-beneficiario-nombre');
            
            if (typeof mostrarOpcionesEstudio === 'function') {
                mostrarOpcionesEstudio(beneficiarioId, beneficiarioNombre);
            }
        }
        
        // Botón de ver estudios existentes (badge/número)
        if (e.target.closest('.ver-estudios-existente-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const btn = e.target.closest('.ver-estudios-existente-btn');
            const beneficiarioId = btn.getAttribute('data-beneficiario-id');
            const beneficiarioNombre = btn.getAttribute('data-beneficiario-nombre');
            
            if (typeof cargarEstudiosBeneficiario === 'function') {
                cargarEstudiosBeneficiario(beneficiarioId, beneficiarioNombre);
            } 
        }
    });

    const modalEstudio = document.getElementById('estudioSocioeconomicoModal');
    if (modalEstudio) {
        modalEstudio.addEventListener('hidden.bs.modal', function() {
            if ($('#select-estudio-existente').hasClass('select2-hidden-accessible')) {
                $('#select-estudio-existente').select2('destroy');
            }
        });
    }

});
</script>