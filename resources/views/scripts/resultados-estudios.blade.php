<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('resultadosEstudiosModal');
    const modal = new bootstrap.Modal(modalEl);
    let estudiosCompletos = [];
    let paginaActual = 1;

    // Cerrar modal con cualquier botón que tenga data-bs-dismiss
    modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', () => modal.hide());
    });

    document.querySelectorAll('.view-resultados-btn').forEach(button => {
        button.addEventListener('click', function() {
            const beneficiarioId = this.getAttribute('data-beneficiario-id');
            cargarResultadosEstudios(beneficiarioId);
        });
    });

    function cargarResultadosEstudios(beneficiarioId) {
        fetch(`/beneficiarios/${beneficiarioId}/estudios-completos`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    estudiosCompletos = data.estudios;
                    paginaActual = 1;
                    mostrarEstudio(paginaActual);
                    configurarPaginacion();
                    modal.show();
                } else {
                    alert('Error al cargar los estudios: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los estudios');
            });
    }

    function mostrarEstudio(pagina) {
        const contenido = document.getElementById('contenidoResultados');
        const indice = pagina - 1;
        
        if (estudiosCompletos[indice]) {
            const estudio = estudiosCompletos[indice];
            fetch(`/estudios/${estudio.id}/vista-resultado?index=${pagina}`)
                .then(response => response.text())
                .then(html => {
                    contenido.innerHTML = html;
                    actualizarTituloModal(estudio, pagina);
                });
        }
    }

    function actualizarTituloModal(estudio, pagina) {
        const titulo = document.getElementById('resultadosEstudiosModalLabel');
        titulo.innerHTML = `<i class="bi bi-clipboard-data me-2"></i>Resultados - ${estudio.beneficiario.nombres} ${estudio.beneficiario.primer_apellido} (Estudio ${pagina} de ${estudiosCompletos.length})`;
    }

    function configurarPaginacion() {
        const paginacion = document.getElementById('paginacionEstudios');
        
        if (estudiosCompletos.length <= 1) {
            paginacion.innerHTML = '';
            return;
        }

        let html = '<nav aria-label="Navegación de estudios"><ul class="pagination pagination-sm mb-0">';
        
        // Botón anterior
        html += `<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-pagina="${paginaActual - 1}">Anterior</a>
                 </li>`;
        
        // Números de página
        for (let i = 1; i <= estudiosCompletos.length; i++) {
            html += `<li class="page-item ${i === paginaActual ? 'active' : ''}">
                        <a class="page-link" href="#" data-pagina="${i}">${i}</a>
                     </li>`;
        }
        
        // Botón siguiente
        html += `<li class="page-item ${paginaActual === estudiosCompletos.length ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-pagina="${paginaActual + 1}">Siguiente</a>
                 </li>`;
        
        html += '</ul></nav>';
        paginacion.innerHTML = html;

        // Event listeners para paginación
        paginacion.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const nuevaPagina = parseInt(this.getAttribute('data-pagina'));
                if (nuevaPagina >= 1 && nuevaPagina <= estudiosCompletos.length) {
                    paginaActual = nuevaPagina;
                    mostrarEstudio(paginaActual);
                    configurarPaginacion();
                }
            });
        });
    }
});
</script>