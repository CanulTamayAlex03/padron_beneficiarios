<script>
    document.addEventListener('DOMContentLoaded', function() {
        const baseUrl = '/beneficiarios';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 'test-token';
        console.log('Script de beneficiarios cargado');

        // Variables globales
        window.curpValida = true;
        window.curpCoincide = true;

        /* ---------- Inicializar tooltips (Bootstrap 5) ---------- */
        (function initTooltips() {
            const triggers = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            triggers.forEach(el => new bootstrap.Tooltip(el));
        })();

        /* ---------- FUNCION de manejo de respuestas JSON ---------- */
        window.handleJsonResponse = async function(response) {
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

        /* ---------- Función para mostrar alertas ---------- */
        window.showAlert = function(message, type = 'success', prependTo = '.card .card-body') {
            const container = document.querySelector(prependTo) || document.querySelector('body');
            const wrapper = document.createElement('div');

            const msgHtml = Array.isArray(message) ? message.join('<br>') : message;

            wrapper.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${type === 'success' ? '<strong>¡Éxito!</strong> ' : '<strong>Error:</strong> '}
                    <span class="ms-1">${msgHtml}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            container.prepend(wrapper.firstElementChild);

            setTimeout(() => {
                const alertEl = container.querySelector('.alert');
                if (alertEl) {
                    bootstrap.Alert.getOrCreateInstance(alertEl).close();
                }
            }, 5000);
        }

        /* ---------- Función para actualizar el estado del botón de envío ---------- */
        window.actualizarEstadoBoton = function() {
            const submitBtn = document.getElementById('submit-btn');

            if (!window.curpValida || !window.curpCoincide) {
                submitBtn.disabled = true;
            } else {
                submitBtn.disabled = false;
            }
        }

        /* ---------- Función para calcular edad ---------- */
        window.calcularEdad = function(fechaNacimiento) {
            const hoy = new Date();
            const nacimiento = new Date(fechaNacimiento);

            let edad = hoy.getFullYear() - nacimiento.getFullYear();
            const mes = hoy.getMonth() - nacimiento.getMonth();

            if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
                edad--;
            }

            return edad;
        }

        // Limpiar validación al cerrar el modal
        const createModal = document.getElementById('createBeneficiarioModal');
        if (createModal) {
            createModal.addEventListener('hidden.bs.modal', function() {
                // Limpiar validación de CURP
                document.getElementById('curp-error').classList.add('d-none');
                document.getElementById('curp-confirm-error').classList.add('d-none');
                window.curpValida = true;
                window.curpCoincide = true;

                // Limpiar errores de campos
                const errorElements = document.querySelectorAll('#createBeneficiarioModal .field-error');
                errorElements.forEach(el => el.remove());

                // Remover clases de error
                const invalidFields = document.querySelectorAll('#createBeneficiarioModal .is-invalid');
                invalidFields.forEach(field => field.classList.remove('is-invalid'));

                // Limpiar mensaje de error general
                const generalError = document.getElementById('modal-general-error');
                if (generalError) {
                    generalError.classList.add('d-none');
                    generalError.innerHTML = '';
                }

                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn) submitBtn.disabled = false;
            });
        }

        console.log('Funciones principales configuradas correctamente');
    });
</script>