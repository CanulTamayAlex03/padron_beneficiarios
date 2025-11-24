<script>
    document.addEventListener('DOMContentLoaded', function() {
        const baseUrl = '/beneficiarios';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 'test-token';

        /* ---------- VER detalles (fetch /{id}) ---------- */
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.view-details-name');
            if (!btn) return;
            const id = btn.getAttribute('data-id');
            if (!id) return;
            console.log('Viendo detalles ID:', id);

            fetch(`${baseUrl}/${id}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(async res => {
                    if (!res.ok) throw new Error('Respuesta no OK');
                    return res.json();
                })
                .then(data => {
                    const d = data.data || data;

                    document.getElementById('view-id').textContent = d.id ?? '';
                    document.getElementById('view-nombres').textContent = d.nombres ?? '';
                    document.getElementById('view-primer_apellido').textContent = d.primer_apellido ?? '';
                    document.getElementById('view-segundo_apellido').textContent = d.segundo_apellido ?? '';
                    document.getElementById('view-curp').textContent = d.curp ?? '';

                    if (d.fecha_nac) {
                        const fechaNac = new Date(d.fecha_nac);
                        const fechaAjustada = new Date(fechaNac.getTime() + fechaNac.getTimezoneOffset() * 60000);
                        document.getElementById('view-fecha_nac').textContent = fechaAjustada.toLocaleDateString('es-MX');

                        const edad = window.calcularEdad(fechaAjustada);
                        document.getElementById('view-edad').textContent = `${edad} años`;
                    } else {
                        document.getElementById('view-fecha_nac').textContent = 'No especificado';
                        document.getElementById('view-edad').textContent = 'No especificado';
                    }

                    document.getElementById('view-estado_nac').textContent = d.estado?.nombre ?? d.estado_nacimiento ?? 'No especificado';
                    document.getElementById('view-sexo').textContent = d.sexo ? (d.sexo === 'M' ? 'Masculino' : d.sexo === 'F' ? 'Femenino' : 'Otro') : 'No especificado';
                    document.getElementById('view-ocupacion').textContent = d.ocupacion?.ocupacion ?? d.ocupacion_nombre ?? 'No especificado';
                    document.getElementById('view-estado_civil').textContent = d.estado_civil ?? 'No especificado';

                    document.getElementById('view-discapacidad').textContent = d.discapacidad ? 'Sí' : 'No';
                    document.getElementById('view-indigena').textContent = d.indigena ? 'Sí' : 'No';
                    document.getElementById('view-maya_hablante').textContent = d.maya_hablante ? 'Sí' : 'No';
                    document.getElementById('view-afromexicano').textContent = d.afromexicano ? 'Sí' : 'No';

                    document.getElementById('view-created').textContent = d.created_at ? new Date(d.created_at).toLocaleString('es-MX') : '';
                    document.getElementById('view-updated').textContent = d.updated_at ? new Date(d.updated_at).toLocaleString('es-MX') : '';

                    const viewModal = new bootstrap.Modal(document.getElementById('viewBeneficiarioModal'));
                    viewModal.show();
                })

                .catch(err => {
                    console.error('Error al cargar detalles:', err);
                    window.showAlert('Error al cargar los detalles', 'danger');
                });
        });

        /* ---------- EDITAR: abrir modal y cargar datos (/id/edit) ---------- */
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.edit-btn');
            if (!btn) return;
            const id = btn.getAttribute('data-id');
            if (!id) return;
            console.log('Editando beneficiario ID:', id);

            const fillLoading = () => {
                if (document.getElementById('edit_nombres')) document.getElementById('edit_nombres').value = 'Cargando...';
                if (document.getElementById('edit_primer_apellido')) document.getElementById('edit_primer_apellido').value = 'Cargando...';
                if (document.getElementById('edit_segundo_apellido')) document.getElementById('edit_segundo_apellido').value = 'Cargando...';
                if (document.getElementById('edit_curp')) document.getElementById('edit_curp').value = 'Cargando...';
                if (document.getElementById('edit_estado_id')) document.getElementById('edit_estado_id').value = 'Cargando...';
                if (document.getElementById('edit_ocupacion_id')) document.getElementById('edit_ocupacion_id').value = 'Cargando...';

                if (document.getElementById('edit_calle')) document.getElementById('edit_calle').value = 'Cargando...';
                if (document.getElementById('edit_estado_viv_id')) document.getElementById('edit_estado_viv_id').value = 'Cargando...';
                if (document.getElementById('edit_municipio_id')) document.getElementById('edit_municipio_id').value = 'Cargando...';
            };
            fillLoading();

            fetch(`${baseUrl}/${id}/editar`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(async res => {
                    if (!res.ok) throw new Error('Respuesta no OK del servidor');
                    return res.json();
                })
                .then(data => {
                    const d = data.data || data;

                    if (document.getElementById('edit_nombres')) document.getElementById('edit_nombres').value = d.nombres ?? '';
                    if (document.getElementById('edit_primer_apellido')) document.getElementById('edit_primer_apellido').value = d.primer_apellido ?? '';
                    if (document.getElementById('edit_segundo_apellido')) document.getElementById('edit_segundo_apellido').value = d.segundo_apellido ?? '';
                    if (document.getElementById('edit_curp')) document.getElementById('edit_curp').value = d.curp ?? '';
                    if (document.getElementById('edit_estado_id')) document.getElementById('edit_estado_id').value = d.estado_id ?? '';
                    if (document.getElementById('edit_ocupacion_id')) document.getElementById('edit_ocupacion_id').value = d.ocupacion_id ?? '';

                    if (document.getElementById('edit_fecha_nac')) {
                        if (d.fecha_nac) {
                            const fecha = new Date(d.fecha_nac);
                            if (!isNaN(fecha)) {
                                const formattedDate = fecha.toISOString().split('T')[0];
                                document.getElementById('edit_fecha_nac').value = formattedDate;
                            } else {
                                document.getElementById('edit_fecha_nac').value = '';
                            }
                        } else {
                            document.getElementById('edit_fecha_nac').value = '';
                        }
                    }

                    if (document.getElementById('edit_sexo')) document.getElementById('edit_sexo').value = d.sexo ?? '';
                    if (document.getElementById('edit_estado_civil')) document.getElementById('edit_estado_civil').value = d.estado_civil ?? '';

                    if (document.getElementById('edit_discapacidad')) document.getElementById('edit_discapacidad').checked = !!d.discapacidad;
                    if (document.getElementById('edit_indigena')) document.getElementById('edit_indigena').checked = !!d.indigena;
                    if (document.getElementById('edit_maya_hablante')) document.getElementById('edit_maya_hablante').checked = !!d.maya_hablante;
                    if (document.getElementById('edit_afromexicano')) document.getElementById('edit_afromexicano').checked = !!d.afromexicano;

                    if (document.getElementById('edit_calle')) document.getElementById('edit_calle').value = d.calle ?? '';
                    if (document.getElementById('edit_numero')) document.getElementById('edit_numero').value = d.numero ?? '';
                    if (document.getElementById('edit_letra')) document.getElementById('edit_letra').value = d.letra ?? '';
                    if (document.getElementById('edit_cruzamiento_1')) document.getElementById('edit_cruzamiento_1').value = d.cruzamiento_1 ?? '';
                    if (document.getElementById('edit_cruzamiento_2')) document.getElementById('edit_cruzamiento_2').value = d.cruzamiento_2 ?? '';
                    if (document.getElementById('edit_tipo_asentamiento')) document.getElementById('edit_tipo_asentamiento').value = d.tipo_asentamiento ?? '';
                    if (document.getElementById('edit_colonia_fracc')) document.getElementById('edit_colonia_fracc').value = d.colonia_fracc ?? '';
                    if (document.getElementById('edit_estado_viv_id')) document.getElementById('edit_estado_viv_id').value = d.estado_viv_id ?? '';
                    if (document.getElementById('edit_municipio_id')) document.getElementById('edit_municipio_id').value = d.municipio_id ?? '';
                    if (document.getElementById('edit_localidad')) document.getElementById('edit_localidad').value = d.localidad ?? '';
                    if (document.getElementById('edit_cp')) document.getElementById('edit_cp').value = d.cp ?? '';
                    if (document.getElementById('edit_telefono')) document.getElementById('edit_telefono').value = d.telefono ?? '';
                    if (document.getElementById('edit_referencias_domicilio')) document.getElementById('edit_referencias_domicilio').value = d.referencias_domicilio ?? '';

                    if (document.getElementById('edit_estado_viv_id') && document.getElementById('edit_municipio_id')) {

                        setTimeout(() => {
                            if (document.getElementById('edit_municipio_id')) {
                                document.getElementById('edit_municipio_id').value = d.municipio_id ?? '';
                            }
                        }, 100);
                    }

                    const editForm = document.getElementById('editBeneficiarioForm');
                    if (editForm) editForm.action = `${baseUrl}/${id}`;

                    const editModalEl = document.getElementById('editBeneficiarioModal');
                    if (editModalEl) {
                        const modal = bootstrap.Modal.getOrCreateInstance(editModalEl);
                        modal.show();
                    }
                })
                .catch(err => {
                    console.error('Error al cargar datos para editar:', err);
                    window.showAlert('Error al cargar datos para editar', 'danger');
                });
        });

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.delete-btn');
            if (!btn) return;

            const id = btn.getAttribute('data-id');
            const nombres = btn.getAttribute('data-nombres');
            const apellidos = btn.getAttribute('data-apellidos');

            if (!id) return;

            document.getElementById('deleteBeneficiarioName').textContent = `${nombres} ${apellidos}`;
            document.getElementById('deleteForm').action = `${baseUrl}/${id}`;

            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        });

        const deleteForm = document.getElementById('deleteForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(deleteForm);
                const url = deleteForm.action;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(window.handleJsonResponse)
                    .then(result => {
                        const deleteModalEl = document.getElementById('deleteConfirmModal');
                        if (deleteModalEl) bootstrap.Modal.getOrCreateInstance(deleteModalEl).hide();

                        if (result.ok) {
                            const msg = result.data && result.data.message ? result.data.message : 'Beneficiario eliminado correctamente.';
                            window.showAlert(msg, 'success');

                            setTimeout(() => window.location.reload(), 900);
                        } else {
                            window.showAlert(result.messages || ['Error al eliminar beneficiario'], 'danger');
                        }
                    })
                    .catch(err => {
                        console.error('Error al eliminar beneficiario:', err);
                        window.showAlert('Error al eliminar el beneficiario', 'danger');
                    });
            });
        }

        /* ---------- SUBMIT crear (AJAX) ---------- */
        const createForm = document.getElementById('createBeneficiarioForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Enviando formulario de creación (AJAX)');

                const submitBtn = document.getElementById('submit-btn');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Guardando...';
                submitBtn.disabled = true;

                const curpInput = document.getElementById('create_curp');
                const curpConfirmInput = document.getElementById('create_curp_confirm');
                const curp = curpInput.value.trim();
                const curpConfirm = curpConfirmInput.value.trim();

                if ((curp === '' && curpConfirm !== '') || (curp !== '' && curpConfirm === '')) {
                    window.showAlert('Debe completar ambos campos de CURP o dejarlos vacíos', 'danger');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    return;
                }

                if (curp !== curpConfirm) {
                    window.showAlert('Las CURP no coinciden', 'danger');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    return;
                }

                if (curp !== '' && curp.length !== 18) {
                    window.showAlert('La CURP debe tener exactamente 18 caracteres', 'danger');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    return;
                }

                const formData = new FormData(createForm);

                fetch(createForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(async response => {
                        const data = await response.json();
                        return {
                            ok: response.ok,
                            status: response.status,
                            data: data
                        };
                    })
                    .then(result => {

                        if (result.ok && result.data.success) {
                
                            const modalEl = document.getElementById('createBeneficiarioModal');
                            if (modalEl) {
                                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                                if (modalInstance) {
                                    modalInstance.hide();
                                }
                            }

                            const msg = result.data.message || 'Beneficiario creado correctamente.';
                            window.showAlert(msg, 'success');

                            if (result.data.data) {
                                window.ultimoBeneficiarioCreado = result.data.data;
                                console.log('Beneficiario guardado:', window.ultimoBeneficiarioCreado);

                                setTimeout(() => {
                                    mostrarModalEstudioSocioeconomico(window.ultimoBeneficiarioCreado);
                                }, 800);
                            } else {
                                setTimeout(() => window.location.reload(), 1500);
                            }

                        } else {
                            console.error('Error en la respuesta:', result);

                            if (result.status === 422 && result.data.errors) {
                                clearModalErrors();
                                showModalErrors(Object.values(result.data.errors).flat());
                            } else {
                                const errorMsg = result.data.message || 'Error al crear el beneficiario';
                                const errorContainer = document.getElementById('modal-general-error');
                                if (errorContainer) {
                                    errorContainer.innerHTML = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> ${errorMsg}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                                    errorContainer.classList.remove('d-none');
                                }
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Error en la petición:', err);
                        window.showAlert('Error de conexión al crear el beneficiario', 'danger');
                    })
                    .finally(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });
        }

        function clearModalErrors() {

            const errorElements = document.querySelectorAll('#createBeneficiarioModal .field-error');
            errorElements.forEach(el => el.remove());

            const invalidInputs = document.querySelectorAll('#createBeneficiarioModal .is-invalid');
            invalidInputs.forEach(input => input.classList.remove('is-invalid'));

            const generalError = document.getElementById('modal-general-error');
            if (generalError) {
                generalError.classList.add('d-none');
                generalError.innerHTML = '';
            }
        }

        function showModalErrors(messages) {
            messages.forEach(message => {
                const fieldMatch = message.match(/\[(.*?)\]/);
                if (fieldMatch) {
                    const fieldName = fieldMatch[1].toLowerCase();
                    const field = document.querySelector(`[name="${fieldName}"]`);

                    if (field) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'text-danger small mt-1 field-error';
                        errorDiv.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${message.replace(/\[.*?\] /, '')}`;

                        const parent = field.closest('.mb-3') || field.closest('.col-md-6') || field.parentElement;
                        if (parent) {
                            parent.appendChild(errorDiv);
                        }

                        field.classList.add('is-invalid');
                    }
                } else {
                    const errorContainer = document.getElementById('modal-general-error');
                    if (errorContainer) {
                        errorContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error:</strong> ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                        errorContainer.classList.remove('d-none');
                    }
                }
            });
        }

        /* ---------- SUBMIT editar (AJAX) ---------- */
        const editForm = document.getElementById('editBeneficiarioForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Enviando formulario de edición (AJAX)');

                const formData = new FormData(editForm);

                fetch(editForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(window.handleJsonResponse)
                    .then(result => {
                        if (result.ok) {
                            const modalEl = document.getElementById('editBeneficiarioModal');
                            if (modalEl) {
                                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                                if (modalInstance) {
                                    modalInstance.hide();
                                }
                            }

                            const msg = result.data && result.data.message ?
                                result.data.message :
                                'Beneficiario actualizado correctamente.';

                            window.showAlert(msg, 'success');

                            // Recargar la página después de un breve delay
                            setTimeout(() => window.location.reload(), 900);
                        } else {
                            if (result.status === 422) {
                                window.showAlert(result.messages, 'danger');
                            } else {
                                window.showAlert(result.messages || ['Error al actualizar beneficiario'], 'danger');
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Error al actualizar beneficiario:', err);
                        window.showAlert('Error al actualizar el beneficiario', 'danger');
                    });
            });
        }

        /* ---------- Reset del formulario de edición al cerrar modal ---------- */
        const editModalEl = document.getElementById('editBeneficiarioModal');
        if (editModalEl) {
            editModalEl.addEventListener('hidden.bs.modal', function() {
                if (editForm) editForm.reset();
            });
        }

        /* ---------- Función mejorada para mostrar modal de estudio ---------- */
        window.mostrarModalEstudioSocioeconomico = function(beneficiarioData) {
            console.log(' Mostrando modal de opciones para:', beneficiarioData);
        
            if (!beneficiarioData || !beneficiarioData.id) {
                console.error('Datos del beneficiario inválidos:', beneficiarioData);
                window.showAlert('Error: No se pudieron obtener los datos del beneficiario', 'warning');
                setTimeout(() => window.location.reload(), 1500);
                return;
            }
        
            // Usar la función GLOBAL que ya existe en el otro script
            if (typeof mostrarOpcionesEstudio === 'function') {
                const nombreCompleto = `${beneficiarioData.nombres || ''} ${beneficiarioData.primer_apellido || ''} ${beneficiarioData.segundo_apellido || ''}`.trim();
                const curpInfo = beneficiarioData.curp ? `(CURP: ${beneficiarioData.curp})` : '';

                mostrarOpcionesEstudio(beneficiarioData.id, `${nombreCompleto} ${curpInfo}`);
            } else {
                console.error('La función mostrarOpcionesEstudio no está disponible');
                // Fallback: redirigir directamente a crear estudio
                window.location.href = `/estudios/create/${beneficiarioData.id}`;
            }
        };

        // Event listener para el botón "Realizar Estudio"
        const btnIniciarEstudio = document.getElementById('btn-iniciar-estudio');
        if (btnIniciarEstudio) {
            btnIniciarEstudio.addEventListener('click', function() {
                console.log('Botón "Realizar Estudio" clickeado');
                console.log('Datos disponibles:', window.ultimoBeneficiarioCreado);

                if (window.ultimoBeneficiarioCreado && window.ultimoBeneficiarioCreado.id) {
                    // Construir URL correctamente usando la ruta nombrada
                    const estudioUrl = "{{ route('estudios.create', ['beneficiario' => 'ID']) }}".replace('ID', window.ultimoBeneficiarioCreado.id);
                    console.log('Redirigiendo a:', estudioUrl);

                    window.location.href = estudioUrl;
                } else {
                    console.error('No hay datos válidos del beneficiario');
                    window.showAlert('Error: No se pudo redirigir al estudio socioeconómico', 'danger');

                    const modal = bootstrap.Modal.getInstance(document.getElementById('estudioSocioeconomicoModal'));
                    if (modal) modal.hide();

                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        }
        console.log('Event listeners de modals configurados correctamente');
    });

</script>