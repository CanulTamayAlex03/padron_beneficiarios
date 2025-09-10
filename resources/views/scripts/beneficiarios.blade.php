<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '/beneficiarios';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('Script de beneficiarios cargado');

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
                document.getElementById('view-id').textContent = d.id ?? '';
                document.getElementById('view-nombres').textContent = d.nombres ?? '';
                document.getElementById('view-apellidos').textContent = d.apellidos ?? '';
                document.getElementById('view-curp').textContent = d.curp ?? '';
                document.getElementById('view-created').textContent = d.created_at ? new Date(d.created_at).toLocaleString() : '';
                document.getElementById('view-updated').textContent = d.updated_at ? new Date(d.updated_at).toLocaleString() : '';

                const viewModal = new bootstrap.Modal(document.getElementById('viewBeneficiarioModal'));
                viewModal.show();
            })
            .catch(err => {
                console.error('Error al cargar detalles:', err);
                showAlert('Error al cargar los detalles', 'danger');
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
                if (document.getElementById('edit_apellidos')) document.getElementById('edit_apellidos').value = d.apellidos ?? '';
                if (document.getElementById('edit_curp')) document.getElementById('edit_curp').value = d.curp ?? '';

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
                    method: 'POST', // Laravel simula DELETE con _method
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
            e.preventDefault();
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

                        // si el servidor devolvió message en data
                        const msg = result.data && (result.data.message || (result.data.data && result.data.data.message)) ? (result.data.message || result.data.data.message) : 'Beneficiario creado correctamente.';
                        showAlert(msg, 'success');

                        // recargar para actualizar tabla y sacar la alert desde el servidor si prefieres servidor->session
                        // Opcional: si prefieres no recargar, comenta la siguiente línea
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
                    method: 'POST', // Laravel simula PUT con _method
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