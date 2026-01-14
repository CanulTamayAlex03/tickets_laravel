<div class="modal fade" id="seguimientoModal" tabindex="-1" aria-labelledby="seguimientoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: #f8fafc;">
            <div class="modal-header text-white py-2" style="background-color: #210240;">
                <h5 class="modal-title fs-6 mb-0" id="seguimientoModalLabel">
                    <i class="bi bi-chat-left-text me-2"></i>Seguimiento de Solicitud
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-3" style="background-color: #ffffff;">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">
                            <i class="bi bi-info-circle me-2"></i>Información de la Solicitud
                        </h6>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-12">
                                <label class="form-label fw-bold small mb-1">Solicita:</label>
                                <div class="bg-light p-2 rounded small" id="seguimiento_employee_name">—</div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-12">
                                <label class="form-label fw-bold small mb-1">Descripción:</label>
                                <div class="bg-light p-2 rounded small" id="seguimiento_description">—</div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Edificio:</label>
                                <div class="bg-light p-2 rounded small" id="seguimiento_building">—</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Departamento:</label>
                                <div class="bg-light p-2 rounded small" id="seguimiento_department">—</div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Fecha recepción:</label>
                                <div class="bg-light p-2 rounded small" id="seguimiento_created_at">—</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Fecha liberación:</label>
                                <div class="bg-light p-2 rounded small" id="seguimiento_support_closing">—</div>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Estado:</label>
                                <div class="bg-light p-2 rounded small" id="seguimiento_status">—</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Asignado a:</label>
                                <div class="bg-light p-2 rounded small" id="seguimiento_assigned_to">—</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6 class="fw-bold border-bottom pb-2 mb-2 text-dark">
                                <i class="bi bi-gear me-2"></i>Seguimiento Técnico
                            </h6>
                            
                            <div class="text-end mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-agregar-seguimiento-simple">
                                    <i class="bi bi-plus-circle me-1"></i> Agregar Seguimiento
                                </button>
                            </div>

                            <div id="lista_seguimientos_simple" class="mt-2">
                                <h6 class="fw-bold small mb-2">Seguimientos anteriores:</h6>
                                <div id="seguimientos_container_simple" class="small">
                                    <div class="alert alert-info">Cargando seguimientos...</div>
                                </div>
                            </div>

                            <div id="form_nuevo_seguimiento_simple" class="mt-3" style="display: none;">
                                <div class="card border-success">
                                    <div class="card-header text-white py-1" style="background-color: #210240;">
                                        <h6 class="mb-0 small">Nuevo Seguimiento</h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <input type="hidden" id="seguimiento_ticket_id" name="ticket_id">
                                        <div class="mb-2">
                                            <label for="nuevo_seguimiento_simple" class="form-label fw-bold small">Descripción:</label>
                                            <textarea class="form-control form-control-sm" id="nuevo_seguimiento_simple" name="nuevo_seguimiento_simple" 
                                                      rows="3" placeholder="Escriba aquí el seguimiento técnico..."></textarea>
                                        </div>
                                        <div class="text-end">
                                            <button type="button" class="btn btn-sm btn-secondary btn-cancelar-seguimiento-simple">
                                                Cancelar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success btn-guardar-seguimiento-simple">
                                                <i class="bi bi-check-circle me-1"></i> Guardar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2" style="background-color: #f8fafc;">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
#seguimientoModal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

#seguimientoModal .bg-light {
    background-color: #f8f9fa !important;
    font-size: 0.8rem;
}

#seguimientoModal h6 {
    font-size: 0.9rem;
}

#seguimientos_container_simple {
    background-color: #f8fafc;
    border-radius: 8px;
    padding: 12px;
    border: 1px solid #e2e8f0;
    max-height: 300px;
    overflow-y: auto;
}

#seguimientos_container_simple .card {
    background-color: white;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

#seguimientos_container_simple .card-body {
    padding: 10px;
}

#seguimientos_container_simple .card p {
    background-color: #f1f5f9;
    padding: 8px;
    border-radius: 4px;
    margin-top: 6px;
    font-size: 0.85rem;
    border-left: 3px solid #3a86ff;
}


#seguimientoModal ::-webkit-scrollbar {
    width: 6px;
}

#seguimientoModal ::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#seguimientoModal ::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#seguimientoModal ::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endpush
