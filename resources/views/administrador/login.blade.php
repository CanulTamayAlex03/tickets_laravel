@extends('layouts.app')

@section('content')
<div class="container-fluid d-flex mt-4" style="background: url('/images/tickets_fondo.jpg') no-repeat center center fixed; background-size: cover; min-height: 100vh; padding: 20px; overflow: hidden;">
    <div class="row justify-content-center w-100 m-0">
        <div class="col-md-6 col-lg-4 mx-auto">
            <div class="text-center mb-3">
                <h1 class="fw-bold text-white">DEPARTAMENTO DE INFORMÁTICA</h1>
            </div>
            <div class="card border-0 shadow-sm" style="background-color: rgba(255, 255, 255, 0.9); min-height: 330px;">
                <div class="card-header bg-dark text-white py-2">
                    <h5 class="mb-0 text-center">INICIAR SESIÓN</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Correo -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Correo electrónico:</label>
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="Ingrese su correo" required autofocus>
                            @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Contraseña -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Contraseña:</label>
                            <input type="password" name="password" class="form-control form-control-sm" placeholder="Ingrese su contraseña" required>
                            @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Recordar sesión -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Recordar sesión</label>
                        </div>

                        <!-- Botón -->
                        <div class="d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-sm btn-primary px-4">
                                <i class="bi bi-box-arrow-in-right"></i> Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .compact-form .form-label {
        margin-bottom: 0.2rem;
        font-size: 0.9rem;
    }

    .compact-form .form-control {
        padding: 0.25rem 0.5rem;
        font-size: 0.9rem;
    }

    .card {
        border-radius: 5px;
        margin-top: 10px;
    }

    .card-header {
        border-radius: 5px 5px 0 0 !important;
    }

    body,
    html {
        height: 100%;
        overflow: hidden;
    }
</style>
@endsection