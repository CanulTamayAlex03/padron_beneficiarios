<script>
    document.addEventListener('DOMContentLoaded', function() {
        const baseUrl = '/beneficiarios';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 'test-token';

        /* ---------- VER detalles (fetch /{id}) ---------- */
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.view-btn');
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

                    // Datos básicos
                    document.getElementById('view-id').textContent = d.id ?? '';
                    document.getElementById('view-nombres').textContent = d.nombres ?? '';
                    document.getElementById('view-primer_apellido').textContent = d.primer_apellido ?? '';
                    document.getElementById('view-segundo_apellido').textContent = d.segundo_apellido ?? '';
                    document.getElementById('view-curp').textContent = d.curp ?? '';

                    // Fecha de nacimiento formateada
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

                    document.getElementById('view-estado_nac').textContent = d.estado_nac ?? 'No especificado';
                    document.getElementById('view-sexo').textContent = d.sexo ? (d.sexo === 'M' ? 'Masculino' : d.sexo === 'F' ? 'Femenino' : 'Otro') : 'No especificado';
                    document.getElementById('view-ocupacion').textContent = d.ocupacion ?? 'No especificado';
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

            // Mostrar loading temporal si existen campos
            const fillLoading = () => {
                if (document.getElementById('edit_nombres')) document.getElementById('edit_nombres').value = 'Cargando...';
                if (document.getElementById('edit_apellidos')) document.getElementById('edit_apellidos').value = 'Cargando...';
                if (document.getElementById('edit_curp')) document.getElementById('edit_curp').value = 'Cargando...';
            };
            fillLoading();

            fetch(`${baseUrl}/${id}/edit`, {
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

                    if (document.getElementById('edit_ocupacion')) document.getElementById('edit_ocupacion').value = d.ocupacion ?? '';

                    if (document.getElementById('edit_estado_nac')) document.getElementById('edit_estado_nac').value = d.estado_nac ?? '';
                    if (document.getElementById('edit_sexo')) document.getElementById('edit_sexo').value = d.sexo ?? '';
                    if (document.getElementById('edit_estado_civil')) document.getElementById('edit_estado_civil').value = d.estado_civil ?? '';

                    if (document.getElementById('edit_discapacidad')) document.getElementById('edit_discapacidad').checked = !!d.discapacidad;
                    if (document.getElementById('edit_indigena')) document.getElementById('edit_indigena').checked = !!d.indigena;
                    if (document.getElementById('edit_maya_hablante')) document.getElementById('edit_maya_hablante').checked = !!d.maya_hablante;
                    if (document.getElementById('edit_afromexicano')) document.getElementById('edit_afromexicano').checked = !!d.afromexicano;

                    // Establecer action del formulario de edición
                    const editForm = document.getElementById('editBeneficiarioForm');
                    if (editForm) editForm.action = `${baseUrl}/${id}`;

                    // Si el modal no se abrió automáticamente, abrirlo
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

        /* ---------- ELIMINAR: confirmación y envío ---------- */
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.delete-btn');
            if (!btn) return;

            const id = btn.getAttribute('data-id');
            const nombres = btn.getAttribute('data-nombres');
            const apellidos = btn.getAttribute('data-apellidos');

            if (!id) return;

            // Configurar el modal de confirmación
            document.getElementById('deleteBeneficiarioName').textContent = `${nombres} ${apellidos}`;
            document.getElementById('deleteForm').action = `${baseUrl}/${id}`;

            // Mostrar el modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        });

        // Manejar el envío del formulario de eliminación
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

                            // Recargar la página después de un breve delay
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
                e.preventDefault(); // ← PREVENIR ENVÍO NORMAL DEL FORMULARIO
                console.log('Enviando formulario de creación (AJAX)');

                // Validar CURP antes de enviar
                const curpInput = document.getElementById('create_curp');
                const curpConfirmInput = document.getElementById('create_curp_confirm');

                const curp = curpInput.value.trim();
                const curpConfirm = curpConfirmInput.value.trim();

                // Validaciones de CURP
                if ((curp === '' && curpConfirm !== '') || (curp !== '' && curpConfirm === '')) {
                    window.showAlert('Debe completar ambos campos de CURP o dejarlos vacíos', 'danger');
                    return;
                }

                if (curp !== curpConfirm) {
                    window.showAlert('Las CURP no coinciden', 'danger');
                    return;
                }

                if (curp !== '' && curp.length !== 18) {
                    window.showAlert('La CURP debe tener exactamente 18 caracteres', 'danger');
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
                    .then(window.handleJsonResponse)
                    .then(result => {
                        if (result.ok) {
                            // Cerrar el modal inmediatamente
                            const modalEl = document.getElementById('createBeneficiarioModal');
                            if (modalEl) {
                                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                                if (modalInstance) {
                                    modalInstance.hide();
                                }
                            }

                            const msg = result.data && result.data.message ?
                                result.data.message :
                                'Beneficiario creado correctamente.';

                            // Mostrar éxito FUERA del modal (en la página principal)
                            window.showAlert(msg, 'success');

                            // Recargar la página después de un breve delay
                            setTimeout(() => window.location.reload(), 900);
                        } else {
                            // MOSTRAR ERRORES DENTRO DEL MODAL SOLAMENTE
                            if (result.status === 422 && result.messages) {
                                // Limpiar errores previos
                                clearModalErrors();

                                // Mostrar errores específicos en cada campo del modal
                                showModalErrors(result.messages);
                            } else {
                                // Para otros errores, mostrar dentro del modal
                                const errorContainer = document.getElementById('modal-general-error');
                                if (errorContainer) {
                                    errorContainer.innerHTML = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error:</strong> ${result.messages.join('<br>')}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `;
                                    errorContainer.classList.remove('d-none');
                                }
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Error al crear beneficiario:', err);
                        // Mostrar error dentro del modal
                        const errorContainer = document.getElementById('modal-general-error');
                        if (errorContainer) {
                            errorContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error:</strong> Error al crear el beneficiario
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                            errorContainer.classList.remove('d-none');
                        }
                    });
            });
        }

        // Función para limpiar errores previos en el modal
        function clearModalErrors() {
            // Limpiar errores de campos
            const errorElements = document.querySelectorAll('#createBeneficiarioModal .field-error');
            errorElements.forEach(el => el.remove());

            // Limpiar mensajes de error general
            const generalError = document.getElementById('modal-general-error');
            if (generalError) {
                generalError.classList.add('d-none');
                generalError.innerHTML = '';
            }
        }

        // Función para mostrar errores específicos en campos del modal
        function showModalErrors(messages) {
            messages.forEach(message => {
                // Buscar a qué campo pertenece el error
                const fieldMatch = message.match(/\[(.*?)\]/);
                if (fieldMatch) {
                    const fieldName = fieldMatch[1].toLowerCase();
                    const field = document.querySelector(`[name="${fieldName}"]`);

                    if (field) {
                        // Crear elemento de error
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'text-danger small mt-1 field-error';
                        errorDiv.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${message.replace(/\[.*?\] /, '')}`;

                        // Insertar después del campo
                        field.closest('.mb-3')?.appendChild(errorDiv);

                        // Resaltar el campo con error
                        field.classList.add('is-invalid');
                    }
                } else {
                    // Error general - mostrar en el contenedor general
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

        console.log('Event listeners de modals configurados correctamente');
    });
</script>