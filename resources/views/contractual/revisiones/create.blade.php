@extends('layouts.theme.app')

@section('title', 'Nueva Revisión Contractual')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 class="h3 mb-0">Nueva Revisión Contractual</h1>
        </div>
    </div>
 

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('contractual.revisiones.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text"
                               name="titulo"
                               id="titulo"
                               class="form-control @error('titulo') is-invalid @enderror"
                               value="{{ old('titulo') }}"
                               required>
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estado inicial</label>
                        <input type="text"
                               class="form-control bg-light"
                               value="BORRADOR"
                               readonly>
                        <small class="text-muted">
                            El estado se asigna automáticamente al crear la revisión.
                        </small>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  id="descripcion"
                                  rows="5"
                                  class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('contractual.revisiones.index') }}" class="btn btn-secondary">
                        Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection