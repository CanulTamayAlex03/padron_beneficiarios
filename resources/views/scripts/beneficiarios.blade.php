<script>
        document.addEventListener('DOMContentLoaded', function() {
            const baseUrl = '/beneficiarios';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 'test-token';
            console.log('Script de beneficiarios cargado');

            // Variables para validación de CURP
            let curpValida = true;
            let curpCoincide = true;
            
            // Función para validar formato de CURP
            window.validarCurp = function() {
                const curpInput = document.getElementById('create_curp');
                const curpError = document.getElementById('curp-error');
                const curp = curpInput.value.trim();
                
                // Si está vacío, es válido (es opcional)
                if (curp === '') {
                    curpError.classList.add('d-none');
                    curpValida = true;
                    actualizarEstadoBoton();
                    return;
                }
                
                // Validar longitud
                if (curp.length !== 18) {
                    curpError.classList.remove('d-none');
                    curpValida = false;
                } else {
                    curpError.classList.add('d-none');
                    curpValida = true;
                }
                
                // Validar coincidencia si el campo de confirmación no está vacío
                const curpConfirm = document.getElementById('create_curp_confirm').value.trim();
                if (curpConfirm !== '') {
                    validarConfirmacionCurp();
                }
                
                actualizarEstadoBoton();
            }
            
            // Función para validar que las CURP coincidan
            window.validarConfirmacionCurp = function() {
                const curpInput = document.getElementById('create_curp');
                const curpConfirmInput = document.getElementById('create_curp_confirm');
                const curpConfirmError = document.getElementById('curp-confirm-error');
                
                const curp = curpInput.value.trim();
                const curpConfirm = curpConfirmInput.value.trim();
                
                // Si ambos están vacíos, es válido
                if (curp === '' && curpConfirm === '') {
                    curpConfirmError.classList.add('d-none');
                    curpCoincide = true;
                    actualizarEstadoBoton();
                    return;
                }
                
                // Si uno está vacío y el otro no, mostrar error
                if ((curp === '' && curpConfirm !== '') || (curp !== '' && curpConfirm === '')) {
                    curpConfirmError.classList.remove('d-none');
                    curpConfirmError.textContent = 'Ambos campos de CURP deben estar completos';
                    curpCoincide = false;
                    actualizarEstadoBoton();
                    return;
                }
                
                // Verificar coincidencia
                if (curp !== curpConfirm) {
                    curpConfirmError.classList.remove('d-none');
                    curpConfirmError.textContent = 'Las CURP no coinciden';
                    curpCoincide = false;
                } else {
                    curpConfirmError.classList.add('d-none');
                    curpCoincide = true;
                }
                
                actualizarEstadoBoton();
            }
            
            // Función para actualizar el estado del botón de envío
            function actualizarEstadoBoton() {
                const submitBtn = document.getElementById('submit-btn');
                
                if (!curpValida || !curpCoincide) {
                    submitBtn.disabled = true;
                } else {
                    submitBtn.disabled = false;
                }
            }
            
            // Limpiar validación al cerrar el modal
            const createModal = document.getElementById('createBeneficiarioModal');
            if (createModal) {
                createModal.addEventListener('hidden.bs.modal', function() {
                    document.getElementById('curp-error').classList.add('d-none');
                    document.getElementById('curp-confirm-error').classList.add('d-none');
                    curpValida = true;
                    curpCoincide = true;
                    
                    const submitBtn = document.getElementById('submit-btn');
                    if (submitBtn) submitBtn.disabled = false;
                });
            }

            function showAlert(message, type = 'success', prependTo = '.card .card-body') {
                const container = document.querySelector(prependTo) || document.querySelector('body');
                const wrapper = document.createElement('div');

                // Si message es array -> unir con <br>
                const msgHtml = Array.isArray(message) ? message.join('<br>') : message;

                wrapper.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${type === 'success' ? '<strong>¡Éxito!</strong> ' : '<strong>Error:</strong> '}
                <span class="ms-1">${msgHtml}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
                container.prepend(wrapper.firstElementChild);

                // Auto-dismiss después de 5s
                setTimeout(() => {
                    const alertEl = container.querySelector('.alert');
                    if (alertEl) {
                        bootstrap.Alert.getOrCreateInstance(alertEl).close();
                    }
                }, 5000);
            }

            /* ---------- Inicializar tooltips (Bootstrap 5) ---------- */
            (function initTooltips() {
                const triggers = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                triggers.forEach(el => new bootstrap.Tooltip(el));
            })();

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

                            const edad = calcularEdad(fechaAjustada);
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
                        showAlert('Error al cargar los detalles', 'danger');
                    });

                function calcularEdad(fechaNacimiento) {
                    const hoy = new Date();
                    const nacimiento = new Date(fechaNacimiento);

                    let edad = hoy.getFullYear() - nacimiento.getFullYear();
                    const mes = hoy.getMonth() - nacimiento.getMonth();

                    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                        edad--;
                    }

                    return edad;
                }
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
                        showAlert('Error al cargar datos para editar', 'danger');
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
                        .then(handleJsonResponse)
                        .then(result => {
                            const deleteModalEl = document.getElementById('deleteConfirmModal');
                            if (deleteModalEl) bootstrap.Modal.getOrCreateInstance(deleteModalEl).hide();

                            if (result.ok) {
                                const msg = result.data && result.data.message ? result.data.message : 'Beneficiario eliminado correctamente.';
                                showAlert(msg, 'success');

                                // Recargar la página después de un breve delay
                                setTimeout(() => window.location.reload(), 900);
                            } else {
                                showAlert(result.messages || ['Error al eliminar beneficiario'], 'danger');
                            }
                        })
                        .catch(err => {
                            console.error('Error al eliminar beneficiario:', err);
                            showAlert('Error al eliminar el beneficiario', 'danger');
                        });
                });
            }

            /* ---------- FUNCION de manejo de respuestas JSON (OK / validation errors / otros) ---------- */
            async function handleJsonResponse(response) {
                const contentType = response.headers.get('content-type') || '';
                const isJson = contentType.includes('application/json');
                const payload = isJson ? await response.json() : null;

                if (response.ok) {
                    return {
                        ok: true,
                        data: payload
                    };
                }

                // 422 -> validación
                if (response.status === 422 && payload && payload.errors) {
                    // convertir errors a array
                    const messages = [];
                    Object.values(payload.errors).forEach(arr => messages.push(...arr));
                    return {
                        ok: false,
                        status: 422,
                        messages
                    };
                }

                // Otros errores con mensaje
                const message = payload && (payload.message || payload.error) ? (payload.message || payload.error) : (`Error ${response.status}`);
                return {
                    ok: false,
                    status: response.status,
                    messages: [message]
                };
            }

            /* ---------- SUBMIT crear (AJAX) ---------- */
            const createForm = document.getElementById('createBeneficiarioForm');
            if (createForm) {
                createForm.addEventListener('submit', function(e) {
                    // Validar CURP antes de enviar
                    const curpInput = document.getElementById('create_curp');
                    const curpConfirmInput = document.getElementById('create_curp_confirm');
                    
                    const curp = curpInput.value.trim();
                    const curpConfirm = curpConfirmInput.value.trim();
                    
                    // Si ambos están vacíos, permitir envío
                    if (curp === '' && curpConfirm === '') {
                        // Continuar con el envío
                    } 
                    // Si solo uno está completado, prevenir envío
                    else if ((curp === '' && curpConfirm !== '') || (curp !== '' && curpConfirm === '')) {
                        e.preventDefault();
                        showAlert('Debe completar ambos campos de CURP o dejarlos vacíos', 'danger');
                        return;
                    }
                    // Si no coinciden, prevenir envío
                    else if (curp !== curpConfirm) {
                        e.preventDefault();
                        showAlert('Las CURP no coinciden', 'danger');
                        return;
                    }
                    // Si la CURP no tiene 18 caracteres, prevenir envío
                    else if (curp.length !== 18) {
                        e.preventDefault();
                        showAlert('La CURP debe tener exactamente 18 caracteres', 'danger');
                        return;
                    }
                    
                    // Si pasa todas las validaciones, continuar con el envío AJAX
                    console.log('Enviando formulario de creación (AJAX)');
                    
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
                        .then(handleJsonResponse)
                        .then(result => {
                            if (result.ok) {
                                const modalEl = document.getElementById('createBeneficiarioModal');
                                if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).hide();

                                const msg = result.data && (result.data.message || (result.data.data && result.data.data.message)) ? (result.data.message || result.data.data.message) : 'Beneficiario creado correctamente.';
                                showAlert(msg, 'success');

                                setTimeout(() => window.location.reload(), 900);
                            } else {
                                showAlert(result.messages || ['Error al crear beneficiario'], 'danger');
                            }
                        })
                        .catch(err => {
                            console.error('Error al crear beneficiario:', err);
                            showAlert('Error al crear el beneficiario', 'danger');
                        });
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
                        .then(handleJsonResponse)
                        .then(result => {
                            if (result.ok) {
                                const modalEl = document.getElementById('editBeneficiarioModal');
                                if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).hide();

                                const msg = result.data && result.data.message ? result.data.message : 'Beneficiario actualizado correctamente.';
                                showAlert(msg, 'success');

                                // Opcional: recargar para actualizar la tabla
                                setTimeout(() => window.location.reload(), 900);
                            } else {
                                if (result.status === 422) {
                                    showAlert(result.messages, 'danger');
                                } else {
                                    showAlert(result.messages || ['Error al actualizar beneficiario'], 'danger');
                                }
                            }
                        })
                        .catch(err => {
                            console.error('Error al actualizar beneficiario:', err);
                            showAlert('Error al actualizar el beneficiario', 'danger');
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

            console.log('Todos los event listeners configurados correctamente');
        });
    </script>