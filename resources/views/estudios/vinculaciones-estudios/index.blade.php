@extends('layouts.app')
@section('title', 'Vinculaciones de Estudios')
@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h2 class="mb-0">Vinculaciones de Estudios</h2>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>¡Éxito!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error:</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="folio" class="form-label">Folio de estudio</label>
                            <input type="text" class="form-control" id="folio" name="folio" 
                                   value="{{ request('folio') }}" placeholder="Buscar por folio...">
                        </div>
                        <div class="col-md-4">
                            <label for="beneficiario_vinculado" class="form-label">Beneficiario vinculado</label>
                            <input type="text" class="form-control" id="beneficiario_vinculado" name="beneficiario_vinculado"
                                   value="{{ request('beneficiario_vinculado') }}" placeholder="Buscar beneficiario...">
                        </div>
                        <div class="col-md-4">
                            <label for="beneficiario_principal" class="form-label">Beneficiario principal</label>
                            <input type="text" class="form-control" id="beneficiario_principal" name="beneficiario_principal"
                                   value="{{ request('beneficiario_principal') }}" placeholder="Buscar beneficiario principal...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                            @if(request()->hasAny(['folio', 'beneficiario_vinculado', 'beneficiario_principal']))
                            <div class="alert mb-2">
                                <strong>Filtros activos:</strong>
                                @if(request('folio'))
                                    <span class="badge bg-secondary">Folio: {{ request('folio') }}</span>
                                @endif
                                @if(request('beneficiario_vinculado'))
                                    <span class="badge bg-secondary">Vinculado: {{ request('beneficiario_vinculado') }}</span>
                                @endif
                                @if(request('beneficiario_principal'))
                                    <span class="badge bg-secondary">Principal: {{ request('beneficiario_principal') }}</span>
                                @endif
                                <a href="{{ route('vinculaciones-estudios.index') }}" class="btn btn-sm btn-outline-danger ms-2">
                                    <i class="bi bi-x-circle"></i> Limpiar
                                </a>
                            </div>
                            @endif
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Estudio (Folio)</th>
                                    <th>Programa</th>
                                    <th>Beneficiario Principal</th>
                                    <th>Beneficiario Vinculado</th>
                                    <th>Fecha Vinculación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vinculaciones as $vinculacion)
                                <tr>
                                    <td>{{ $vinculacion->id }}</td>
                                    <td>
                                        <strong>{{ $vinculacion->estudio->folio ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        {{ $vinculacion->estudio->programa->nombre_programa ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $vinculacion->beneficiarioPrincipal->nombres ?? 'N/A' }} 
                                        {{ $vinculacion->beneficiarioPrincipal->primer_apellido ?? '' }}
                                    </td>
                                    <td>
                                        {{ $vinculacion->beneficiarioVinculado->nombres ?? 'N/A' }} 
                                        {{ $vinculacion->beneficiarioVinculado->primer_apellido ?? '' }}
                                    </td>
                                    <td>{{ $vinculacion->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @can('eliminar vinculaciones estudios')
                                        <button class="btn btn-danger btn-sm delete-vinculacion-btn"
                                                data-id="{{ $vinculacion->id }}"
                                                data-estudio="{{ $vinculacion->estudio->folio ?? 'N/A' }}"
                                                data-vinculado="{{ $vinculacion->beneficiarioVinculado->nombres ?? '' }} {{ $vinculacion->beneficiarioVinculado->primer_apellido ?? '' }}"
                                                title="Eliminar vinculación">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No hay vinculaciones registradas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                 <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Mostrando {{ $vinculaciones->firstItem() ?? 0 }} - {{ $vinculaciones->lastItem() ?? 0 }} 
                            de {{ $vinculaciones->total() }} registros
                        </div>
                        <nav aria-label="Navegación de vinculaciones">
                            <ul class="pagination mb-0">
                                {{-- Primer página --}}
                                <li class="page-item {{ $vinculaciones->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $vinculaciones->url(1) }}" aria-label="Primera">
                                        <span aria-hidden="true">&laquo;&laquo;</span>
                                    </a>
                                </li>
                                {{-- Página anterior --}}
                                <li class="page-item {{ $vinculaciones->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $vinculaciones->previousPageUrl() }}" aria-label="Anterior">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                {{-- Páginas --}}
                                @php
                                $current = $vinculaciones->currentPage();
                                $last = $vinculaciones->lastPage();
                                $start = max($current - 2, 1);
                                $end = min($start + 4, $last);
                                if ($end - $start < 4) {
                                    $start = max($end - 4, 1);
                                }
                                @endphp
                            
                                @for ($i = $start; $i <= $end; $i++)
                                    <li class="page-item {{ $i == $current ? 'active' : '' }}">
                                        <a class="page-link" href="{{ $vinculaciones->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor
                            
                                {{-- Página siguiente --}}
                                <li class="page-item {{ !$vinculaciones->hasMorePages() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $vinculaciones->nextPageUrl() }}" aria-label="Siguiente">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            
                                {{-- Última página --}}
                                <li class="page-item {{ !$vinculaciones->hasMorePages() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $vinculaciones->url($last) }}" aria-label="Última">
                                        <span aria-hidden="true">&raquo;&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                            
                        <div class="d-none d-md-block">
                            <span class="badge bg-dark">Página {{ $vinculaciones->currentPage() }} de {{ $vinculaciones->lastPage() }}</span>
                        </div>
                    </div>                      
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteVinculacionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de eliminar la vinculación del estudio 
                   <strong id="estudio-folio"></strong> con 
                   <strong id="beneficiario-vinculado"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteVinculacionForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-vinculacion-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const vinculacionId = this.getAttribute('data-id');
            const estudioFolio = this.getAttribute('data-estudio');
            const beneficiarioVinculado = this.getAttribute('data-vinculado');

            document.getElementById('estudio-folio').textContent = estudioFolio;
            document.getElementById('beneficiario-vinculado').textContent = beneficiarioVinculado;
            document.getElementById('deleteVinculacionForm').action = `/vinculaciones-estudios/${vinculacionId}`;

            const modal = new bootstrap.Modal(document.getElementById('deleteVinculacionModal'));
            modal.show();
        });
    });

    document.getElementById('deleteVinculacionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const url = form.action;
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteVinculacionModal'));

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                _method: 'DELETE'
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                modal.hide();
                
                showAlert('success', data.message);
                
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Error al eliminar la vinculación');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modal.hide();
            showAlert('danger', error.message || 'Error al eliminar la vinculación');
        });
    });

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <strong>${type === 'success' ? '¡Éxito!' : 'Error:'}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(alertDiv, cardBody.firstChild);
        
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 5000);
    }

    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endsection