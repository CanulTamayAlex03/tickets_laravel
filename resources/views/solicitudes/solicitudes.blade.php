@extends('layouts.app')

@section('content')
<div class="container mt-4">

    {{-- Card de búsqueda --}}
    <div class="card mb-4 shadow-sm" style="background-color: #f0f0f0ff;">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-list-task me-2"></i> Mis solicitudes</h5>
        </div>
        <div class="card-body">
           <form class="row g-3">
    <div class="col-md-6">
        <label for="buscarUsuario" class="form-label fw-bold">Buscar usuario</label>
        <input type="text" id="buscarUsuario" class="form-control" placeholder="Ingrese nombre o correo">
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-search"></i> Buscar
        </button>
    </div>
</form>

        </div>
    </div>

    {{-- Card de tabla --}}
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i> Lista de solicitudes</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Detalle de solicitud</th>
                            <th scope="col">Fecha de recepción</th>
                            <th scope="col">Estatus</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>1</th>
                            <td>Solicitud de soporte técnico</td>
                            <td>2025-08-10</td>
                            <td><span class="badge bg-warning text-dark">Pendiente</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <th>2</th>
                            <td>Solicitud de cambio de equipo</td>
                            <td>2025-08-09</td>
                            <td><span class="badge bg-success">Completada</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <th>3</th>
                            <td>Solicitud de instalación de software</td>
                            <td>2025-08-08</td>
                            <td><span class="badge bg-danger">Rechazada</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>
    body {
        background-color: #afadadff; 
    }
</style>
@endsection