<div class="modal fade" id="editPersonalModal" tabindex="-1" aria-labelledby="editPersonalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="editPersonalModalLabel">Editar Personal de Soporte</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lastnames" class="form-label">Apellidos</label>
                        <input type="text" class="form-control" id="edit_lastnames" name="lastnames" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="hidden" name="active" value="0">
                        <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1">
                        <label class="form-check-label" for="edit_active">Activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .form-switch .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>