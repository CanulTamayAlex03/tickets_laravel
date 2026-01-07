@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white py-2 text-center">
            <h5 class="mb-0">Catálogo de Edificios</h5>
        </div>
        <div class="card-body p-3">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <form action="{{ route('admin.edificios') }}" method="GET">
                    <div class="input-group input-group-sm" style="width: 300px;">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text"
                            class="form-control"
                            placeholder="Filtrar por descripción..."
                            name="search"
                            value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Filtrar</button>
                        @if(request('search'))
                        <a href="{{ route('admin.edificios') }}" class="btn btn-outline-secondary" title="Limpiar filtro">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </form>
                @can('crear edificios')
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addBuildingModal">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo edificio
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
                        @foreach($buildings as $building)
                        <tr class="{{ $building->trashed() ? 'table-secondary' : '' }}">
                            <td>{{ $building->id }}</td>
                            <td>
                                {{ $building->description }}
                                @if($building->trashed())
                                <br>
                                @endif
                            </td>
                            <td>
                                @if($building->trashed())
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
                                    @can('editar edificios')
                                    <button class="btn btn-warning px-3 btn-edit"
                                        title="Editar"
                                        data-id="{{ $building->id }}"
                                        data-description="{{ $building->description }}"
                                        data-active="{{ $building->trashed() ? 0 : 1 }}">
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
                    Mostrando {{ $buildings->firstItem() }} a {{ $buildings->lastItem() }} de {{ $buildings->total() }} registros
                </small>
                <nav aria-label="Page navigation">
                    {{ $buildings->onEachSide(1)->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear edificio -->
<div class="modal fade" id="addBuildingModal" tabindex="-1" aria-labelledby="addBuildingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="addBuildingModalLabel">Nuevo Edificio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addBuildingForm">
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
                <button type="button" class="btn btn-primary" id="saveBuildingBtn">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar edificio -->
<div class="modal fade" id="editBuildingModal" tabindex="-1" aria-labelledby="editBuildingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="editBuildingModalLabel">Editar Edificio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editBuildingForm">
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
                                Desmarca para inactivar el edificio
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="updateBuildingBtn">Guardar Cambios</button>
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
    $('#saveBuildingBtn').click(function() {
        const form = $('#addBuildingForm');
        const btn = $(this);
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');
        
        $.ajax({
            url: '{{ route("admin.edificios.store") }}',
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addBuildingModal').modal('hide');
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
                    showToast('error', xhr.responseJSON?.message || 'Error al guardar el edificio');
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
            $('#statusHelp').html('<i class="bi bi-info-circle me-1"></i>Desmarca para inactivar el edificio');
        } else {
            $('#edit_active').prop('checked', false);
            $('#statusHelp').html('<i class="bi bi-info-circle me-1"></i>Marca para activar el edificio');
        }
        
        $.ajax({
            url: '{{ url("admin/edificios") }}/' + id + '/tickets-count',
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
        
        $('#editBuildingModal').modal('show');
    });

    $('#updateBuildingBtn').click(function() {
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
            url: '{{ route("admin.edificios.update", ":id") }}'.replace(':id', id),
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#editBuildingModal').modal('hide');
                    
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
                    showToast('error', xhr.responseJSON?.message || 'Error al actualizar el edificio');
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
