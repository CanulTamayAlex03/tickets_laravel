{{-- Modal para Reasignar Ticket - Versión Simplificada --}}
<div class="modal fade" id="reasignarSolicitudModal" tabindex="-1" aria-labelledby="reasignarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #210240;">
                <h5 class="modal-title" id="reasignarModalLabel">
                    <i class="bi bi-person-rolodex me-2"></i>Reasignar Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reasignarForm">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="ticket_id" id="reasignar_ticket_id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ticket #<span id="reasignar_ticket_number"></span></label>
                        <small class="text-muted d-block">Actual: <span id="reasignar_current_support" class="text-dark">—</span></small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reasignar_support_personal_id" class="form-label">
                            <i class="bi bi-person-plus me-1"></i>Reasignar a:
                        </label>
                        <select class="form-select" 
                                id="reasignar_support_personal_id" 
                                name="support_personal_id"
                                required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($supportPersonals as $personal)
                                <option value="{{ $personal->id }}">
                                    {{ $personal->name }} {{ $personal->lastnames }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info" id="btnConfirmarReasignacion">
                    <i class="bi bi-check-circle me-1"></i>Reasignar
                </button>
            </div>
        </div>
    </div>
</div>