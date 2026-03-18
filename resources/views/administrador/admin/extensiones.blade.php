@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white py-2 text-center">
            <h5 class="mb-0">Catálogo de Extensiones Telefónicas</h5>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card-body">
            {{-- Filtros y búsqueda --}}
            <div class="d-flex justify-content-center mb-3">
                <div style="max-width:750px; width:100%;">
                    {{-- Formulario de importación --}}
                    <div class="card mb-3">
                        <div class="card-body py-3">
                            <form action="{{ route('admin.extensiones.importar') }}"
                                method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row align-items-end g-2">
                                    <div class="col-md-6">
                                        <label class="form-label mb-1">
                                            <i class="bi bi-file-earmark-excel-fill me-1"></i>
                                            Seleccionar archivo Excel
                                        </label>
                                        <input type="file"
                                            name="archivo"
                                            class="form-control form-control-sm"
                                            accept=".xlsx,.xls,.csv"
                                            required>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="bi bi-upload me-1"></i>
                                            Importar Excel
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="{{ asset('assets/plantillas/ejemplo_extensiones_dif2026.xlsx') }}"
                                            class="btn btn-success btn-sm w-100"
                                            download>
                                            <i class="bi bi-download me-1"></i>
                                            Plantilla
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de extensiones --}}
            <div class="d-flex justify-content-center mt-4">
                <div style="max-width: 950px; width:100%;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width:10%">ID</th>
                                    <th>Nombre extensión</th>
                                    <th style="width:20%">Extensión</th>
                                    <th style="width:15%">Estado</th>
                                    <th style="width:20%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($extensiones as $ext)
                                <tr class="{{ $ext->trashed() ? 'table-secondary' : '' }}">
                                    <td>{{ $ext->id }}</td>
                                    <td>
                                        {{ $ext->nombre_extension }}
                                        @if($ext->trashed())
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-x"></i>
                                            Inactivado: {{ $ext->deleted_at->format('d/m/Y H:i') }}
                                        </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $ext->extension }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($ext->trashed())
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i> Inactivo
                                        </span>
                                        @else
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i> Activo
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm {{ $ext->trashed() ? 'btn-success' : 'btn-warning' }} btn-toggle-status"
                                            title="{{ $ext->trashed() ? 'Activar' : 'Inactivar' }}"
                                            data-id="{{ $ext->id }}"
                                            data-nombre="{{ $ext->nombre_extension }}"
                                            data-extension="{{ $ext->extension }}"
                                            data-status="{{ $ext->trashed() ? 'inactive' : 'active' }}">
                                            <i class="bi {{ $ext->trashed() ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                                            {{ $ext->trashed() ? 'Activar' : 'Inactivar' }}
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No hay extensiones registradas
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Paginación --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Mostrando {{ $extensiones->firstItem() }} a {{ $extensiones->lastItem() }} de {{ $extensiones->total() }} registros
                </small>
                <nav>
                    {{ $extensiones->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
    </div>
</div>

{{-- Modal para confirmar cambio de estado --}}
<div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white" id="toggleModalHeader">
                <h5 class="modal-title" id="toggleStatusModalLabel">Confirmar cambio de estado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-question-circle-fill text-warning" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center fs-5" id="toggleStatusMessage">
                    ¿Estás seguro de que deseas cambiar el estado de la extensión?
                </p>
                <div class="alert alert-info" id="extensionInfo" style="display: none;">
                    <strong>Extensión:</strong> <span id="extensionNombre"></span><br>
                    <strong>Número:</strong> <span id="extensionNumero"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn" id="confirmToggleStatus">Sí, cambiar</button>
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
    
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    
    .table-secondary {
        opacity: 0.8;
        background-color: #f8f9fa !important;
    }
    
    .btn-success {
        background-color: #198754;
        border-color: #198754;
    }
    
    .btn-success:hover {
        background-color: #157347;
        border-color: #146c43;
    }
    
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
    }
    
    .btn-warning:hover {
        background-color: #ffca2c;
        border-color: #ffc720;
        color: #000;
    }
</style>

@section('scripts')
<script>
$(document).ready(function() {
    let toggleExtensionId = null;
    let toggleExtensionNombre = null;
    let toggleExtensionNumero = null;
    let toggleCurrentStatus = null;
    
    $(document).on('click', '.btn-toggle-status', function() {
        toggleExtensionId = $(this).data('id');
        toggleExtensionNombre = $(this).data('nombre');
        toggleExtensionNumero = $(this).data('extension');
        toggleCurrentStatus = $(this).data('status');
        
        const action = toggleCurrentStatus === 'active' ? 'inactivar' : 'activar';
        const actionText = toggleCurrentStatus === 'active' ? 'inactivar' : 'activar';
        const headerClass = toggleCurrentStatus === 'active' ? 'bg-warning' : 'bg-success';
        
        $('#toggleModalHeader').removeClass('bg-warning bg-success').addClass(headerClass);
        $('#toggleStatusMessage').html(`¿Estás seguro de que deseas <strong>${action}</strong> la extensión?`);
        $('#extensionNombre').text(toggleExtensionNombre);
        $('#extensionNumero').text(toggleExtensionNumero);
        $('#extensionInfo').show();
        
        $('#confirmToggleStatus')
            .removeClass('btn-warning btn-success')
            .addClass(toggleCurrentStatus === 'active' ? 'btn-warning' : 'btn-success')
            .text(`Sí, ${actionText}`);
        
        $('#toggleStatusModal').modal('show');
    });

    $('#confirmToggleStatus').click(function() {
        if (!toggleExtensionId) return;
        
        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...');
        
        $.ajax({
            url: '{{ route("admin.extensiones.toggle-active", ":id") }}'.replace(':id', toggleExtensionId),
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#toggleStatusModal').modal('hide');
                    
                    showToast('success', response.message);
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            },
            error: function(xhr) {
                showToast('error', xhr.responseJSON?.message || 'Error al cambiar el estado de la extensión');
            },
            complete: function() {
                btn.prop('disabled', false).html('Sí, cambiar');
                toggleExtensionId = null;
                toggleExtensionNombre = null;
                toggleExtensionNumero = null;
                toggleCurrentStatus = null;
            }
        });
    });

    $('#toggleStatusModal').on('hidden.bs.modal', function() {
        toggleExtensionId = null;
        toggleExtensionNombre = null;
        toggleExtensionNumero = null;
        toggleCurrentStatus = null;
        $('#extensionInfo').hide();
    });

    function showToast(type, message) {
        $('.custom-toast').remove();
        
        const toast = $(`
            <div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show custom-toast" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 99999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        
        $('body').append(toast);
        
        setTimeout(() => {
            toast.fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);
    }

    $('input[name="search"]').keypress(function(e) {
        if (e.which === 13) {
            $(this).closest('form').submit();
        }
    });
});
</script>
@endsection
@endsection