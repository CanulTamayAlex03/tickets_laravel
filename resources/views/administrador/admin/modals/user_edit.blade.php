<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Nueva Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="edit_password" name="password">
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="edit_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Dejar en blanco para no cambiar</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation">
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="edit_password_confirmation">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_role_id" class="form-label">Rol</label>
                        <select class="form-select" id="edit_role_id" name="role_id" required>
                            <option value="">Seleccionar rol</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_buildings" class="form-label">Edificios</label>
                        <select class="form-control select2-tags" id="edit_buildings" name="buildings[]" multiple="multiple">
                            @foreach($buildings as $building)
                            <option value="{{ $building->id }}">{{ $building->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="hidden" name="estatus" value="0">
                        <input type="checkbox" class="form-check-input" id="edit_estatus" name="estatus" value="1">
                        <label class="form-check-label" for="edit_estatus">Usuario activo</label>
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
    .form-select[multiple] {
        height: auto;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .toggle-password {
        border-left: 0;
    }
    
    .input-group .form-control:focus {
        z-index: 3;
    }
</style>