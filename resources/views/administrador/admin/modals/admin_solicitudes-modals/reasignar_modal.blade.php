<div class="modal fade" id="reasignarSolicitudModal" tabindex="-1" aria-labelledby="reasignarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="reasignarModalLabel">
                    <i class="bi bi-person-rolodex me-2"></i>Reasignar Ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reasignarForm" action="{{ route('admin.solicitudes.update', ':ticket_id') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="ticket_id" id="reasignar_ticket_id">
                    
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-ticket-detailed me-1"></i>Información del Ticket</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-muted small">Usuario:</label>
                                    <p class="fw-bold" id="reasignar_employee_name">—</p>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-muted small">Edificio:</label>
                                    <p class="fw-bold" id="reasignar_building">—</p>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-muted small">Departamento:</label>
                                    <p class="fw-bold" id="reasignar_department">—</p>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-muted small">Fecha de creación:</label>
                                    <p class="fw-bold" id="reasignar_created_at">—</p>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label text-muted small">Descripción:</label>
                                    <p class="fw-bold" id="reasignar_description">—</p>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label text-muted small">Personal asignado actualmente:</label>
                                    <p class="fw-bold text-info" id="reasignar_current_support">—</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reasignar_support_personal_id" class="form-label">
                            <i class="bi bi-person-plus me-1"></i>Nuevo personal de soporte
                        </label>
                        <select class="form-select select2-reasignar" 
                                id="reasignar_support_personal_id" 
                                name="support_personal_id"
                                style="width: 100%;">
                            <option value="">-- Seleccionar personal --</option>
                            @foreach($supportPersonals as $personal)
                                <option value="{{ $personal->id }}">
                                    {{ $personal->name }} {{ $personal->lastnames }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            Selecciona el nuevo personal que atenderá este ticket.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reasignar_comentario" class="form-label">
                            <i class="bi bi-chat-text me-1"></i>Comentario de reasignación (opcional)
                        </label>
                        <textarea class="form-control" 
                                id="reasignar_comentario" 
                                name="reasignar_comentario" 
                                rows="3" 
                                placeholder="Ej: Reasignado debido a sobrecarga de trabajo..."></textarea>
                        <div class="form-text">
                            Este comentario se agregará automáticamente como seguimiento.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reasignar_service_status_id" class="form-label">
                            <i class="bi bi-flag me-1"></i>Estado del servicio
                        </label>
                        <select class="form-select" id="reasignar_service_status_id" name="service_status_id">
                            <option value="2">Atendiendo</option>
                            <option value="4">Pendiente</option>
                            <option value="3">Cerrado por usuario</option>
                            <option value="5">Completado</option>
                        </select>
                        <div class="form-text">
                            Puedes cambiar el estado si es necesario.
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Al reasignar el ticket, se notificará al nuevo personal asignado.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-info" id="btnConfirmarReasignacion">
                    <i class="bi bi-check-circle me-1"></i>Confirmar Reasignación
                </button>
            </div>
        </div>
    </div>
</div>