<!-- Sección de acompañantes -->
<div class="card mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-people-fill me-2"></i> Acompañantes
        </h5>
        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createFamiliarModal">
            <i class="bi bi-person-plus"></i> Agregar acompañante
        </button>
    </div>

    <div class="card-body">
        @if($beneficiario->familiares->count())
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre completo</th>
                        <th>CURP</th>
                        <th>Teléfono</th>
                        <th>Parentesco</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($beneficiario->familiares as $familiar)
                    <tr>
                        <td>{{ $familiar->nombres }} {{ $familiar->primer_apellido }} {{ $familiar->segundo_apellido }}</td>
                        <td>{{ $familiar->curp }}</td>
                        <td>{{ $familiar->telefono }}</td>
                        <td>{{ $familiar->relacion_parentezco }}</td>
                        <td class="text-center">
                            <!-- Botón editar -->
                            <button type="button" class="btn btn-sm btn-warning"
                                data-bs-toggle="modal" data-bs-target="#editFamiliarModal{{ $familiar->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <!-- Botón eliminar -->
                            <button type="button" class="btn btn-sm btn-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteFamiliarModal{{ $familiar->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted">No hay acompañantes registrados.</p>
        @endif
    </div>
</div>