@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white py-2 text-center">
            <h5 class="mb-0">Catálogo de Departamentos</h5>
        </div>
        <div class="card-body p-3">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <form action="{{ route('admin.departamentos') }}" method="GET">
                    <div class="input-group input-group-sm" style="width: 300px;">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text"
                            class="form-control"
                            placeholder="Filtrar por descripción..."
                            name="search"
                            value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Filtrar</button>
                        @if(request('search'))
                        <a href="{{ route('admin.departamentos') }}" class="btn btn-outline-secondary" title="Limpiar filtro">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </form>
                @can('crear departamentos')
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo departamento
                </button>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-2 mx-auto" style="width: 90%; margin-top: 15px">
                    <thead class="table-dark">
                        <tr>
                            <th width="10%">ID</th>
                            <th>Descripción</th>
                            <th width="15%">Estado</th>
                            <th width="20%" class="text-start">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($departments as $department)
                        <tr class="{{ $department->trashed() ? 'table-secondary' : '' }}">
                            <td>{{ $department->id }}</td>
                            <td>
                                {{ $department->description }}
                                @if($department->trashed())
                                <br>
                                @endif
                            </td>
                            <td>
                                @if($department->trashed())
                                <span class="badge bg-danger">
                                Inactivo
                                </span>
                                @else
                                <span class="badge bg-success">
                                Activo
                                </span>
                                @endif
                            </td>
                            <td class="text-start">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Acciones">
                                    @can('editar departamentos')
                                    <button class="btn btn-warning px-3 btn-edit"
                                        title="Editar"
                                        data-id="{{ $department->id }}"
                                        data-description="{{ $department->description }}"
                                        data-active="{{ $department->trashed() ? 0 : 1 }}">
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
                    Mostrando {{ $departments->firstItem() }} a {{ $departments->lastItem() }} de {{ $departments->total() }} registros
                </small>
                <nav aria-label="Page navigation">
                    {{ $departments->onEachSide(1)->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear departamento -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="addDepartmentModalLabel">Nuevo Departamento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDepartmentForm">
                    @csrf
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción *</label>
                        <input type="text" class="form-control" id="description" name="description" required>
                        <div class="invalid-feedback" id="descriptionError"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveDepartmentBtn">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar departamento -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="editDepartmentModalLabel">Editar Departamento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDepartmentForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Descripción *</label>
                        <input type="text" class="form-control" id="edit_description" name="description" required>
                        <div class="invalid-feedback" id="editDescriptionError"></div>
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
                                Desmarca para inactivar el departamento
                            </span>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" id="statusAlert" style="display: none;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Este departamento tiene tickets asociados. 
                        Si lo inactivas, los tickets seguirán mostrando el departamento pero con el estado "INACTIVO".
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="updateDepartmentBtn">Guardar Cambios</button>
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
    $('#saveDepartmentBtn').click(function() {
        const form = $('#addDepartmentForm');
        const btn = $(this);
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');
        
        $.ajax({
            url: '{{ route("admin.departamentos.store") }}',
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addDepartmentModal').modal('hide');
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
                    showToast('error', xhr.responseJSON?.message || 'Error al guardar el departamento');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html('Guardar');
            }
        });
    });

    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const description = $(this).data('description');
        const active = $(this).data('active');
        
        $('#edit_id').val(id);
        $('#edit_description').val(description);
        
        if (active == '1') {
            $('#edit_active').prop('checked', true);
            $('#statusHelp').html('<i class="bi bi-info-circle me-1"></i>Desmarca para inactivar el departamento');
        } else {
            $('#edit_active').prop('checked', false);
            $('#statusHelp').html('<i class="bi bi-info-circle me-1"></i>Marca para activar el departamento');
        }
        
        $.ajax({
            url: '{{ url("admin/departamentos") }}/' + id + '/tickets-count',
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
        
        $('#editDepartmentModal').modal('show');
    });

    $('#updateDepartmentBtn').click(function() {
        const btn = $(this);
        const id = $('#edit_id').val();
        
        const formData = {
            _method: 'PUT',
            _token: '{{ csrf_token() }}',
            description: $('#edit_description').val(),
            active: $('#edit_active').is(':checked') ? '1' : '0'
        };
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');
        
        $.ajax({
            url: '{{ route("admin.departamentos.update", ":id") }}'.replace(':id', id),
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#editDepartmentModal').modal('hide');
                    
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
                        if (key === 'description') {
                            $('#editDescriptionError').text(errors[key][0]).show();
                            $('#edit_description').addClass('is-invalid');
                        }
                    });
                } else {
                    showToast('error', xhr.responseJSON?.message || 'Error al actualizar el departamento');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html('Guardar Cambios');
            }
        });
    });

    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').hide();
    });

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