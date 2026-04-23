@extends('layouts.theme.app')

@section('title', 'Editar Revisión Contractual')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 class="h3 mb-0">Editar Revisión Contractual</h1>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('contractual.revisiones.update', $revision) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text"
                               name="titulo"
                               id="titulo"
                               class="form-control @error('titulo') is-invalid @enderror"
                               value="{{ old('titulo', $revision->titulo) }}"
                               required>
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="estado_id" class="form-label">Estado</label>
                        <select name="estado_id"
                                id="estado_id"
                                class="form-select @error('estado_id') is-invalid @enderror"
                                required>
                            <option value="">Seleccione estado</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id }}"
                                    {{ old('estado_id', $revision->estado_id) == $estado->id ? 'selected' : '' }}>
                                    {{ $estado->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('estado_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  id="descripcion"
                                  rows="5"
                                  class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $revision->descripcion) }}</textarea>
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
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection