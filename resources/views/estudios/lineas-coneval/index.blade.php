@extends('layouts.app')

@section('title', 'Líneas CONEVAL')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>Líneas CONEVAL
                    </h5>
                    @can('crear lineas coneval')
                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createLineaModal">
                        <i class="bi bi-plus-circle me-1"></i>Nueva Línea
                    </button>
                    @endcan
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Tabla agrupada por período -->
                    @if($lineasAgrupadas->count() > 0)
                        @foreach($lineasAgrupadas as $periodo => $lineasDelPeriodo)
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-dark">
                                    <i class="bi bi-calendar me-2"></i>
                                    <strong>Período: {{ \Carbon\Carbon::parse($periodo)->format('F Y') }}</strong>
                                    @if($lineasDelPeriodo->first()->descripcion)
                                        <small class="text-muted ms-2">- {{ $lineasDelPeriodo->first()->descripcion }}</small>
                                    @endif
                                </h6>
                                <div>
                                    @php
                                        $activoCount = $lineasDelPeriodo->where('activo', true)->count();
                                    @endphp
                                    @if($activoCount > 0)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-pause-circle me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Zona</th>
                                                <th>Descripción</th>
                                                <th class="text-end">Cantidad</th>
                                                <th>Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lineasDelPeriodo as $linea)
                                            <tr>
                                                <td>
                                                    <span class="text-dark">
                                                        <strong>{{ $linea->zona }}</strong>
                                                    </span>
                                                </td>
                                                <td>{{ $linea->descripcion ?? '-' }}</td>
                                                <td class="text-end fw-bold">${{ number_format($linea->cantidad, 2) }}</td>
                                                <td>
                                                    @if($linea->activo)
                                                        <span class="badge bg-success">Activa</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactiva</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @can('editar lineas coneval')
                                                    <button type="button" class="btn btn-warning btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editLineaModal{{ $linea->id }}"
                                                            title="Editar">
                                                        <i class="bi bi-pencil"></i> Editar
                                                    </button>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="text-muted mt-3">No hay líneas CONEVAL registradas</h4>
                        <p class="text-muted">Comienza agregando la primera línea CONEVAL.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Líneas CONEVAL (las 3 zonas) -->
@can ('crear lineas coneval')
<div class="modal fade" id="createLineaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Nuevo Conjunto de Líneas CONEVAL</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('lineas-coneval.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Periodo <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="periodo" id="periodo" required>
                        <small class="text-muted">Primer día del mes que aplica este conjunto</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Descripción del conjunto (opcional)</label>
                        <textarea class="form-control" name="descripcion" rows="2" placeholder="Ej: Valores para el primer trimestre 2025"></textarea>
                    </div>

                    <!-- Cantidades por zona -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Cantidades por Zona ($) <span class="text-danger">*</span></label>
                        
                        <div class="row">
                            <!-- Rural -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Rural</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" 
                                           name="cantidades[Rural]" placeholder="0.00" required>
                                </div>
                            </div>
                            <!-- Urbana -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Urbano</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" 
                                           name="cantidades[Urbana]" placeholder="0.00" required>
                                </div>
                            </div>

                            <!-- Semiurbano -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Semiurbano</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" 
                                           name="cantidades[Semiurbano]" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" checked>
                        <label class="form-check-label fw-bold">Activar este conjunto completo</label>
                        <small class="form-text text-muted d-block">
                            ⚡ Al activar, se desactivarán automáticamente otros conjuntos del mismo período.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i> Guardar las 3 Líneas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<!-- Modales de Edición -->
@can ('editar lineas coneval')
@foreach($lineas as $linea)
<div class="modal fade" id="editLineaModal{{ $linea->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Editar Línea - {{ $linea->zona }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('lineas-coneval.update', $linea) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Zona</label>
                        <select class="form-select" name="zona" required>
                            <option value="Rural" {{ $linea->zona == 'Rural' ? 'selected' : '' }}>Rural</option>
                            <option value="Semiurbano" {{ $linea->zona == 'Semiurbano' ? 'selected' : '' }}>Semiurbano</option>
                            <option value="Urbana" {{ $linea->zona == 'Urbana' ? 'selected' : '' }}>Urbana</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Periodo</label>
                        <input type="date" class="form-control" name="periodo" value="{{ $linea->periodo->format('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cantidad ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="cantidad" value="{{ $linea->cantidad }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="2">{{ $linea->descripcion }}</textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" {{ $linea->activo ? 'checked' : '' }}>
                        <label class="form-check-label">Línea activa</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endcan

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    document.getElementById('periodo').value = firstDay.toISOString().split('T')[0];
});
</script>
@endpush