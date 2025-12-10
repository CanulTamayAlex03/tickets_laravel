@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white py-2 text-center">
            <h5 class="mb-0">Mis Solicitudes - En Atención</h5>
        </div>
        <div class="card-body tabla-container p-3">

            @if(!request()->has('employee_search'))
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Ingresa tus datos para buscar tus solicitudes en atención
            </div>
            @endif

            <div class="row mb-4 justify-content-center">
                <div class="col-md-8">
                    <form action="{{ route('mis_solicitudes') }}" method="GET" class="row g-3">
                        <div class="col-md-8">
                            <label for="employee_search" class="form-label fw-bold">Buscar mis solicitudes</label>
                            <select class="form-select select2-employee" 
                                   id="employee_search" name="employee_search"
                                   data-placeholder="Buscar por: nombres, apellidos, No. nómina...">
                                <option value=""></option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->full_name }}" 
                                            {{ request('employee_search') == $employee->full_name ? 'selected' : '' }}>
                                        {{ $employee->full_name }} ({{ $employee->no_nomina }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small">
                                Solo se mostrarán tus solicitudes que están en proceso de atención
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="d-grid gap-2 w-100">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-search me-1"></i> Buscar
                                </button>
                                @if(request()->has('employee_search'))
                                <a href="{{ route('mis_solicitudes') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-x-circle me-1"></i> Limpiar
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(request()->has('employee_search'))
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead class="table-primary small">
                        <tr>
                            <th width="5%">ID</th>
                            <th>Descripción</th>
                            <th width="25%">Información</th>
                            <th>Fecha Recepción</th>
                            <th>Estatus</th>
                            <th width="12%" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>
                            <td>{{ $ticket->description }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $ticket->employee?->full_name ?? '—' }}</strong>
                                    <small class="text-muted">
                                        <i class="bi bi-building"></i>
                                        {{ $ticket->building?->description ?? 'Sin edificio' }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="bi bi-diagram-3"></i>
                                        {{ $ticket->department?->description ?? 'Sin departamento' }}
                                    </small>
                                </div>
                            </td>
                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $ticket->serviceStatus->description ?? 'Atendiendo' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-success btn-sm liberar-ticket" 
                                        data-ticket-id="{{ $ticket->id }}">
                                    <i class="bi bi-check-circle"></i> Liberar
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                <i class="bi bi-search"></i>  
                                No se encontraron solicitudes en atención con tus datos
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tickets->count() > 0)
            <div class="d-flex justify-content-between align-items-center mt-2">
                <small class="text-muted">
                    Mostrando {{ $tickets->firstItem() }} a {{ $tickets->lastItem() }} de {{ $tickets->total() }} registros
                </small>
                {{ $tickets->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
            @endif
            @endif
        </div>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="liberarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded">
            <div class="modal-header text-white py-2" style="background-color: #210240;">
                <h6 class="modal-title mb-0">
                    <i class="bi bi-check-circle me-2"></i>¿Confirmar liberación de solicitud?
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="liberarForm">
                @csrf
                <div class="modal-body p-3">

                    <div class="mb-2">
                        <label class="form-label fw-bold small">Solicitud #<span id="modal_ticket_id"></span></label>
                        <div class="card bg-light">
                            <div class="card-body py-2 small">
                                <div><strong>Usuario:</strong> <span id="modal_usuario_text">—</span></div>
                                <div><strong>Edificio:</strong> <span id="modal_edificio_text">—</span></div>
                                <div><strong>Departamento:</strong> <span id="modal_departamento_text">—</span></div>
                                <div><strong>Fecha liberación:</strong> <span id="modal_fecha_liberacion_text">—</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small">Descripción</label>
                        <div class="card bg-light">
                            <div class="card-body small" id="modal_descripcion_text" style="max-height: 80px; overflow-y:auto;">
                                —
                            </div>
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="mb-2">
                        <label class="form-label fw-bold small">Retroalimentación</label>
                        <textarea class="form-control form-control-sm" id="retroalimentation" name="retroalimentation"
                                  rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small mb-1">Calificación del Servicio *</label>
                        <div class="rating-stars-horizontal">
                            <input type="radio" id="star1" name="stars" value="1" required>
                            <label for="star1" class="star">★</label>

                            <input type="radio" id="star2" name="stars" value="2">
                            <label for="star2" class="star">★</label>

                            <input type="radio" id="star3" name="stars" value="3">
                            <label for="star3" class="star">★</label>

                            <input type="radio" id="star4" name="stars" value="4">
                            <label for="star4" class="star">★</label>

                            <input type="radio" id="star5" name="stars" value="5">
                            <label for="star5" class="star">★</label>
                        </div>
                        <small class="text-muted">Selecciona de 1 a 5 estrellas</small>
                    </div>
                </div>

                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-check-circle"></i> Liberar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {

    $('.select2-employee').select2({
        placeholder: function() { return $(this).data('placeholder'); },
        width: '100%',
        language: 'es',
        minimumResultsForSearch: 3,
    });
    $(document).on('select2:open', '.select2-employee', function(e) {
        setTimeout(function() {
            document.querySelector('.select2-container--open .select2-search__field').focus();
        }, 10);
    });

    let currentTicketId = null;

    $(document).on('click', '.liberar-ticket', function() {
        currentTicketId = $(this).data('ticket-id');

        $('#liberarModal').modal('show');

        $.ajax({
            url: `/mis-solicitudes/${currentTicketId}/detalles`,
            method: 'GET',
            success: function(response) {
                if(response.success) {
                    const t = response.ticket;

                    $('#modal_ticket_id').text(t.id);
                    $('#modal_usuario_text').text(t.employee ? `${t.employee.no_nomina || 'Sin nómina'} - ${t.employee.full_name || 'Sin nombre'}` : 'Sin usuario');
                    $('#modal_edificio_text').text(t.building?.description || 'Sin edificio');
                    $('#modal_departamento_text').text(t.department?.description || 'Sin departamento');
                    $('#modal_fecha_liberacion_text').text(new Date().toLocaleString('es-MX'));
                    $('#modal_descripcion_text').text(t.description || 'Sin descripción');

                    $('#retroalimentation').val('');
                    $('.rating-stars-horizontal .star').removeClass('active hover');
                    $('.rating-stars-horizontal input').prop('checked', false);
                }
            }
        });
    });

    // ✅ FIX CORRECTO PARA CLICK IZQ → DER
    $(document).on('change', '.rating-stars-horizontal input', function() {
        const value = parseInt($(this).val());
        $('.rating-stars-horizontal .star').removeClass('active');

        $('.rating-stars-horizontal input').each(function() {
            if (parseInt($(this).val()) <= value) {
                $(this).next('.star').addClass('active');
            }
        });
    });

    // Hover
    $(document).on('mouseenter', '.rating-stars-horizontal .star', function() {
        $(this).addClass('hover');
        $(this).prevAll('.star').addClass('hover');
    });

    $(document).on('mouseleave', '.rating-stars-horizontal', function() {
        $('.rating-stars-horizontal .star').removeClass('hover');
    });

    $('#liberarForm').submit(function(e) {
        e.preventDefault();
        if(!$('input[name="stars"]:checked').val()) {
            alert('Selecciona una calificación.');
            return;
        }

        $.ajax({
            url: `/mis-solicitudes/${currentTicketId}/confirmar-liberacion`,
            method: 'POST',
            data: $(this).serialize(),
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(res) {
                if(res.success) {
                    alert(res.message);
                    $('#liberarModal').modal('hide');
                    location.reload();
                }
            }
        });
    });
});
</script>

<style>
.rating-stars-horizontal {
    display:flex;
    justify-content:center;
    gap:5px;
}
.rating-stars-horizontal input {display:none;}
.rating-stars-horizontal .star {
    font-size:2rem;
    color:#ddd;
    cursor:pointer;
    transition:transform 0.2s ease;
}
.rating-stars-horizontal .star.hover,
.rating-stars-horizontal .star:hover {
    color:#ffc107;
    transform:scale(1.15);
}
.rating-stars-horizontal .star.active {
    color:#ffc107;
}
#liberarModal .modal-dialog { max-width:500px; }
#modal_descripcion_text {line-height:1.4;}

#liberarModal .modal-content {
    background-color: #f2f2f2;
}

.tabla-container {
    background-color: #f8f8f8ff;
    padding: 15px;
    border-radius: 6px;
}
</style>

@endsection
