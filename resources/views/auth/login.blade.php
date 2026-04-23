@extends('layouts.guest')

@section('content')
<div class="form-container">
    <div class="form-form">
        <div class="form-form-wrap">
            <div class="form-container">
                <div class="form-content">

                    <h1 class="">
                        Iniciar sesión en
                        <a href="{{ url('/') }}">
                            <span class="brand-name">{{ config('app.name', 'Capj') }}</span>
                        </a>
                    </h1>

                    <p class="signup-link">
                        Acceso exclusivo para usuarios autorizados del sistema
                    </p>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login.store') }}" class="text-left">
                        @csrf

                        <div class="form">

                            <div id="username-field" class="field-wrapper input">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>

                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="Correo electrónico"
                                    required
                                    autofocus
                                >
                            </div>

                            @error('email')
                                <div class="text-danger mt-1 mb-2">{{ $message }}</div>
                            @enderror

                            <div id="password-field" class="field-wrapper input mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>

                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Contraseña"
                                    required
                                    autocomplete="current-password"
                                >
                            </div>

                            @error('password')
                                <div class="text-danger mt-1 mb-2">{{ $message }}</div>
                            @enderror

                            <div class="d-sm-flex justify-content-between">
                                <div class="field-wrapper toggle-pass">
                                    <p class="d-inline-block">Mostrar contraseña</p>
                                    <label class="switch s-primary">
                                        <input type="checkbox" id="toggle-password" class="d-none">
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                <div class="field-wrapper">
                                    <button type="submit" class="btn btn-primary">
                                        Ingresar
                                    </button>
                                </div>
                            </div>

                            <div class="field-wrapper text-center keep-logged-in">
                                <div class="n-chk new-checkbox checkbox-outline-primary">
                                    <label class="new-control new-checkbox checkbox-outline-primary">
                                        <input
                                            type="checkbox"
                                            name="remember"
                                            class="new-control-input"
                                            {{ old('remember') ? 'checked' : '' }}
                                        >
                                        <span class="new-control-indicator"></span>
                                        Mantener sesión iniciada
                                    </label>
                                </div>
                            </div>

                            @if (Route::has('password.request'))
                                <div class="field-wrapper">
                                    <a href="{{ route('password.request') }}" class="forgot-pass-link">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                </div>
                            @endif

                        </div>
                    </form>

                    <p class="terms-conditions">
                        © {{ date('Y') }} {{ config('app.name', 'Laravel') }}.
                        Sistema de acceso institucional.
                    </p>

                </div>
            </div>
        </div>
    </div>

    <div class="form-image">
        <div class="l-image"></div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('toggle-password');
        const password = document.getElementById('password');

        if (toggle && password) {
            toggle.addEventListener('change', function () {
                password.type = this.checked ? 'text' : 'password';
            });
        }
    });
</script>
@endpush
@endsection