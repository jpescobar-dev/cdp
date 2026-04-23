@extends('layouts.theme.app')

@section('title', 'Nuevo Hallazgo')
@section('title2', 'Crear')

@section('content')
<div class="widget-content widget-content-area br-6 mt-2 mb-2">
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="mb-0">Nuevo Hallazgo</h4>
            <small class="text-muted">Snapshot v{{ $snapshot->numero_version }} | {{ $revision->titulo }}</small>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('contractual.revisiones.snapshots.hallazgos.index', [$revision, $snapshot]) }}"
               class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>
    </div>

    <div class="widget widget-table-one">
        <div class="widget-content">
            <form action="{{ route('contractual.revisiones.snapshots.hallazgos.store', [$revision, $snapshot]) }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="titulo">Título</label>
                        <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" value="{{ old('titulo') }}" required>
                        @error('titulo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="tipo_hallazgo">Tipo de hallazgo</label>
                        <select name="tipo_hallazgo" id="tipo_hallazgo" class="form-control">
                            <option value="">Seleccione</option>
                            <option value="critico" {{ old('tipo_hallazgo') == 'critico' ? 'selected' : '' }}>Crítico</option>
                            <option value="relevante" {{ old('tipo_hallazgo') == 'relevante' ? 'selected' : '' }}>Relevante</option>
                            <option value="observacion_menor" {{ old('tipo_hallazgo') == 'observacion_menor' ? 'selected' : '' }}>Observación menor</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="nivel_criticidad">Nivel criticidad</label>
                        <select name="nivel_criticidad" id="nivel_criticidad" class="form-control">
                            <option value="">Seleccione</option>
                            <option value="alto" {{ old('nivel_criticidad') == 'alto' ? 'selected' : '' }}>Alto</option>
                            <option value="medio" {{ old('nivel_criticidad') == 'medio' ? 'selected' : '' }}>Medio</option>
                            <option value="bajo" {{ old('nivel_criticidad') == 'bajo' ? 'selected' : '' }}>Bajo</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tipo_riesgo">Tipo de riesgo</label>
                        <input type="text" name="tipo_riesgo" id="tipo_riesgo" class="form-control" value="{{ old('tipo_riesgo') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="estado_id">Estado</label>
                        <select name="estado_id" id="estado_id" class="form-control">
                            <option value="">Seleccione</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id }}" {{ old('estado_id') == $estado->id ? 'selected' : '' }}>
                                    {{ $estado->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="hecho_acreditado">Hecho acreditado</label>
                        <textarea name="hecho_acreditado" id="hecho_acreditado" rows="3" class="form-control">{{ old('hecho_acreditado') }}</textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="observacion">Observación</label>
                        <textarea name="observacion" id="observacion" rows="3" class="form-control">{{ old('observacion') }}</textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="fundamento_documental">Fundamento documental</label>
                        <textarea name="fundamento_documental" id="fundamento_documental" rows="3" class="form-control">{{ old('fundamento_documental') }}</textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="consecuencia_posible">Consecuencia posible</label>
                        <textarea name="consecuencia_posible" id="consecuencia_posible" rows="3" class="form-control">{{ old('consecuencia_posible') }}</textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="recomendacion">Recomendación</label>
                        <textarea name="recomendacion" id="recomendacion" rows="3" class="form-control">{{ old('recomendacion') }}</textarea>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary btn-sm">Guardar Hallazgo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
