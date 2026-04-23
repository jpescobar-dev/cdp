@extends('layouts.theme.app')

@section('title', 'Usuarios')
@section('title2', 'Crear')

@section('content')
<div class="widget-content widget-content-area br-6 mt-2 mb-2">
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label>Nombre</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="form-group mb-3">
            <label>Correo</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="form-group mb-3">
            <label>Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Confirmar contraseña</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Activo</label>
            <select name="is_active" class="form-control" required>
                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Debe cambiar contraseña</label>
            <select name="must_change_password" class="form-control" required>
                <option value="1" {{ old('must_change_password', '1') == '1' ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ old('must_change_password') == '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <div class="form-group mb-4">
            <label>Roles</label>
            <div class="row">
                @foreach($roles as $role)
                    <div class="col-md-4 mb-2">
                        <div class="custom-control custom-checkbox">
                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="role_{{ $role->id }}"
                                name="roles[]"
                                value="{{ $role->name }}"
                                {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}
                            >
                            <label class="custom-control-label" for="role_{{ $role->id }}">
                                {{ $role->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="text-right">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>
@endsection