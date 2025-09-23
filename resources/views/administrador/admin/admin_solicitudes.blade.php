@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white py-2 text-center">
            <h5 class="mb-0">Administración de Solicitudes</h5>
        </div>
        <div class="card-body p-3">
            <div class="d-flex justify-content-between mb-3 mt-4">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.admin_solicitudes', ['status' => 'nuevo']) }}"
                        class="btn btn-filter {{ request('status') == 'nuevo' ? 'active' : '' }}">
                        <i class="bi bi-star me-1"></i> Nuevos
                    </a>
                    <a href="{{ route('admin.admin_solicitudes', ['status' => 'atendiendo']) }}"
                        class="btn btn-filter {{ request('status') == 'atendiendo' ? 'active' : '' }}">
                        <i class="bi bi-hourglass-split me-1"></i> Atendiendo
                    </a>
                    <a href="{{ route('admin.admin_solicitudes', ['status' => 'cerrado']) }}"
                        class="btn btn-filter {{ request('status') == 'cerrado' ? 'active' : '' }}">
                        <i class="bi bi-x-circle me-1"></i> Cerrado por usuario
                    </a>
                    <a href="{{ route('admin.admin_solicitudes', ['status' => 'pendiente']) }}"
                        class="btn btn-filter {{ request('status') == 'pendiente' ? 'active' : '' }}">
                        <i class="bi bi-clock-history me-1"></i> Pendiente
                    </a>
                    <a href="{{ route('admin.admin_solicitudes', ['status' => 'completado']) }}"
                        class="btn btn-filter {{ request('status') == 'completado' ? 'active' : '' }}">
                        <i class="bi bi-check2-circle me-1"></i> Completado
                    </a>
                    <button class="btn btn-filter {{ request()->hasAny(['search', 'employee_id', 'building_id', 'department_id', 'status_filter']) ? 'active' : '' }}"
                        data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-funnel me-1"></i> Búsqueda
                    </button>
                </div>
            </div>

            <!-- Tabla de solicitudes -->
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-2">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">ID</th>
                            <th>Descripción</th>
                            <th width="15%">Usuario/Área</th>
                            <th>Fecha Recepción</th>
                            <th>Estatus</th>
                            <th width="12%" class="text-center">Acciones</th>
                            <th width="15%">Asignado a</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>
                            <td>{{ $ticket->description }}</td>
                            <td class="py-2">
                                <div class="d-flex flex-column">
                                    <strong>{{ $ticket->employee?->name ?? '—' }}</strong>
                                    <small class="text-muted">{{ $ticket->department?->description ?? '—' }}</small>
                                </div>
                            </td>
                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @php
                                $statusColors = [
                                1 => 'primary', // Nuevo
                                2 => 'info', // Atendiendo
                                3 => 'danger', // Cerrado
                                4 => 'warning', // Pendiente
                                5 => 'success', // Completado
                                ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$ticket->service_status_id] ?? 'secondary' }}">
                                    {{ $ticket->serviceStatus->description ?? 'Sin estatus' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-primary" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-warning" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <button class="btn btn-sm btn-dark me-1" title="Asignar">
                                        <i class="bi bi-rocket"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info" title="Enviar mensaje">
                                        <i class="bi bi-chat-left-text"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No hay tickets con este filtro</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Mostrando {{ $tickets->firstItem() }} a {{ $tickets->lastItem() }} de {{ $tickets->total() }} registros
                </small>

                <nav aria-label="Page navigation">
                    {{ $tickets->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
                </nav>
            </div>

        </div>
    </div>
</div>
<!-- Modal de Filtros -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="filterModalLabel">Buscar Solicitudes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.admin_solicitudes') }}" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employee_id" class="form-label">Empleado</label>
                            <select class="form-select select2-employee" id="employee_id" name="employee_id">
                                <option value="">Seleccionar empleado</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->full_name }} - {{ $employee->no_nomina ?? 'Sin nómina' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="building_id" class="form-label">Edificio</label>
                            <select class="form-select select2-building" id="building_id" name="building_id">
                                <option value="">Seleccionar edificio</option>
                                @foreach($buildings as $building)
                                <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                                    {{ $building->description }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="department_id" class="form-label">Departamento</label>
                            <select class="form-select select2-department" id="department_id" name="department_id">
                                <option value="">Seleccionar departamento</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->description }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status_filter" class="form-label">Estatus</label>
                            <select class="form-select" id="status_filter" name="status_filter">
                                <option value="">Todos los estatus</option>
                                <option value="nuevo" {{ request('status_filter') == 'nuevo' ? 'selected' : '' }}>Nuevo</option>
                                <option value="atendiendo" {{ request('status_filter') == 'atendiendo' ? 'selected' : '' }}>Atendiendo</option>
                                <option value="pendiente" {{ request('status_filter') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="completado" {{ request('status_filter') == 'completado' ? 'selected' : '' }}>Completado</option>
                                <option value="cerrado" {{ request('status_filter') == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="search" class="form-label">Buscar en descripción</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Buscar en descripción..." value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="{{ route('admin.admin_solicitudes') }}" class="btn btn-outline-danger">Limpiar filtros</a>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // Inicializar Select2
    $('.select2-employee').select2({
        placeholder: "Buscar por nombre o nómina",
        language: "es",
        width: '100%',
        dropdownParent: $('#filterModal')
    });
    
    $('.select2-building').select2({
        placeholder: "Seleccionar edificio",
        language: "es",
        width: '100%',
        dropdownParent: $('#filterModal')
    });
    
    $('.select2-department').select2({
        placeholder: "Seleccionar departamento",
        language: "es",
        width: '100%',
        dropdownParent: $('#filterModal')
    });
    
    // Verificar si hay parámetros de filtro en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = urlParams.has('search') || 
                       urlParams.has('employee_id') || 
                       urlParams.has('building_id') || 
                       urlParams.has('department_id') || 
                       urlParams.has('status_filter');
});
</script>
@endsection

<style>
    .table {
        font-size: 0.85rem;
    }

    .table th {
        white-space: nowrap;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        font-weight: 500;
    }

    .btn-group-sm>.btn {
        padding: 0.25rem 0.5rem;
    }

    .pagination {
        font-size: 0.8rem;
        margin: 0;
    }

    .card-header h5 {
        font-weight: 600;
    }

    /* Estilo base de los filtros */
    .btn-filter {
        background-color: #6f42c1;
        color: #fff;
        border: none;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Hover */
    .btn-filter:hover {
        background-color: #5a379c;
        color: #fff;
    }

    /* Botón activo */
    .btn-filter.active {
        background-color: #4b2982;
        font-weight: 600;
        box-shadow: 0 0 6px rgba(111, 66, 193, 0.6);
        color: #fff;
    }


    /* Hover states */
    .btn-primary:hover {
        background-color: #0a58ca;
        border-color: #0a53be;
    }

    .btn-secondary:hover {
        background-color: #565e64;
        border-color: #51585e;
    }

    .btn-filter.active {
        background-color: #4b2982;
        font-weight: 600;
        box-shadow: 0 0 6px rgba(111, 66, 193, 0.6);
        color: #fff;
    }
</style>
@endsection