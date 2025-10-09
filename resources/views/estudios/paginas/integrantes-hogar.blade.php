<div class="card mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-house-door-fill me-2"></i> Integrantes del Hogar
        </h5>
        @if(isset($estudio) && $estudio->id)
        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createIntegranteModal">
            <i class="bi bi-person-plus"></i> Agregar Integrante
        </button>
        @else
        <div class="alert alert-warning py-1 mb-0">
            <small><i class="bi bi-info-circle"></i> Guarde el estudio primero para agregar integrantes</small>
        </div>
        @endif
    </div>

    <div class="card-body">
        @if(isset($estudio) && $estudio->id && $estudio->integrantesHogar->count())
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre completo</th>
                        <th>Edad</th>
                        <th>Parentesco</th>
                        <th>Ingreso mensual</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($estudio->integrantesHogar as $integrante)
                    <tr>
                        <td>{{ $integrante->nombres }} {{ $integrante->apellidos }}</td>
                        <td>{{ $integrante->edad }} años</td>
                        <td>{{ $integrante->parentesco }}</td>
                        <td>${{ number_format($integrante->ingreso_mensual, 2) }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-warning"
                                data-bs-toggle="modal" data-bs-target="#editIntegranteModal{{ $integrante->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteIntegranteModal{{ $integrante->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted">
            @if(isset($estudio) && $estudio->id)
            No hay integrantes del hogar registrados.
            @else
            El estudio debe ser guardado primero para gestionar integrantes del hogar.
            @endif
        </p>
        @endif
    </div>
</div>

@include('estudios.integrantes-hogar-modals.create-modal')
@include('estudios.integrantes-hogar-modals.edit-modal')    
@include('estudios.integrantes-hogar-modals.delete-modal')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createForm = document.getElementById('createIntegranteForm');

        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Guardando...';
                submitBtn.disabled = true;

                fetch('{{ route("integrantes-hogar.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('createIntegranteModal'));
                            modal.hide();

                            location.reload();
                        } else {
                            alert(data.error || 'Error al guardar');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al guardar el integrante');
                    })
                    .finally(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });
        }
    });

    // Editar integrante
    document.querySelectorAll('.edit-integrantes-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const integranteId = this.getAttribute('data-id');
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Actualizando...';
            submitBtn.disabled = true;

            fetch(`/integrantes-hogar/${integranteId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(`editIntegranteModal${integranteId}`));
                        modal.hide();
                        location.reload();
                    } else {
                        alert(data.error || 'Error al actualizar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al actualizar el integrante');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    });

    // Eliminar integrante
    document.querySelectorAll('.delete-integrantes-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const integranteId = this.getAttribute('data-id');
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Eliminando...';
            submitBtn.disabled = true;

            fetch(`/integrantes-hogar/${integranteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(`deleteIntegranteModal${integranteId}`));
                        modal.hide();
                        location.reload();
                    } else {
                        alert(data.error || 'Error al eliminar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el integrante');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    });
</script>