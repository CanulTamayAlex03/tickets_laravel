<div class="modal fade" id="eliminarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-trash me-2"></i> Eliminar ticket
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p class="mb-2">
                    ¿Seguro que deseas eliminar el ticket
                    <strong>#<span id="ticketId"></span></strong>?
                </p>
                <p class="text-muted mb-3" id="ticketDescripcion"></p>

                <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input me-2" type="checkbox" id="confirmarEliminar">
                    <label class="form-check-label">
                        Confirmar eliminación
                    </label>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button class="btn btn-danger" id="btnEliminar" disabled>
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>

        </div>
    </div>
</div>
