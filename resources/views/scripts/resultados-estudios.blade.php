<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('resultadosEstudiosModal');
    const modal = new bootstrap.Modal(modalEl);
    let estudiosCompletos = [];
    let paginaActual = 1;

    modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', () => modal.hide());
    });

document.addEventListener('click', function(e) {
    const button = e.target.closest('.view-resultados-btn, .view-resultados-estudio-btn');

    if (!button) {
        return;
    }

    const estudioId = button.getAttribute('data-estudio-id');
    const beneficiarioId = button.getAttribute('data-beneficiario-id');

    if (estudioId) {
        cargarResultadoEstudio(estudioId);
        return;
    }

    if (beneficiarioId) {
        cargarResultadosEstudios(beneficiarioId);
    }
});

function cargarResultadoEstudio(estudioId) {
    const selectModalEl = document.getElementById('selectEstudioModal');

    const abrirResultados = () => {
        fetch(`/estudios/${estudioId}/vista-resultado?index=1`)
            .then(response => {
                if (!response.ok) throw new Error(`Error ${response.status}`);
                return response.text();
            })
            .then(html => {
                document.getElementById('contenidoResultados').innerHTML = html;
                document.getElementById('paginacionEstudios').innerHTML = '';
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar el resultado del estudio');
            });
    };

    if (selectModalEl && selectModalEl.classList.contains('show')) {

        const limpiarY = (callback) => {
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
            callback();
        };

        let yaEjecutado = false;

        const onHidden = () => {
            if (yaEjecutado) return;
            yaEjecutado = true;
            selectModalEl.removeEventListener('hidden.bs.modal', onHidden);
            limpiarY(abrirResultados);
        };

        selectModalEl.addEventListener('hidden.bs.modal', onHidden);

        setTimeout(() => {
            if (yaEjecutado) return;
            yaEjecutado = true;
            selectModalEl.removeEventListener('hidden.bs.modal', onHidden);

            selectModalEl.classList.remove('show');
            selectModalEl.style.display = 'none';
            selectModalEl.setAttribute('aria-hidden', 'true');
            limpiarY(abrirResultados);
        }, 500);

        const selectModal = window.selectEstudioModalInstance 
            || bootstrap.Modal.getInstance(selectModalEl);

        if (selectModal) {
            selectModal.hide();
        } else {
            selectModalEl.classList.remove('show');
            selectModalEl.style.display = 'none';
            selectModalEl.setAttribute('aria-hidden', 'true');
            limpiarY(abrirResultados);
        }

    } else {
        abrirResultados();
    }
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
        
        html += `<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-pagina="${paginaActual - 1}">Anterior</a>
                 </li>`;
        
        for (let i = 1; i <= estudiosCompletos.length; i++) {
            html += `<li class="page-item ${i === paginaActual ? 'active' : ''}">
                        <a class="page-link" href="#" data-pagina="${i}">${i}</a>
                     </li>`;
        }
        
        html += `<li class="page-item ${paginaActual === estudiosCompletos.length ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-pagina="${paginaActual + 1}">Siguiente</a>
                 </li>`;
        
        html += '</ul></nav>';
        paginacion.innerHTML = html;

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