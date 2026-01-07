@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white py-2 text-center">
            <h5 class="mb-0">Catálogo de Empleados</h5>
        </div>
        <div class="card-body p-3">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <form action="{{ route('admin.empleados') }}" method="GET">
                    <div class="input-group input-group-sm" style="width: 300px;">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text"
                            class="form-control"
                            placeholder="Buscar por nombre o nómina..."
                            name="search"
                            value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Filtrar</button>
                        @if(request('search'))
                        <a href="{{ route('admin.empleados') }}" class="btn btn-outline-secondary" title="Limpiar filtro">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </form>
                @can('crear empleados')
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo empleado
                </button>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-2 mx-auto" style="width: 95%; margin-top: 15px">
                    <thead class="table-dark">
                        <tr>
                            <th width="10%">ID</th>
                            <th>Nombre Completo</th>
                            <th>Nómina</th>
                            <th width="15%">Estado</th>
                            <th width="20%" class="text-start">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr class="{{ $employee->trashed() ? 'table-secondary' : '' }}">
                            <td>{{ $employee->id }}</td>
                            <td>
                                {{ $employee->full_name }}
                                @if($employee->trashed())
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-calendar-x"></i>
                                    Inactivado: {{ $employee->deleted_at->format('d/m/Y H:i') }}
                                </small>
                                @endif
                            </td>
                            <td>{{ $employee->no_nomina }}</td>
                            <td>
                                @if($employee->trashed())
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i> Inactivo
                                </span>
                                @else
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i> Activo
                                </span>
                                @endif
                            </td>
                            <td class="text-start">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Acciones">
                                    @can('editar empleados')
                                    <button class="btn btn-warning px-3 btn-edit"
                                        title="Editar"
                                        data-id="{{ $employee->id }}"
                                        data-name="{{ $employee->name }}"
                                        data-lastname="{{ $employee->lastname }}"
                                        data-lastname2="{{ $employee->lastname2 ?? '' }}"
                                        data-no_nomina="{{ $employee->no_nomina }}"
                                        data-active="{{ $employee->trashed() ? 0 : 1 }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    @endcan
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
                    Mostrando {{ $employees->firstItem() }} a {{ $employees->lastItem() }} de {{ $employees->total() }} registros
                </small>
                <nav aria-label="Page navigation">
                    {{ $employees->onEachSide(1)->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear empleado -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="addEmployeeModalLabel">Nuevo Empleado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addEmployeeForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="nameError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastname" class="form-label">Apellido Paterno *</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                            <div class="invalid-feedback" id="lastnameError"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="lastname2" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="lastname2" name="lastname2">
                            <div class="invalid-feedback" id="lastname2Error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="no_nomina" class="form-label">Número de Nómina *</label>
                            <input type="text" class="form-control" id="no_nomina" name="no_nomina" required>
                            <div class="invalid-feedback" id="no_nominaError"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveEmployeeBtn">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar empleado -->
<div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="editEmployeeModalLabel">Editar Empleado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editEmployeeForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                            <div class="invalid-feedback" id="editNameError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_lastname" class="form-label">Apellido Paterno *</label>
                            <input type="text" class="form-control" id="edit_lastname" name="lastname" required>
                            <div class="invalid-feedback" id="editLastnameError"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_lastname2" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="edit_lastname2" name="lastname2">
                            <div class="invalid-feedback" id="editLastname2Error"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_no_nomina" class="form-label">Número de Nómina *</label>
                            <input type="text" class="form-control" id="edit_no_nomina" name="no_nomina" required>
                            <div class="invalid-feedback" id="editNoNominaError"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" 
                                   id="edit_active" name="active" value="1">
                            <label class="form-check-label" for="edit_active">
                                <strong>Activo</strong>
                            </label>
                        </div>
                        <div class="form-text">
                            <span id="statusHelp" class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Desmarca para inactivar el empleado
                            </span>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" id="statusAlert" style="display: none;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Este empleado tiene tickets asociados. 
                        Si lo inactivas, los tickets seguirán mostrando al empleado pero con el estado "INACTIVO".
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="updateEmployeeBtn">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

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
        background-color: #f8f9fa !important;
    }
    
    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
    
    .form-check-input:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

@section('scripts')
<script>
$(document).ready(function() {
    // Crear empleado
    $('#saveEmployeeBtn').click(function() {
        const form = $('#addEmployeeForm');
        const btn = $(this);
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');
        
        $.ajax({
            url: '{{ route("admin.empleados.store") }}',
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addEmployeeModal').modal('hide');
                    form[0].reset();
                    
                    showToast('success', response.message);
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        $(`#${key}Error`).text(errors[key][0]).show();
                        $(`#${key}`).addClass('is-invalid');
                    });
                } else {
                    showToast('error', xhr.responseJSON?.message || 'Error al guardar el empleado');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html('Guardar');
            }
        });
    });

    // Editar empleado
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const lastname = $(this).data('lastname');
        const lastname2 = $(this).data('lastname2');
        const no_nomina = $(this).data('no_nomina');
        const active = $(this).data('active');
        
        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_lastname').val(lastname);
        $('#edit_lastname2').val(lastname2);
        $('#edit_no_nomina').val(no_nomina);
        
        // Configurar el checkbox
        if (active == '1') {
            $('#edit_active').prop('checked', true);
            $('#statusHelp').html('<i class="bi bi-info-circle me-1"></i>Desmarca para inactivar el empleado');
        } else {
            $('#edit_active').prop('checked', false);
            $('#statusHelp').html('<i class="bi bi-info-circle me-1"></i>Marca para activar el empleado');
        }
        
        // Verificar si el empleado tiene tickets asociados
        $.ajax({
            url: '{{ url("admin/empleados") }}/' + id + '/tickets-count',
            type: 'GET',
            success: function(response) {
                if (response.count > 0) {
                    $('#statusAlert').show();
                } else {
                    $('#statusAlert').hide();
                }
            },
            error: function() {
                $('#statusAlert').hide();
            }
        });
        
        $('#editEmployeeModal').modal('show');
    });

    $('#updateEmployeeBtn').click(function() {
        const btn = $(this);
        const id = $('#edit_id').val();
        
        // Preparar datos
        const formData = {
            _method: 'PUT',
            _token: '{{ csrf_token() }}',
            name: $('#edit_name').val(),
            lastname: $('#edit_lastname').val(),
            lastname2: $('#edit_lastname2').val(),
            no_nomina: $('#edit_no_nomina').val(),
            active: $('#edit_active').is(':checked') ? '1' : '0'
        };
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');
        
        $.ajax({
            url: '{{ route("admin.empleados.update", ":id") }}'.replace(':id', id),
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#editEmployeeModal').modal('hide');
                    
                    showToast('success', response.message);
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        if (key === 'name') {
                            $('#editNameError').text(errors[key][0]).show();
                            $('#edit_name').addClass('is-invalid');
                        } else if (key === 'lastname') {
                            $('#editLastnameError').text(errors[key][0]).show();
                            $('#edit_lastname').addClass('is-invalid');
                        } else if (key === 'lastname2') {
                            $('#editLastname2Error').text(errors[key][0]).show();
                            $('#edit_lastname2').addClass('is-invalid');
                        } else if (key === 'no_nomina') {
                            $('#editNoNominaError').text(errors[key][0]).show();
                            $('#edit_no_nomina').addClass('is-invalid');
                        }
                    });
                } else {
                    showToast('error', xhr.responseJSON?.message || 'Error al actualizar el empleado');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html('Guardar Cambios');
            }
        });
    });

    // Limpiar errores al cerrar modales
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').hide();
    });

    // Función para mostrar toast
    function showToast(type, message) {
        const toast = $(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 99999;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        
        $('body').append(toast);
        
        setTimeout(() => {
            toast.alert('close');
        }, 5000);
    }
});
</script>
@endsection
@endsection