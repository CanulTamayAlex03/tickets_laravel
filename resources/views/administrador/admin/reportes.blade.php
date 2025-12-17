@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0"><i class="fas fa-file-excel me-2"></i>Reportes - Descargar Excel</h3>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.reportes.export') }}" method="POST" id="exportForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-dark mb-3"><i class="fas fa-calendar-alt me-2"></i>Rango de Fechas</h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="start_date" class="form-label">Fecha Inicial</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="end_date" class="form-label">Fecha Final</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">Estatus</label>
                                    <select class="form-select form-select-sm" id="status" name="status" 
                                            data-placeholder="Todos los estatus">
                                        <option value=""></option>
                                        <option value="nuevo">Nuevo</option>
                                        <option value="atendiendo">Atendiendo</option>
                                        <option value="cerrado">Cerrado</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="completado">Completado</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="support_personal_id" class="form-label">Personal de Soporte</label>
                                    <select class="form-select form-select-sm" id="support_personal_id" name="support_personal_id"
                                            data-placeholder="Todo el personal de soporte">
                                        <option value=""></option>
                                        @foreach($supportPersonnel as $person)
                                            <option value="{{ $person->id }}">
                                                {{ $person->name }} {{ $person->lastnames }}
                                                @if(!$person->active)
                                                    (Inactivo)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="text-dark mb-3"><i class="fas fa-filter me-2"></i>Filtros Adicionales</h5>
                                
                                <div class="mb-3">
                                    <label for="building_id" class="form-label">Edificio</label>
                                    <select class="form-select form-select-sm" id="building_id" name="building_id" 
                                            data-placeholder="Todos los edificios">
                                        <option value=""></option>
                                        @foreach($buildings as $building)
                                            <option value="{{ $building->id }}">{{ $building->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Departamento</label>
                                    <select class="form-select form-select-sm" id="department_id" name="department_id"
                                            data-placeholder="Todos los departamentos">
                                        <option value=""></option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->description }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-success btn-md" id="exportBtn">
                                <i class="fas fa-download me-2"></i>Descargar Reporte Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/es.js') }}"></script>

<script>
$(document).ready(function() {
    $('.form-select, .form-select-sm').each(function() {
        let placeholder = $(this).data('placeholder') || $(this).find('option:first').text();
        $(this).select2({
            placeholder: placeholder,
            allowClear: true,
            width: '100%',
            language: 'es',
            minimumResultsForSearch: 3
        });
    });

    $(document).on('select2:open', function() {
        setTimeout(() => {
            document.querySelector('.select2-container--open .select2-search__field')?.focus();
        }, 10);
    });

    function resetExportButton() {
        $('#exportBtn')
            .prop('disabled', false)
            .html('<i class="fas fa-download me-2"></i>Descargar Reporte Excel');
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
               document.querySelector('input[name="_token"]')?.value;
    }

    $('#exportForm').on('submit', function(e) {
        e.preventDefault();
        
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();

        if (startDate && endDate) {
            if (new Date(endDate) < new Date(startDate)) {
                alert('Error: La fecha final no puede ser anterior a la fecha inicial.');
                return false;
            }
        }

        $('#exportBtn')
            .prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i>Generando Excel...');

        const form = e.target;
        const formData = new FormData(form);
        const url = form.action;
        
        const csrfToken = getCsrfToken();
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error del servidor: ${response.status}`);
            }
            
            const contentDisposition = response.headers.get('content-disposition');
            let filename = 'reporte_tickets.xlsx';
            
            if (contentDisposition) {
                const filenameMatch = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (filenameMatch && filenameMatch[1]) {
                    filename = decodeURIComponent(filenameMatch[1].replace(/['"]/g, ''));
                }
            }
            
            return response.blob().then(blob => ({ blob, filename }));
        })
        .then(({ blob, filename }) => {
            const downloadUrl = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = downloadUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            
            setTimeout(() => {
                document.body.removeChild(a);
                window.URL.revokeObjectURL(downloadUrl);
            }, 100);
            
            resetExportButton();
            
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Excelente!',
                    text: 'El reporte se ha descargado correctamente.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }, 500);
        })
        .catch(error => {
            console.error('Error:', error);
            
            resetExportButton();
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo generar el reporte. Por favor, intente nuevamente.',
                confirmButtonText: 'Entendido'
            });
        });
    });

    $(window).on('beforeunload', function() {
        resetExportButton();
    });
});
</script>
@endpush

@push('styles')
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">

<style>
.btn-success {
    background-color: #28a745;
    border-color: #28a745;
    padding: 12px 30px;
    font-weight: 600;
    min-width: 250px;
}
.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.card-header {
    border-radius: 10px 10px 0 0 !important;
    font-size: 1.25rem;
}

.form-label { font-weight: 600; }

.alert-info {
    background-color: #e7f3fe;
    border-left: 4px solid #0066cc;
}

.select2-container .select2-selection--single {
    height: 38px !important;
    padding: 6px 12px !important;
    font-size: 0.95rem;
}
.select2-selection__arrow {
    height: 38px !important;
}
</style>
@endpush
