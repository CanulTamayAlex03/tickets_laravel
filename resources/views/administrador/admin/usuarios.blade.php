@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white py-2 text-center">
            <h5 class="mb-0">Administración de usuarios</h5>
        </div>
        <div class="card-body p-3">
            <!-- Mensajes de éxito/error -->
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

            <!-- Filtro y botón de agregar -->
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

            <!-- Tabla de usuarios -->
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-2 mx-auto" style="width: 90%; margin-top: 15px">
                    <thead class="table-dark">
                        <tr>
                            <th width="10%">ID</th>
                            <th>Email</th>
                            <th>Tipo de Usuario</th>
                            <th>Estado</th>
                            <th width="25%" class="text-start">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->userType->description ?? 'N/A' }}</td>
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
                                        data-user_type_id="{{ $user->user_type_id }}"
                                        data-estatus="{{ $user->estatus }}"
                                        data-buildings="{{ $user->buildings->pluck('id')->toJson() }}">
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

<!-- Incluir modales -->
@include('administrador.admin.modals.user_create')
@include('administrador.admin.modals.user_edit')

<!-- Scripts para manejo de modales -->
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Select2 en los selects que están fuera de modales
    $('.select2-tags:not(.modal .select2-tags)').select2({
        width: '100%',
        placeholder: "Selecciona edificios...",
        allowClear: true
    });

    // Manejar el modal de creación
    $('#addUserModal').on('shown.bs.modal', function () {
        // Inicializar Select2 dentro del modal de creación
        $('#buildings').select2({
            width: '100%',
            placeholder: "Selecciona edificios...",
            allowClear: true,
            dropdownParent: $('#addUserModal')
        });
    });

    // Manejar el modal de edición
    $('#editUserModal').on('shown.bs.modal', function () {
        // Inicializar Select2 dentro del modal de edición
        $('#edit_buildings').select2({
            width: '100%',
            placeholder: "Selecciona edificios...",
            allowClear: true,
            dropdownParent: $('#editUserModal')
        });
    });

    // Cerrar Select2 cuando se cierre el modal
    $('#addUserModal, #editUserModal').on('hidden.bs.modal', function () {
        $('#buildings, #edit_buildings').select2('destroy');
    });

    // Manejar el modal de edición
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const userEmail = this.getAttribute('data-email');
            const userTypeId = this.getAttribute('data-user_type_id');
            const estatus = this.getAttribute('data-estatus');
            const buildings = JSON.parse(this.getAttribute('data-buildings'));
            
            // Configurar el formulario de edición
            document.getElementById('editForm').action = `/admin/usuarios/${userId}`;
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_user_type_id').value = userTypeId;
            document.getElementById('edit_estatus').checked = estatus === '1';
            
            // Mostrar el modal
            const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editModal.show();
            
            // Configurar los edificios después de que Select2 se inicialice
            $('#editUserModal').one('shown.bs.modal', function() {
                const buildingSelect = document.getElementById('edit_buildings');
                Array.from(buildingSelect.options).forEach(option => {
                    option.selected = buildings.includes(parseInt(option.value));
                });
                
                // Actualizar Select2
                $('#edit_buildings').trigger('change');
            });
        });
    });
});
</script>
@endsection

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
</style>
@endsection