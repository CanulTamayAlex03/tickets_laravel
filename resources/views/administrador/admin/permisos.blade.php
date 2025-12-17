@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white py-2 text-center">
            <h5 class="mb-0">Administración de Roles y Permisos</h5>
        </div>
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#crearRolModal">
                        <i class="fas fa-plus me-1"></i> Nuevo Rol
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Rol</th>
                                <th>Permisos</th>
                                <th style="width: 12%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $rol)
                            <tr>
                                <td>{{ $rol->id }}</td>
                                <td>{{ $rol->name }}</td>
                                <td>
                                    @if($rol->permissions->count() > 0)
                                    @foreach($rol->permissions as $permiso)
                                    <span class="badge bg-success text-light mb-1">{{ $permiso->name }}</span>
                                    @endforeach
                                    @else
                                    <span class="text-muted">Sin permisos</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-warning btn-sm editar-rol"
                                            data-id="{{ $rol->id }}">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Rol -->
<div class="modal fade" id="crearRolModal" tabindex="-1" aria-labelledby="crearRolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.permisos.create-role') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="crearRolModalLabel">Crear Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Rol</label>
                        <input type="text" class="form-control" id="nombre" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permisos</label>
                        <div class="border p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                            <div class="row">
                                @foreach($permissions as $permiso)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input"
                                            name="permissions[]"
                                            value="{{ $permiso->id }}"
                                            id="permiso_{{ $permiso->id }}">
                                        <label class="form-check-label" for="permiso_{{ $permiso->id }}">
                                            {{ $permiso->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Rol -->
<div class="modal fade" id="editarRolModal" tabindex="-1" aria-labelledby="editarRolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editarRolForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="rol_id" id="edit_rol_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarRolModalLabel">Editar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nombre" class="form-label">Nombre del Rol</label>
                        <input type="text" class="form-control" id="edit_nombre" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permisos</label>
                        <div class="border p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                            <div class="row" id="permisosContainer">
                                <div class="col-12 text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando permisos...</span>
                                    </div>
                                    <p class="mt-2 small">Cargando permisos...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('styles')
<style>
    .tabla-container {
        background-color: #f8f8f8ff;
        padding: 15px;
        border-radius: 6px;
    }
</style>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de roles cargado');

    document.querySelectorAll('.editar-rol').forEach(button => {
        button.removeAttribute('data-bs-toggle');
        button.removeAttribute('data-bs-target');
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.editar-rol')) {
            const button = e.target.closest('.editar-rol');
            const id = button.getAttribute('data-id');
            console.log('Editando rol ID:', id);
            loadRoleData(id);
        }
    });

    function loadRoleData(id) {
        document.getElementById('permisosContainer').innerHTML = `
            <div class="col-12 text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando permisos...</span>
                </div>
                <p class="mt-2 small">Cargando datos...</p>
            </div>
        `;

        const modalElement = document.getElementById('editarRolModal');
        const modal = new bootstrap.Modal(modalElement);
        
        modal.show();

        fetch('/admin/gestion-permisos/rol/' + id + '/edit-ajax')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Respuesta AJAX:', data);
                
                if (data.rol) {
                    document.getElementById('edit_rol_id').value = data.rol.id;
                    document.getElementById('edit_nombre').value = data.rol.name;

                    document.getElementById('editarRolForm').action = '/admin/gestion-permisos/rol/' + data.rol.id + '/permisos';

                    let permissionsHtml = '';
                    if (data.permisos && data.permisos.length > 0) {
                        data.permisos.forEach(function(perm) {
                            let isChecked = false;
                            if (data.rol.permissions) {
                                isChecked = data.rol.permissions.some(function(p) {
                                    return p.id === perm.id;
                                });
                            }

                            permissionsHtml += `
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" 
                                           id="edit_perm_${perm.id}" 
                                           name="permissions[]" 
                                           value="${perm.id}"
                                           ${isChecked ? 'checked' : ''}>
                                    <label for="edit_perm_${perm.id}" class="form-check-label">
                                        ${perm.name}
                                    </label>
                                </div>
                            </div>
                        `;
                        });
                    } else {
                        permissionsHtml = '<div class="col-12 text-center text-muted py-3">No hay permisos disponibles</div>';
                    }

                    document.getElementById('permisosContainer').innerHTML = permissionsHtml;
                    console.log('Permisos cargados correctamente');
                }
            })
            .catch(error => {
                console.error('Error AJAX:', error);
                document.getElementById('permisosContainer').innerHTML = `
                <div class="col-12 text-center text-danger py-3">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p>Error al cargar los datos</p>
                    <small>Error: ${error.message}</small>
                </div>
            `;
            });

        const closeButtons = modalElement.querySelectorAll('[data-bs-dismiss="modal"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                modal.hide();
            });
        });

        const editForm = document.getElementById('editarRolForm');
        editForm.onsubmit = function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');

            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Guardando...';
        
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        modal.hide();
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                console.error('Error al guardar:', error);
                showAlert('danger', 'Error al guardar los cambios: ' + error.message);
                submitButton.disabled = false;
                submitButton.innerHTML = 'Actualizar';
            });
        };

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            const content = document.querySelector('.container-fluid');
            content.insertBefore(alertDiv, content.firstChild);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    }
});
</script>
@endsection