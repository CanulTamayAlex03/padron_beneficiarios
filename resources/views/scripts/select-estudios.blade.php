<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('selectEstudioModal');
        const modal = new bootstrap.Modal(modalEl);

        // Cerrar modal correctamente
        modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', () => modal.hide());
        });

        // Abrir modal al hacer clic en botones
        document.querySelectorAll('.ver-estudios-existente-btn').forEach(button => {
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
            <td colspan="5" class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                Cargando estudios...
            </td>
        </tr>
    `;

    fetch(`/api/beneficiarios/${beneficiarioId}/estudios`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            // Verificar si la respuesta tiene la estructura esperada
            if (data && data.estudios) {
                mostrarEstudios(data.estudios);
            } else if (data && data.error) {
                // Mostrar error del servidor
                mostrarError(`Error del servidor: ${data.error}`);
            } else {
                // Estructura inesperada
                mostrarError('Respuesta inesperada del servidor');
            }
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error al cargar los estudios: ' + error.message);
            modal.show();
        });
}

        function mostrarEstudios(estudios) {
    const tbody = document.querySelector('#estudios-table tbody');
    
    // VERIFICAR QUE estudios ES UN ARRAY
    if (!Array.isArray(estudios)) {
        console.error('estudios no es un array:', estudios);
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error: Formato de datos incorrecto</td></tr>';
        return;
    }
    
    const estudiosPropios = estudios.filter(e => e.tipo === 'propio').length;
    const estudiosVinculados = estudios.filter(e => e.tipo === 'vinculado').length;
    
    let totalText = estudios.length;
    if (estudiosPropios > 0 && estudiosVinculados > 0) {
        totalText = `${estudios.length} (${estudiosPropios} propios + ${estudiosVinculados} vinculados)`;
    } else if (estudiosVinculados > 0) {
        totalText = `${estudios.length} vinculados`;
    }
    
    document.getElementById('total-estudios').textContent = totalText;

    if (estudios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay estudios registrados</td></tr>';
        return;
    }

    tbody.innerHTML = '';

    estudios.forEach(estudio => {
        const fechaCreacion = new Date(estudio.created_at).toLocaleDateString('es-MX');
        const estadoEstudio = obtenerEstadoEstudio(estudio);
        
        let botonTexto, botonClase, botonIcono;
        if (estudio.tipo === 'propio') {
            botonTexto = 'Ir al estudio';
            botonClase = 'btn-primary';
            botonIcono = 'bi-box-arrow-in-right';
        } else {
            botonTexto = 'Ir al estudio';
            botonClase = 'btn-info';
            botonIcono = 'bi-share';
        }

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <strong>${estudio.folio || 'N/A'}</strong>
                ${estudio.tipo === 'vinculado' ? '<br><small class="text-muted">🔗 Vinculado</small>' : ''}
            </td>
            <td>${fechaCreacion}</td>
            <td>
                <span class="badge ${estadoEstudio.clase}">${estadoEstudio.texto}</span>
                ${estudio.tipo === 'vinculado' && estudio.beneficiario_principal_nombre ? 
                    `<br><small class="text-muted">De: ${estudio.beneficiario_principal_nombre}</small>` : 
                    ''}
            </td>
            <td>
                ${estudio.tipo === 'vinculado' ? 
                    `<small class="text-info">Vinculado</small>` : 
                    `<small class="text-success">Propio</small>`}
            </td>
            <td>
                @can('editar beneficiarios')
                    <button class="btn btn-sm ${botonClase} ir-estudio-btn" 
                            data-ruta="${estudio.ruta_edicion || '#'}"
                            data-tipo="${estudio.tipo}">
                        <i class="bi ${botonIcono}"></i> ${botonTexto}
                    </button>
                @endcan
            </td>
        `;
        tbody.appendChild(tr);
    });

    document.querySelectorAll('.ir-estudio-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const ruta = this.getAttribute('data-ruta');
            const tipo = this.getAttribute('data-tipo');
            
            if (ruta && ruta !== '#') {
                modal.hide();
                setTimeout(() => {
                    window.location.href = ruta;
                }, 300);
            } else {
                alert('No se puede acceder a este estudio');
            }
        });
    });
}

function mostrarError(mensaje) {
    const tbody = document.querySelector('#estudios-table tbody');
    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center text-danger">
                <i class="bi bi-exclamation-triangle"></i><br>
                ${mensaje}
            </td>
        </tr>
    `;
}

function obtenerEstadoEstudio(estudio) {
    if (!estudio) {
        return {
            texto: 'Desconocido',
            clase: 'bg-secondary'
        };
    }

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