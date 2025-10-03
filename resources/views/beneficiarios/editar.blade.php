@can('editar beneficiarios')
@extends('layouts.app')

@section('title', 'Editar Beneficiario')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="bi bi-person-gear me-2"></i>
                            Editar Beneficiario
                        </h3>
                        <a href="{{ route('beneficiarios') }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Información del Beneficiario -->
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading">
                            <i class="bi bi-person-circle me-2"></i>Datos del Beneficiario
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Nombre completo:</strong>
                                    {{ $beneficiario->nombres }} {{ $beneficiario->primer_apellido }} {{ $beneficiario->segundo_apellido }}
                                </p>
                                <p class="mb-1"><strong>CURP:</strong> {{ $beneficiario->curp }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>ID Beneficiario:</strong> {{ $beneficiario->id }}</p>
                                <p class="mb-1"><strong>Edad:</strong> {{ $beneficiario->fecha_nac->age }} años</p>

                            </div>
                        </div>
                    </div>

                    <!-- Inclusión de la sección de editar beneficiarios -->
                    @include('estudios.paginas.beneficiario-edit')

                    <!-- Inclusión de la sección de acompañantes -->
                    @include('estudios.paginas.acompanantes')

                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-clipboard-data me-2"></i>
                                Estudios Socioeconómicos
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($beneficiario->estudiosSocioeconomicos->count() > 0)
                            <div class="alert alert-success">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-check-circle me-2"></i>
                                        <strong>Este beneficiario tiene {{ $beneficiario->estudiosSocioeconomicos->count() }} estudio(s) socioeconómico(s).</strong>
                                    </div>
                                    <a href="{{ route('beneficiarios.estudios.editar', ['beneficiario' => $beneficiario->id, 'estudio' => $beneficiario->estudiosSocioeconomicos->first()->id]) }}"
                                        class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-eye me-1"></i> Ver Estudios
                                    </a>
                                </div>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="bi bi-clipboard-x display-1 text-muted"></i>
                                </div>
                                <h4 class="text-muted mb-3">No hay estudios socioeconómicos</h4>
                                <p class="text-muted mb-4">
                                    Este beneficiario no tiene estudios socioeconómicos registrados.
                                </p>
                                <a href="{{ route('estudios.create', $beneficiario->id) }}"
                                    class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Crear Primer Estudio
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal crear -->
@include('estudios.familiares-modals.create')

@foreach($beneficiario->familiares as $familiar)
<!-- Incluir modales individuales -->
@include('estudios.familiares-modals.edit', ['familiar' => $familiar])
@include('estudios.familiares-modals.delete', ['familiar' => $familiar])
@endforeach
@endcan
@endsection