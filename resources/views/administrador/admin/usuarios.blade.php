@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white py-2 text-center">
            <h5 class="mb-0">Administración de usuarios</h5>
        </div>
        <div class="card-body p-3">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <form action="{{ route('admin.usuarios') }}" method="GET">
                    <div class="input-group input-group-sm" style="width: 300px;">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text"
                            class="form-control"
                            placeholder="Filtrar por correo..."
                            name="search"
                            value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Filtrar</button>
                        @if(request('search'))
                        <a href="{{ route('admin.usuarios') }}" class="btn btn-outline-secondary" title="Limpiar filtro">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </form>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo usuario
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-2 mx-auto" style="width: 90%; margin-top: 15px">
                    <thead class="table-dark">
                        <tr>
                            <th width="10%">ID</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th width="25%" class="text-start">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->getRoleNames()->first() ?? 'Sin rol' }}</td>
                            <td>
                                <span class="badge rounded-pill {{ $user->estatus ? 'bg-success' : 'bg-danger' }}">
                                    {{ $user->estatus ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-start">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Acciones">
                                    <button class="btn btn-warning px-3 btn-edit"
                                            title="Editar"
                                            data-id="{{ $user->id }}"
                                            data-email="{{ $user->email }}"
                                            data-role_id="{{ $user->roles->first()->id ?? '' }}"
                                            data-estatus="{{ $user->estatus ? '1' : '0' }}"
                                            data-buildings="{{ json_encode($user->buildings->pluck('id')->toArray()) }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} de {{ $users->total() }} registros
                </small>
                <nav aria-label="Page navigation">
                    {{ $users->onEachSide(1)->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
    </div>
</div>

@include('administrador.admin.modals.user_create')
@include('administrador.admin.modals.user_edit')

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado - Script ejecutándose');
        
    const editButtons = document.querySelectorAll('.btn-edit');
    console.log('Botones de editar encontrados:', editButtons.length);
    
    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Botón editar clickeado');
            
            const userId = this.getAttribute('data-id');
            const userEmail = this.getAttribute('data-email');
            const roleId = this.getAttribute('data-role_id');
            const estatus = this.getAttribute('data-estatus');
            const buildings = JSON.parse(this.getAttribute('data-buildings') || '[]');
            
            console.log('Datos:', { userId, userEmail, roleId, estatus, buildings });
            
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_role_id').value = roleId;
            document.getElementById('edit_estatus').checked = estatus === '1';
            
            const editForm = document.getElementById('editForm');
            if (editForm) {
                editForm.action = `/admin/usuarios/${userId}`;
                console.log('Form action actualizado a:', editForm.action);
            }
            
            const editModalElement = document.getElementById('editUserModal');
            if (editModalElement) {
                const editModal = new bootstrap.Modal(editModalElement);
                editModal.show();
                console.log('Modal mostrado');
                
                editModalElement.addEventListener('shown.bs.modal', function() {
                    console.log('Modal completamente mostrado');
                    
                    const buildingSelect = document.getElementById('edit_buildings');
                    if (buildingSelect) {
                        console.log('Select de buildings encontrado');
                        
                        Array.from(buildingSelect.options).forEach(option => {
                            option.selected = false;
                        });
                        
                        buildings.forEach(buildingId => {
                            const option = buildingSelect.querySelector(`option[value="${buildingId}"]`);
                            if (option) {
                                option.selected = true;
                                console.log('Building seleccionado:', buildingId);
                            }
                        });
                        
                        if (typeof $.fn.select2 !== 'undefined' && $('#edit_buildings').hasClass('select2-hidden-accessible')) {
                            $('#edit_buildings').trigger('change');
                            console.log('Select2 actualizado');
                        }
                    }
                    
                    document.getElementById('edit_password').value = '';
                    document.getElementById('edit_password_confirmation').value = '';
                    
                    document.querySelectorAll('.toggle-password i').forEach(icon => {
                        icon.className = 'bi bi-eye';
                    });
                });
            } else {
                console.error('ERROR: No se encontró el elemento #editUserModal');
            }
        });
    });
    
    document.addEventListener('click', function(e) {
        const toggleBtn = e.target.closest('.toggle-password');
        if (toggleBtn) {
            const targetId = toggleBtn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = toggleBtn.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    });
    
    const addForm = document.getElementById('addUserForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres');
                return false;
            }
        });
    }
    
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            const password = document.getElementById('edit_password').value;
            const confirmPassword = document.getElementById('edit_password_confirmation').value;

            const hasPassword = password.trim().length > 0;
            const hasConfirmPassword = confirmPassword.trim().length > 0;

            if ((hasPassword && !hasConfirmPassword) || (!hasPassword && hasConfirmPassword)) {
                e.preventDefault();
                alert('Por favor, complete ambos campos de contraseña o déjelos vacíos si no desea cambiarla');
                return false;
            }

            if (hasPassword && hasConfirmPassword) {
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    return false;
                }

                if (password.length < 6) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 6 caracteres');
                    return false;
                }
            }

        });
    }
    
    if (typeof $.fn.select2 !== 'undefined') {
        $('#addUserModal').on('shown.bs.modal', function() {
            $('#buildings').select2({
                dropdownParent: $(this),
                width: '100%'
            });
        });
        
        $('#editUserModal').on('shown.bs.modal', function() {
            $('#edit_buildings').select2({
                dropdownParent: $(this),
                width: '100%'
            });
        });
        
        $('#addUserModal, #editUserModal').on('hidden.bs.modal', function() {
            $('#buildings, #edit_buildings').select2('destroy');
        });
    }
});
</script>

<style>
    .table {
        font-size: 0.85rem;
    }
    .table th {
        white-space: nowrap;
    }
    .pagination {
        font-size: 0.8rem;
        margin: 0;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
    }
    .btn-group-sm > .btn {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        margin: 0 2px;
    }
    .card-header.text-center h5 {
        text-align: center;
        width: 100%;
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    .input-group .btn-outline-secondary {
        border-left: none;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    .toggle-password {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-left: 0;
    }
</style>
@endsection