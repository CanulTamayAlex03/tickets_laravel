<div class="modal fade" id="verModal" tabindex="-1" aria-labelledby="verModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: #f8fafc;">
            <div class="modal-header text-white py-2" style="background-color: #210240;">
                <h5 class="modal-title fs-6 mb-0" id="verModalLabel">
                    <i class="bi bi-eye me-2"></i>Ver Solicitud #<span id="ver_ticket_id">—</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-3" style="background-color: #ffffff;">
                <div class="row g-3">
                    <!-- Columna izquierda: Información básica -->
                    <div class="col-md-6">
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-dark">
                            <i class="bi bi-info-circle me-2"></i>Información de la Solicitud
                        </h6>
                        
                        <!-- Asignado a -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Asignado a:</label>
                            <div class="bg-light p-2 rounded small" id="ver_support_personal">—</div>
                        </div>

                        <!-- Información en modo lectura -->
                        <div class="row g-2 mb-2">
                            <div class="col-12">
                                <label class="form-label fw-bold small mb-1">Solicita:</label>
                                <div class="bg-light p-2 rounded small" id="ver_employee_name">—</div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-12">
                                <label class="form-label fw-bold small mb-1">Descripción:</label>
                                <div class="bg-light p-2 rounded small" id="ver_description">—</div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Edificio:</label>
                                <div class="bg-light p-2 rounded small" id="ver_building">—</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Departamento:</label>
                                <div class="bg-light p-2 rounded small" id="ver_department">—</div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Fecha recepción:</label>
                                <div class="bg-light p-2 rounded small" id="ver_created_at">—</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small mb-1">Fecha liberación:</label>
                                <div class="bg-light p-2 rounded small" id="ver_support_closing">—</div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label fw-bold small mb-1">Retroalimentación:</label>
                            <div class="bg-light p-2 rounded small" id="ver_retroalimentation">—</div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small mb-1">Calificación:</label>
                            <div class="bg-light p-2 rounded small" id="ver_stars">
                                —
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: Seguimiento y clasificación -->
                    <div class="col-md-6">
                        <!-- Seguimiento Técnico -->
                        <div class="mb-3">
                            <h6 class="fw-bold border-bottom pb-2 mb-2 text-dark">
                                <i class="bi bi-gear me-2"></i>Seguimiento Técnico
                            </h6>

                            <div id="lista_seguimientos_ver" class="mt-2">
                                <h6 class="fw-bold small mb-2">Seguimientos:</h6>
                                <div id="seguimientos_container_ver" class="small">
                                    <div class="alert alert-info">Cargando seguimientos...</div>
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
                                    <label class="form-label fw-bold small">Indicador:</label>
                                    <div class="bg-light p-2 rounded small" id="ver_indicator">—</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Servicio:</label>
                                    <div class="bg-light p-2 rounded small" id="ver_service">—</div>
                                </div>
                            </div>
                            
                            <div class="row g-2 mt-2">
                                <div class="col-12">
                                    <label class="form-label fw-bold small">Equipo:</label>
                                    <div class="bg-light p-2 rounded small" id="ver_equipment">—</div>
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
                                <label class="form-label fw-bold small">Actividad realizada:</label>
                                <div class="bg-light p-2 rounded small" id="ver_activity">—</div>
                            </div>

                            <!-- Estado -->
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label fw-bold small">Estado de la solicitud:</label>
                                    <div class="bg-light p-2 rounded small" id="ver_status">—</div>
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
#verModal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

#verModal .bg-light {
    background-color: #f8f9fa !important;
    font-size: 0.85rem;
    min-height: 36px;
    display: flex;
    align-items: center;
    padding: 0.4rem 0.5rem;
}

#verModal .form-label {
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

#verModal h6 {
    font-size: 0.9rem;
}

#verModal ::-webkit-scrollbar {
    width: 6px;
}

#verModal ::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#verModal ::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#verModal ::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

#seguimientos_container_ver {
    background-color: #f8fafc;
    border-radius: 8px;
    padding: 12px;
    border: 1px solid #e2e8f0;
}

#seguimientos_container_ver .seguimiento-item {
    background-color: white;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

#seguimientos_container_ver .seguimiento-body {
    padding: 10px;
}

#seguimientos_container_ver .seguimiento-desc {
    background-color: #f1f5f9;
    padding: 8px;
    border-radius: 4px;
    margin-top: 6px;
    font-size: 0.85rem;
    border-left: 3px solid #3a86ff;
}

#seguimientos_container_ver .seguimiento-item:last-child .seguimiento-desc {
    border-left: 4px solid #3a86ff;
}

.star-rating {
    color: #ffc107;
    font-size: 1.1rem;
}

.badge-estado {
    font-size: 0.75rem;
    padding: 0.3em 0.6em;
    font-weight: 600;
}
</style>
@endpush