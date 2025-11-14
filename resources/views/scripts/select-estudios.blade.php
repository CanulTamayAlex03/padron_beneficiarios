<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('selectEstudioModal');
        const modal = new bootstrap.Modal(modalEl);

        // Cerrar modal correctamente
        modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', () => modal.hide());
        });

        // Abrir modal al hacer clic en botones
        document.querySelectorAll('.view-estudios-btn').forEach(button => {
            button.addEventListener('click', function() {
                const beneficiarioId = this.getAttribute('data-beneficiario-id');
                const beneficiarioNombre = this.getAttribute('data-beneficiario-nombre');
                cargarEstudiosBeneficiario(beneficiarioId, beneficiarioNombre);
            });
        });

        function cargarEstudiosBeneficiario(beneficiarioId, beneficiarioNombre) {
            document.getElementById('beneficiario-nombre').textContent = beneficiarioNombre;
            const tbody = document.querySelector('#estudios-table tbody');

            // Limpiar tabla
            tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    Cargando estudios...
                </td>
            </tr>
        `;

            fetch(`/api/beneficiarios/${beneficiarioId}/estudios`)
                .then(response => response.json())
                .then(data => {
                    mostrarEstudios(data.estudios);
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-danger">
                            Error al cargar los estudios
                        </td>
                    </tr>
                `;
                });
        }

        function mostrarEstudios(estudios) {
            const tbody = document.querySelector('#estudios-table tbody');
            document.getElementById('total-estudios').textContent = estudios.length;

            if (estudios.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay estudios registrados</td></tr>';
                return;
            }

            tbody.innerHTML = '';

            estudios.forEach(estudio => {
                const fechaCreacion = new Date(estudio.created_at).toLocaleDateString('es-MX');
                const estadoEstudio = obtenerEstadoEstudio(estudio);

                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td><strong>${estudio.folio || 'N/A'}</strong></td>
                <td>${fechaCreacion}</td>
                <td><span class="badge ${estadoEstudio.clase}">${estadoEstudio.texto}</span></td>
                <td>
                @can('editar beneficiarios')
                    <button class="btn btn-sm btn-primary ir-estudio-btn" data-ruta="${estudio.ruta_edicion}">
                        <i class="bi bi-box-arrow-in-right"></i> Ir al estudio
                    </button>
                @endcan
                </td>
            `;
                tbody.appendChild(tr);
            });

            // Acciones de botones
            document.querySelectorAll('.ir-estudio-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const ruta = this.getAttribute('data-ruta');
                    modal.hide();
                    setTimeout(() => {
                        window.location.href = ruta;
                    }, 300);
                });
            });
        }

        function obtenerEstadoEstudio(estudio) {
            const v1 = (estudio.res_estudio_1 || '').toLowerCase();
            const v2 = (estudio.res_estudio_2 || '').toLowerCase();
            const v3 = (estudio.res_estudio_3 || '').toLowerCase();

            if (!v1 || !v2 || !v3 || v1 === 'no aplica' || v2 === 'no aplica' || v3 === 'no aplica') {
                return {
                    texto: 'Incompleto',
                    clase: 'bg-secondary'
                };
            }
            if (v1 && v2 && v3) {
                return {
                    texto: 'Completo',
                    clase: 'bg-success'
                };
            }
            return {
                texto: 'En progreso',
                clase: 'bg-warning'
            };
        }

    });
</script>