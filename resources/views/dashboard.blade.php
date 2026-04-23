@extends('layouts.theme.app')

@section('title', 'Dashboard')
@section('title2', 'Dashboard')

@section('content')

<div class="row">

    <div class="col-md-4 mb-4">
        <div class="widget p-3">
            <h6>Usuario</h6>
            <p class="mb-0">{{ Auth::user()->name }}</p>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="widget p-3">
            <h6>Email</h6>
            <p class="mb-0">{{ Auth::user()->email }}</p>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="widget p-3">
            <h6>Rol</h6>
            <p class="mb-0">
                {{ Auth::user()->getRoleNames()->first() ?? 'Sin rol' }}
            </p>
        </div>
    </div>

</div>

@endsection