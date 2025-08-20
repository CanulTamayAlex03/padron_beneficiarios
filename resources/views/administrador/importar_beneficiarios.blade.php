@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Importar Beneficiarios</h2>
                        <a href="{{ route('administrador.exportar_beneficiarios') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-download"></i> Descargar Plantilla
                        </a>
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
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Importar Beneficiarios
                            </button>
                        </div>
                    </form>

                    <!-- Tabla de datos importados -->
                    @if(session()->has('importedData') && count(session('importedData')) > 0)
                    <div class="mt-5">
                        <h4 class="mb-3">📊 Datos Importados</h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead class="table-success">
                                    <tr>
                                        @foreach(array_keys(session('importedData')[0]) as $header)
                                            <th>{{ ucfirst($header) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(session('importedData') as $index => $row)
                                        @if($index > 0) <!-- Saltar encabezados -->
                                        <tr>
                                            @foreach($row as $value)
                                                <td>{{ $value }}</td>
                                            @endforeach
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('beneficiarios') }}" class="btn btn-outline-primary">
                                <i class="bi bi-list"></i> Ver todos los beneficiarios
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Tabla de beneficiarios existentes -->
                    @if(!session()->has('importedData'))
                    <div class="mt-5">
                        <h4 class="mb-3">👥 Beneficiarios Existentes</h4>
                        @php
                            $beneficiarios = App\Models\Beneficiario::latest()->take(5)->get();
                        @endphp
                        
                        @if($beneficiarios->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombres</th>
                                        <th>Apellidos</th>
                                        <th>Fecha Registro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($beneficiarios as $beneficiario)
                                    <tr>
                                        <td>{{ $beneficiario->id }}</td>
                                        <td>{{ $beneficiario->nombres }}</td>
                                        <td>{{ $beneficiario->apellidos }}</td>
                                        <td>{{ $beneficiario->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('beneficiarios') }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> Ver todos los registros
                            </a>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle"></i> No hay beneficiarios registrados aún.
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">

<style>
    .table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    }
</style>
@endsection
