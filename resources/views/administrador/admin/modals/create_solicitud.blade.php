<div class="modal fade" id="createSolicitudModal" tabindex="-1" aria-labelledby="createSolicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white py-2" style="background-color: #210240;">
                <h5 class="modal-title" id="createSolicitudModalLabel">NUEVA SOLICITUD</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('tickets.store') }}" method="POST" class="compact-form" id="createTicketForm">
                    @csrf
                    <input type="hidden" name="origin" value="admin">
                    
                    <div class="row g-2 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Edificio:</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-select form-select-sm modal-select" id="modalBuildingSelect" name="building_id" required data-placeholder="Buscar edificio...">
                                <option value=""></option>
                                @foreach($buildings as $building)
                                    <option value="{{ $building->id }}">{{ $building->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Departamento:</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-select form-select-sm modal-select" id="modalDepartmentSelect" name="department_id" required data-placeholder="Buscar departamento...">
                                <option value=""></option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="mb-4">
                        <label class="form-label fw-bold">Solicita: </label>
                        <select class="form-select form-select-sm modal-select" id="modalEmployeeSelect" name="employee_id" required data-placeholder="Buscar por: nombres, apellidos, No. nómina...">
                            <option value=""></option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->full_name }} ({{ $employee->no_nomina }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Descripción:</label>
                        <textarea class="form-control form-control-sm" rows="4" name="description" required placeholder="Describa el problema o solicitud"></textarea>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <div>
                            <button type="submit" class="btn btn-sm btn-primary" id="submitTicketBtn">
                                <i class="bi bi-send"></i> Enviar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/es.js') }}"></script>

<style>
    .compact-form .form-label {
        margin-bottom: 0.2rem;
        font-size: 0.9rem;
    }
    .compact-form .form-control,
    .compact-form .form-select {
        padding: 0.25rem 0.5rem;
        font-size: 0.9rem;
    }
    
    .select2-container .select2-selection--single {
        height: calc(1.5em + .5rem + 2px) !important;
        padding: 0.25rem 0.5rem;
        font-size: 0.9rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .5rem + 2px) !important;
    }
    
    .select2-container {
        z-index: 1060;
    }
    
    .fade-out {
        transition: opacity 0.5s ease-in-out;
        opacity: 0;
    }
</style>

<script>
$(document).ready(function() {
    $('#createSolicitudModal').on('shown.bs.modal', function() {
        $('.modal-select').select2({
            placeholder: function() {
                return $(this).data('placeholder');
            },
            width: '100%',
            language: 'es',
            minimumResultsForSearch: 3,
            dropdownParent: $('#createSolicitudModal')
        });
        
        $(document).on('select2:open', '.modal-select', function(e) {
            setTimeout(function() {
                const searchField = document.querySelector('.select2-container--open .select2-search__field');
                if (searchField) {
                    searchField.focus();
                }
            }, 10);
        });
    });

    $('#createSolicitudModal').on('hidden.bs.modal', function() {
        $('.modal-select').select2('destroy');
        $('#createTicketForm')[0].reset();
    });

        setTimeout(function() {
        $('.alert-dismissible').each(function() {
            $(this).addClass('fade-out');
            setTimeout(() => {
                $(this).alert('close');
            }, 500);
        });
    }, 7000);
});
</script>