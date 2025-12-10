<div class="modal fade" id="editarSolicitudModal" tabindex="-1" aria-labelledby="editarSolicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: #f8fafc;">
            <div class="modal-header text-white py-2" style="background-color: #210240;">
                <h5 class="modal-title fs-6 mb-0" id="editarSolicitudModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Editar Solicitud
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editarSolicitudForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_ticket_id" name="ticket_id">
                
                <div class="modal-body p-3" style="background-color: #ffffff;">
                    <div class="row g-3">
                        <!-- Columna izquierda: Información básica -->
                        <div class="col-md-6">
                            <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">
                                <i class="bi bi-info-circle me-2"></i>Información de la Solicitud
                            </h6>
                            
                            <!-- Asignar a -->
                            <div class="mb-3">
                                <label for="edit_support_personal_id" class="form-label fw-bold small">Asignar a:</label>
                                <select class="form-select form-select-sm select2-support-personal-edit" id="edit_support_personal_id" name="support_personal_id">
                                    <option value="">Seleccionar personal de soporte</option>
                                    @foreach($supportPersonals as $personal)
                                        <option value="{{ $personal->id }}">
                                            {{ $personal->name }} {{ $personal->lastnames }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Información en modo lectura -->
                            <div class="row g-2 mb-2">
                                <div class="col-12">
                                    <label class="form-label fw-bold small mb-1">Solicita:</label>
                                    <div class="bg-light p-2 rounded small" id="edit_employee_name">—</div>
                                </div>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-12">
                                    <label class="form-label fw-bold small mb-1">Descripción:</label>
                                    <div class="bg-light p-2 rounded small" id="edit_description">—</div>
                                </div>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label fw-bold small mb-1">Edificio:</label>
                                    <div class="bg-light p-2 rounded small" id="edit_building">—</div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small mb-1">Departamento:</label>
                                    <div class="bg-light p-2 rounded small" id="edit_department">—</div>
                                </div>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label fw-bold small mb-1">Fecha recepción:</label>
                                    <div class="bg-light p-2 rounded small" id="edit_created_at">—</div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small mb-1">Fecha liberación:</label>
                                    <div class="bg-light p-2 rounded small" id="edit_support_closing">—</div>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <label class="form-label fw-bold small mb-1">Retroalimentación:</label>
                                <div class="bg-light p-2 rounded small" id="edit_retroalimentation">—</div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold small mb-1">Calificación:</label>
                                <div class="bg-light p-2 rounded small" id="edit_stars">
                                    —
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Edición y seguimiento -->
                        <div class="col-md-6">
                            <!-- Seguimiento Técnico -->
                            <div class="mb-3">
                                <h6 class="fw-bold border-bottom pb-2 mb-2 text-dark">
                                    <i class="bi bi-gear me-2"></i>Seguimiento Técnico
                                </h6>
                                
                                <!-- Botón para agregar seguimiento -->
                                <div class="text-end mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-agregar-seguimiento">
                                        <i class="bi bi-plus-circle me-1"></i> Agregar Seguimiento
                                    </button>
                                </div>

                                <div id="lista_seguimientos" class="mt-2">
                                    <h6 class="fw-bold small mb-2">Seguimientos anteriores:</h6>
                                    <div id="seguimientos_container" class="small">
                                    </div>
                                </div>

                                <div id="form_nuevo_seguimiento" class="mt-3" style="display: none;">
                                    <div class="card border-success">
                                        <div class="card-header text-white py-1" style="background-color: #210240;">
                                            <h6 class="mb-0 small">Nuevo Seguimiento</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="mb-2">
                                                <label for="nuevo_seguimiento" class="form-label fw-bold small">Descripción:</label>
                                                <textarea class="form-control form-control-sm" id="nuevo_seguimiento" name="nuevo_seguimiento" 
                                                          rows="3" placeholder="Escriba aquí el seguimiento técnico..."></textarea>
                                            </div>
                                            <div class="text-end">
                                                <button type="button" class="btn btn-sm btn-secondary btn-cancelar-seguimiento">
                                                    Cancelar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-success btn-guardar-seguimiento">
                                                    <i class="bi bi-check-circle me-1"></i> Guardar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Indicador y Servicio -->
                            <div class="mb-3">
                                <h6 class="fw-bold border-bottom pb-2 mb-2 text-dark">
                                    <i class="bi bi-tags me-2"></i>Clasificación
                                </h6>
                                
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="edit_indicator_type_id" class="form-label fw-bold small">Indicador:</label>
                                        <select class="form-select form-select-sm" id="edit_indicator_type_id" name="indicator_type_id">
                                            <option value="">Seleccionar</option>
                                            @foreach($indicatorTypes as $indicator)
                                                <option value="{{ $indicator->id }}">
                                                    {{ $indicator->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="edit_another_service_id" class="form-label fw-bold small">Servicio:</label>
                                        <select class="form-select form-select-sm" id="edit_another_service_id" name="another_service_id" disabled>
                                            <option value="">Primero seleccione un indicador</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Solo el campo de equipo -->
                                <div class="row g-2 mt-2">
                                    <div class="col-12">
                                        <label for="edit_equipment_id" class="form-label fw-bold small">Equipo:</label>
                                        <select class="form-select form-select-sm" id="edit_equipment_id" name="equipment_id">
                                            <option value="">Seleccionar equipo</option>
                                            @foreach($equipmentList as $equipment)
                                                <option value="{{ $equipment->id }}">
                                                    {{ $equipment->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Actividad realizada y Estado -->
                            <div>
                                <h6 class="fw-bold border-bottom pb-2 mb-2 text-dark">
                                    <i class="bi bi-clipboard-check me-2"></i>Cierre de Solicitud
                                </h6>
                                
                                <!-- Actividad realizada -->
                                <div class="mb-2">
                                    <label for="edit_activity_description" class="form-label fw-bold small">Actividad realizada:</label>
                                    <textarea class="form-control form-control-sm" id="edit_activity_description" 
                                              name="activity_description" rows="2" 
                                              placeholder="Describa las actividades realizadas..."></textarea>
                                </div>

                                <!-- Solo estado, sin checkbox -->
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label for="edit_service_status_id" class="form-label fw-bold small">Estado:</label>
                                        <select class="form-select form-select-sm" id="edit_service_status_id" name="service_status_id">
                                            @foreach($serviceStatuses as $status)
                                                <option value="{{ $status->id }}">
                                                    {{ $status->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2" style="background-color: #f8fafc;">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-success">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
#editarSolicitudModal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

#editarSolicitudModal .form-control-sm,
#editarSolicitudModal .form-select-sm {
    font-size: 0.85rem;
    padding: 0.25rem 0.5rem;
    min-height: 36px;
}

#editarSolicitudModal .bg-light {
    background-color: #f8f9fa !important;
    font-size: 0.85rem;
    min-height: 36px;
    display: flex;
    align-items: center;
    padding: 0.4rem 0.5rem;
}

#editarSolicitudModal .form-label {
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

#editarSolicitudModal h6 {
    font-size: 0.9rem;
}

#editarSolicitudModal ::-webkit-scrollbar {
    width: 6px;
}

#editarSolicitudModal ::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#editarSolicitudModal ::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#editarSolicitudModal ::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

#seguimientos_container {
    background-color: #f8fafc;
    border-radius: 8px;
    padding: 12px;
    border: 1px solid #e2e8f0;
}

#seguimientos_container .card {
    background-color: white;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

#seguimientos_container .card-body {
    padding: 10px;
}

#seguimientos_container .card p {
    background-color: #f1f5f9;
    padding: 8px;
    border-radius: 4px;
    margin-top: 6px;
    font-size: 0.85rem;
    border-left: 3px solid #3a86ff;
}


.star-rating {
    color: #ffc107;
    font-size: 1.1rem;
}
</style>
@endpush