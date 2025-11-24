@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Importar Beneficiarios</h2>
                        @can('exportar beneficiarios')
                        <a href="{{ route('administrador.exportar_beneficiarios') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-upload"></i> Exportar Beneficiarios
                        </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <!-- Mensajes de alerta -->
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

                    <!-- Formulario de importación -->
                    <div class="alert alert-info">
                        <h5 class="alert-heading">📋 Instrucciones:</h5>
                        <ul class="mb-0">
                            <li>El archivo debe estar en formato Excel (.xlsx, .xls) o CSV</li>
                            <li>La primera fila debe contener los encabezados: <strong>nombres, apellidos</strong></li>
                            <li>Puedes descargar la plantilla de ejemplo</li>
                        </ul>
                    </div>

                    <form action="{{ route('administrador.importar_beneficiarios.process') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf

                        <div class="mb-3">
                            <label for="file" class="form-label">Seleccionar archivo:</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required accept=".xlsx,.xls,.csv">
                            @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Formatos permitidos: .xlsx, .xls, .csv</div>
                        </div>

                        <div class="d-grid">
                            @can('importar beneficiarios')
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-download"></i> Importar Beneficiarios
                            </button>
                            @endcan
                        </div>
                    </form>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">

<style>
    .table th {
        background: linear-gradient(135deg, #1b1b1bff 0%, #1b1b1bff 100%);
        color: white;
        font-weight: 600;
    }

    .card-header {
        background: linear-gradient(135deg, #1b1b1bff 0%, #1b1b1bff 100%);
        color: white;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1e62c9ff 0%, #1e62c9ff 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2f6bc5ff 0%, #2f6bc5ff 100%);
    }

</style>
@endsection