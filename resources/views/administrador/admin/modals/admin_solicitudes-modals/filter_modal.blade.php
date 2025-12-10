<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #210240;">
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
                            <label for="search" class="form-label">Buscar en descripción, empleado, departamento, etc.</label>
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Buscar en descripción, nombres, departamentos..." value="{{ request('search') }}">
                        </div>

                        <!-- Filtros adicionales si los necesitas -->
                        <div class="col-md-6 mb-3">
                            <label for="employee_search" class="form-label">Búsqueda específica de empleado</label>
                            <input type="text" class="form-control" id="employee_search" name="employee_search"
                                placeholder="Buscar por nombre o nómina..." value="{{ request('employee_search') }}">
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