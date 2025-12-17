@extends('layouts.app')

@section('content')
<div class="container-fluid d-flex mt-4 tickets-container">
    <div class="row justify-content-center w-100 m-0">
        <div class="col-md-10 col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm" style="background-color: rgba(255, 255, 255, 0.9);">
                <div class="card-header bg-dark text-white py-2">
                    <h5 class="mb-0 text-center">TICKETS</h5>
                </div>
                <div class="card-body p-4">
                    <h6 class="text-center fw-bold mb-4">NUEVA SOLICITUD</h6>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('tickets.store') }}" method="POST" class="compact-form">
                        @csrf

                        <!-- Select de Edificio -->
                        <div class="row g-2 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Edificio:</label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-select form-select-sm" id="buildingSelect" name="building_id" required data-placeholder="Buscar edificio...">
                                    <option value=""></option>
                                    @foreach($buildings as $building)
                                        <option value="{{ $building->id }}">{{ $building->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Select de Departamento -->
                        <div class="row g-2 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Departamento:</label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-select form-select-sm" id="departmentSelect" name="department_id" required data-placeholder="Buscar departamento...">
                                    <option value=""></option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Select de Empleado -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Solicita: </label>
                            <select class="form-select form-select-sm" id="employeeSelect" name="employee_id" required data-placeholder="Buscar por: nombres, apellidos, No. nómina...">
                                <option value=""></option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->full_name }} ({{ $employee->no_nomina }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Descripción:</label>
                            <textarea class="form-control form-control-sm" rows="4" name="description" required placeholder="Describa el problema o solicitud"></textarea>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Limpiar
                            </a>
                            <div>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-send"></i> Enviar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botón de validación de internet -->
<div class="internet-validation-btn" id="internetValidationBtn">
    <i class="bi bi-wifi"></i>
    <span>Internet</span>
</div>

<!-- Botón de correo -->
<div class="email-btn" id="emailBtn">
    <i class="bi bi-envelope"></i>
    <span>Correo Institucional</span>
</div>


<div class="validation-options" id="validationOptions">
    <a href="http://172.16.4.94:1000/portal?" target="_blank">Opción 1</a>
    <a href="http://172.16.1.45:1000/portal?" target="_blank">Opción 2</a>
</div>

<!-- jQuery y Select2 -->
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/es.js') }}"></script>

<script>
$(document).ready(function() {
    $('.form-select-sm').select2({
        placeholder: function() {
            return $(this).data('placeholder');
        },
        width: '100%',
        language: 'es',
        minimumResultsForSearch: 3
    });

    $(document).on('select2:open', '.form-select-sm', function(e) {
        setTimeout(function() {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        }, 10);
    });

    const internetValidationBtn = document.getElementById('internetValidationBtn');
        const validationOptions = document.getElementById('validationOptions');
        const optionLinks = validationOptions.querySelectorAll('a');

        internetValidationBtn.addEventListener('click', function() {
            validationOptions.classList.toggle('show');
        });
        optionLinks.forEach(link => {
            link.addEventListener('click', function() {
                validationOptions.classList.remove('show');
            });
        });
        document.addEventListener('click', function(event) {
            if (!internetValidationBtn.contains(event.target) && !validationOptions.contains(event.target)) {
                validationOptions.classList.remove('show');
            }
        });
        const emailBtn = document.getElementById('emailBtn');
        emailBtn.addEventListener('click', function() {
            window.open('http://mail.yucatan.gob.mx/', '_blank');
        });
    });
</script>

<style>
    body, html {
        height: 100%;
        margin: 0;
        padding: 0;
    }
    
    .tickets-container {
        background: url('/images/tickets_fondo.jpg') no-repeat center center fixed;
        background-size: cover;
        min-height: 100vh;
        padding: 20px;
    }
    
    .compact-form .form-label {
        margin-bottom: 0.2rem;
        font-size: 0.9rem;
    }
    .compact-form .form-control,
    .compact-form .form-select {
        padding: 0.25rem 0.5rem;
        font-size: 0.9rem;
    }
    
    .card { 
        border-radius: 5px; 
        margin-top: 10px;
        margin-bottom: 20px;
    }
    .card-header { 
        border-radius: 5px 5px 0 0 !important; 
    }
    
    .select2-container .select2-selection--single {
        height: calc(1.5em + .5rem + 2px) !important;
        padding: 0.25rem 0.5rem;
        font-size: 0.9rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .5rem + 2px) !important;
    }
</style>
@endsection