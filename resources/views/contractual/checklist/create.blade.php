@extends('layouts.theme.app')

@section('content')
<div class="widget-content widget-content-area br-6 mt-2 mb-2">

    <h4>Nuevo Ítem Checklist</h4>

    <form method="POST" action="{{ route('contractual.revisiones.snapshots.checklist.store', [$revision, $snapshot]) }}">
        @csrf

        <div class="mb-3">
            <label>Ítem</label>
            <input type="text" name="item" class="form-control" value="{{ old('item') }}" required>
            @error('item')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Estado</label>
            <select name="estado_item" class="form-control">
                <option value="cumple" {{ old('estado_item') == 'cumple' ? 'selected' : '' }}>Cumple</option>
                <option value="no_cumple" {{ old('estado_item') == 'no_cumple' ? 'selected' : '' }}>No cumple</option>
                <option value="no_se_encuentra" {{ old('estado_item') == 'no_se_encuentra' ? 'selected' : '' }}>No se encuentra</option>
                <option value="pendiente_verificar" {{ old('estado_item') == 'pendiente_verificar' ? 'selected' : '' }}>Pendiente verificar</option>
            </select>
            @error('estado_item')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Observación</label>
            <textarea name="observacion" class="form-control">{{ old('observacion') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Referencia documental</label>
            <textarea name="referencia_documental" class="form-control">{{ old('referencia_documental') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Orden</label>
            <input type="number" name="orden" class="form-control" min="0" value="{{ old('orden', 0) }}">
        </div>

        <button class="btn btn-success btn-sm">Guardar</button>

    </form>

</div>
@endsection
