<script>
    document.addEventListener('DOMContentLoaded', function() {
        let curpCheckTimeout = null;

        /* ---------- Validación de CURP con verificación de existencia ---------- */
        window.validarCurp = function() {
            const curpInput = document.getElementById('create_curp');
            const curpError = document.getElementById('curp-error');
            const curpStatus = document.getElementById('curp-status');
            const curp = curpInput.value.trim();

            // Limpiar timeout anterior
            if (curpCheckTimeout) {
                clearTimeout(curpCheckTimeout);
            }

            // Si está vacío, es válido (es opcional)
            if (curp === '') {
                curpError.classList.add('d-none');
                curpStatus.classList.add('d-none');
                window.curpValida = true;
                window.actualizarEstadoBoton();
                return;
            }

            // Validar longitud
            if (curp.length !== 18) {
                curpError.classList.remove('d-none');
                curpStatus.classList.add('d-none');
                window.curpValida = false;
                window.actualizarEstadoBoton();
                return;
            } else {
                curpError.classList.add('d-none');
                window.curpValida = true;
            }

            // Mostrar "verificando..." mientras se consulta
            curpStatus.innerHTML = '<span class="text-warning"><i class="bi bi-hourglass-split"></i> Verificando...</span>';
            curpStatus.classList.remove('d-none');

            // Verificar si la CURP ya existe (con debounce)
            curpCheckTimeout = setTimeout(() => {
                verificarCurpExistente(curp);
            }, 800); // 800ms de delay

            // Validar coincidencia si el campo de confirmación no está vacío
            const curpConfirm = document.getElementById('create_curp_confirm').value.trim();
            if (curpConfirm !== '') {
                window.validarConfirmacionCurp();
            }

            window.actualizarEstadoBoton();
        }

        // Función para verificar si la CURP ya existe
        function verificarCurpExistente(curp) {
            const curpStatus = document.getElementById('curp-status');
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
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.exists) {
                        curpStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle"></i> Esta CURP ya está registrada</span>';
                        curpStatus.classList.remove('d-none');
                        window.curpValida = false;
                    } else {
                        curpStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> CURP disponible</span>';
                        curpStatus.classList.remove('d-none');
                        window.curpValida = true;
                    }
                    window.actualizarEstadoBoton();
                })
                .catch(error => {
                    console.error('Error al verificar CURP:', error);
                    curpStatus.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Error al verificar</span>';
                    curpStatus.classList.remove('d-none');
                    window.curpValida = true; // Permitir enviar aunque falle la verificación
                    window.actualizarEstadoBoton();
                });
        }

        // Función para validar que las CURP coincidan (sin copy-paste)
        window.validarConfirmacionCurp = function() {
            const curpInput = document.getElementById('create_curp');
            const curpConfirmInput = document.getElementById('create_curp_confirm');
            const curpConfirmError = document.getElementById('curp-confirm-error');

            const curp = curpInput.value.trim();
            const curpConfirm = curpConfirmInput.value.trim();

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

    document.addEventListener('DOMContentLoaded', function() {
        const estadoVivSelect = document.getElementById('create_estado_viv_id');
        const municipioSelect = document.getElementById('create_municipio_id');
        const municipioOptions = Array.from(municipioSelect.options);

        function filterMunicipiosByEstado(estadoId) {
            // Mostrar todos los municipios si no hay estado seleccionado
            if (!estadoId) {
                municipioOptions.forEach(option => {
                    option.style.display = '';
                });
                municipioSelect.value = '';
                return;
            }

            // Filtrar municipios por estado
            municipioOptions.forEach(option => {
                if (option.value === '' || option.getAttribute('data-estado') === estadoId) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });

            // Resetear selección si el municipio actual no pertenece al estado
            const selectedOption = municipioSelect.options[municipioSelect.selectedIndex];
            if (selectedOption && selectedOption.getAttribute('data-estado') !== estadoId && selectedOption.value !== '') {
                municipioSelect.value = '';
            }
        }

        // Event listener para cambio de estado
        estadoVivSelect.addEventListener('change', function() {
            filterMunicipiosByEstado(this.value);
        });

        // Inicializar filtro al cargar la página
        filterMunicipiosByEstado(estadoVivSelect.value);
    });


    document.addEventListener('DOMContentLoaded', function() {
        const editEstadoVivSelect = document.getElementById('edit_estado_viv_id');
        const editMunicipioSelect = document.getElementById('edit_municipio_id');

        if (editEstadoVivSelect && editMunicipioSelect) {
            editEstadoVivSelect.addEventListener('change', function() {
                filterEditMunicipiosByEstado(this.value);
            });
        }

        window.filterEditMunicipiosByEstado = function(estadoId) {
            if (!editMunicipioSelect) return;

            const editMunicipioOptions = Array.from(editMunicipioSelect.options);

            if (!estadoId) {
                editMunicipioOptions.forEach(option => {
                    option.style.display = '';
                });
                editMunicipioSelect.value = '';
                return;
            }

            editMunicipioOptions.forEach(option => {
                if (option.value === '' || option.getAttribute('data-estado') === estadoId) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });

            const selectedOption = editMunicipioSelect.options[editMunicipioSelect.selectedIndex];
            if (selectedOption && selectedOption.getAttribute('data-estado') !== estadoId && selectedOption.value !== '') {
                editMunicipioSelect.value = '';
            }
        };
    });
</script>