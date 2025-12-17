<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="addUserModalLabel">Agregar Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Rol</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="">Seleccionar rol</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="buildings" class="form-label">Edificios</label>
                        <select
                            class="form-control select2-tags"
                            id="buildings"
                            name="buildings[]"
                            multiple="multiple">
                            @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ in_array($building->id, old('buildings', [])) ? 'selected' : '' }}>
                                {{ $building->description }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="estatus" name="estatus" value="1" {{ old('estatus', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="estatus">Usuario activo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
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
</style>