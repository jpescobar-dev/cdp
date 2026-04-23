@extends('layouts.theme.app')

@section('title', 'Crear Usuario')
@section('title2', 'Nuevo Registro')

@section('content')

@include('partials.alerts')

<div class="widget-content widget-content-area br-6 mt-2 mb-2">
    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="name">Nombre</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" maxlength="100" required>
                    @error('name')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" maxlength="100" required>
                    @error('email')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="activo">Estado</label>
                    <select name="activo" id="activo" class="form-control @error('activo') is-invalid @enderror" required>
                        <option value="1" {{ old('activo', '1') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('activo')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="role">Rol</label>
                    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                        <option value="">Seleccione un rol</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group text-end">
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-warning btn-sm">Cancelar</a>
            <button type="submit" class="btn btn-outline-primary btn-sm">Guardar</button>
        </div>
    </form>
</div>
@endsection