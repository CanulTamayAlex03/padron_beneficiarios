<script>
    document.addEventListener('DOMContentLoaded', function() {
        let curpCheckTimeout = null;

        /* ---------- Validación de CURP con verificación de existencia ---------- */
        window.validarCurp = function() {
            const curpInput = document.getElementById('create_curp');
            const curpError = document.getElementById('curp-error');
            const curpStatus = document.getElementById('curp-status');
            const curpConfirmInput = document.getElementById('create_curp_confirm');
            const curp = curpInput.value.trim().toUpperCase();

            // Actualizar el input con valor en mayúsculas
            curpInput.value = curp;

            // Limpiar timeout anterior
            if (curpCheckTimeout) {
                clearTimeout(curpCheckTimeout);
            }

            // Si está vacío, es válido (es opcional)
            if (curp === '') {
                curpError.classList.add('d-none');
                curpStatus.classList.add('d-none');
                window.curpValida = true;

                // Deshabilitar campo de confirmación
                if (curpConfirmInput) {
                    curpConfirmInput.disabled = true;
                    curpConfirmInput.value = '';
                }

                // Ocultar alerta de CURP registrada
                document.getElementById('curp-registrada-alert').classList.add('d-none');
                window.actualizarEstadoBoton();
                return;
            }

            // Validar longitud
            if (curp.length !== 18) {
                curpError.classList.remove('d-none');
                curpStatus.classList.add('d-none');
                window.curpValida = false;

                // Habilitar campo de confirmación
                if (curpConfirmInput) {
                    curpConfirmInput.disabled = false;
                }

                // Ocultar alerta de CURP registrada
                document.getElementById('curp-registrada-alert').classList.add('d-none');
                window.actualizarEstadoBoton();
                return;
            } else {
                curpError.classList.add('d-none');
                window.curpValida = true;
            }

            // Habilitar campo de confirmación
            if (curpConfirmInput) {
                curpConfirmInput.disabled = false;
            }

            // Mostrar "verificando..." mientras se consulta
            window.curpCheckInProgress = true;
            curpStatus.innerHTML = '<span class="text-warning"><i class="bi bi-hourglass-split"></i> Verificando...</span>';
            curpStatus.classList.remove('d-none');
            window.actualizarEstadoBoton();

            // Verificar si la CURP ya existe (con debounce)
            curpCheckTimeout = setTimeout(() => {
                verificarCurpExistente(curp);
            }, 800);

            window.actualizarEstadoBoton();
        }

        function verificarCurpExistente(curp) {
            const curpStatus = document.getElementById('curp-status');
            const curpAlert = document.getElementById('curp-registrada-alert');
            const beneficiarioInfo = document.getElementById('beneficiario-existente-info');
            const beneficiarioLink = document.getElementById('beneficiario-existente-link');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch(`{{ route('beneficiarios.check-curp') }}?curp=${encodeURIComponent(curp)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error HTTP: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    window.curpCheckInProgress = false;

                    if (data.exists) {
                        const beneficiario = data.beneficiario;

                        const nombreCompleto = [
                                beneficiario.nombres,
                                beneficiario.primer_apellido,
                                beneficiario.segundo_apellido
                            ]
                            .filter(part => part && part !== 'null' && part !== null)
                            .join(' ')
                            .trim();

                        curpStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Esta CURP ya está registrada</span>';
                        curpStatus.classList.remove('d-none');
                        window.curpValida = false;

                        beneficiarioInfo.textContent = nombreCompleto;
                        beneficiarioLink.href = beneficiario.ruta_edicion;

                        curpAlert.classList.remove('d-none');
                    } else {
                        // CURP disponible
                        curpStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> CURP disponible</span>';
                        curpStatus.classList.remove('d-none');
                        window.curpValida = true;
                        curpAlert.classList.add('d-none');
                    }

                    window.actualizarEstadoBoton();
                })
                .catch(error => {
                    window.curpCheckInProgress = false;
                    console.error('Error al verificar CURP:', error);

                    curpStatus.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Error al verificar</span>';
                    curpStatus.classList.remove('d-none');
                    window.curpValida = true;
                    curpAlert.classList.add('d-none');
                    window.actualizarEstadoBoton();
                });
        }

        // Función para validar que las CURP coincidan (sin copy-paste)
        window.validarConfirmacionCurp = function() {
            const curpInput = document.getElementById('create_curp');
            const curpConfirmInput = document.getElementById('create_curp_confirm');
            const curpConfirmError = document.getElementById('curp-confirm-error');

            const curp = curpInput.value.trim();
            const curpConfirm = curpConfirmInput.value.trim().toUpperCase();

            // Actualizar valor en mayúsculas
            curpConfirmInput.value = curpConfirm;

            // Si ambos están vacíos, es válido
            if (curp === '' && curpConfirm === '') {
                curpConfirmError.classList.add('d-none');
                window.curpCoincide = true;
                window.actualizarEstadoBoton();
                return;
            }

            // Si uno está vacío y el otro no, mostrar error
            if ((curp === '' && curpConfirm !== '') || (curp !== '' && curpConfirm === '')) {
                curpConfirmError.classList.remove('d-none');
                curpConfirmError.innerHTML = '<i class="bi bi-exclamation-circle"></i> Ambos campos de CURP deben estar completos';
                window.curpCoincide = false;
                window.actualizarEstadoBoton();
                return;
            }

            // Verificar coincidencia
            if (curp !== curpConfirm) {
                curpConfirmError.classList.remove('d-none');
                curpConfirmError.innerHTML = '<i class="bi bi-exclamation-circle"></i> Las CURP no coinciden';
                window.curpCoincide = false;
            } else {
                curpConfirmError.classList.add('d-none');
                window.curpCoincide = true;

                // Cuando coinciden, verificar si ya existe
                if (curp.length === 18) {
                    window.validarCurp();
                }
            }

            window.actualizarEstadoBoton();
        }

        // Prevenir copy-paste en el campo de confirmación
        const curpConfirmInput = document.getElementById('create_curp_confirm');
        if (curpConfirmInput) {
            curpConfirmInput.addEventListener('paste', function(e) {
                e.preventDefault();
                window.showAlert('No se permite pegar texto en este campo', 'warning');
            });

            curpConfirmInput.addEventListener('copy', function(e) {
                e.preventDefault();
            });

            curpConfirmInput.addEventListener('cut', function(e) {
                e.preventDefault();
            });
        }

        console.log('Funciones de validación configuradas correctamente');
    });

    document.addEventListener('DOMContentLoaded', function() {
        const fechaNacInput = document.getElementById('create_fecha_nac');
        const edadInput = document.getElementById('create_edad');

        if (fechaNacInput && edadInput) {
            fechaNacInput.addEventListener('change', calcularEdadDesdeInput);
            fechaNacInput.addEventListener('input', calcularEdadDesdeInput);

            setTimeout(() => {
                if (fechaNacInput.value) {
                    calcularEdadDesdeInput();
                }
            }, 100);
        }

        function calcularEdadDesdeInput() {
            const fechaNac = fechaNacInput.value;
            if (!fechaNac) {
                edadInput.value = '';
                return;
            }

            const fecha = new Date(fechaNac);
            const fechaAjustada = new Date(fecha.getTime() + fecha.getTimezoneOffset() * 60000);

            const edad = window.calcularEdad(fechaAjustada);
            edadInput.value = `${edad} años`;
        }
    });

</script>