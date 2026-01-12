@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white py-2 text-center">
            <h5 class="mb-0">Administración de Personal de Soporte</h5>
        </div>
        <div class="card-body p-3">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <form action="{{ route('admin.soporte') }}" method="GET">
                    <div class="input-group input-group-sm" style="width: 300px;">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text"
                            class="form-control"
                            placeholder="Filtrar por nombre/apellido/email..."
                            name="search"
                            value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Filtrar</button>
                        @if(request('search'))
                        <a href="{{ route('admin.soporte') }}" class="btn btn-outline-secondary" title="Limpiar filtro">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </form>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPersonalModal">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo personal
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-2 mx-auto" style="width: 90%; margin-top: 15px">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Nombre</th>
                            <th width="20%">Apellidos</th>
                            <th width="25%">Email</th>
                            <th width="10%">Estado</th>
                            <th width="20%" class="text-start">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supportPersonals as $personal)
                        <tr>
                            <td>{{ $personal->id }}</td>
                            <td>{{ $personal->name }}</td>
                            <td>{{ $personal->lastnames }}</td>
                            <td>
                                <small>{{ $personal->email }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $personal->active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $personal->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-start">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Acciones">
                                    <button class="btn btn-warning px-3 btn-edit"
                                        title="Editar"
                                        data-id="{{ $personal->id }}"
                                        data-name="{{ $personal->name }}"
                                        data-lastnames="{{ $personal->lastnames }}"
                                        data-email="{{ $personal->email }}"
                                        data-active="{{ $personal->active }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Mostrando {{ $supportPersonals->firstItem() }} a {{ $supportPersonals->lastItem() }} de {{ $supportPersonals->total() }} registros
                </small>
                <nav aria-label="Page navigation">
                    {{ $supportPersonals->onEachSide(1)->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
    </div>
</div>

@include('administrador.admin.modals.support_personal_create')
@include('administrador.admin.modals.support_personal_edit')

@include('administrador.admin.scripts.support_personals_modals')

<style>
    .table {
        font-size: 0.85rem;
    }

    .table th {
        white-space: nowrap;
    }

    .pagination {
        font-size: 0.8rem;
        margin: 0;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
    }

    .btn-group-sm>.btn {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        margin: 0 2px;
    }

    .card-header.text-center h5 {
        text-align: center;
        width: 100%;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    .input-group .btn-outline-secondary {
        border-left: none;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    .table-secondary {
        opacity: 0.8;
    }
</style>
@endsection