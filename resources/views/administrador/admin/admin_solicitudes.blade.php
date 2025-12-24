@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white py-2 text-center">
            <h5 class="mb-0">Administración de Solicitudes</h5>
        </div>

        <div class="card-body p-3 tabla-container">
            <button class="btn btn-sm btn-primary"
                data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-search me-1"></i> Buscar Solicitudes
            </button>
            <div class="d-flex justify-content-between mb-3 mt-4">
    <div class="btn-group" role="group">
        <a href="{{ route('admin.admin_solicitudes', ['status' => 'nuevo']) }}"
            class="btn btn-filter {{ request('status') == 'nuevo' || !request()->has('status') ? 'active' : '' }}">
            <i class="bi bi-star me-1"></i> Nuevos
        </a>
        <a href="{{ route('admin.admin_solicitudes', ['status' => 'atendiendo']) }}"
            class="btn btn-filter {{ request('status') == 'atendiendo' ? 'active' : '' }}">
            <i class="bi bi-hourglass-split me-1"></i> Atendiendo
        </a>
        <a href="{{ route('admin.admin_solicitudes', ['status' => 'cerrado']) }}"
            class="btn btn-filter {{ request('status') == 'cerrado' ? 'active' : '' }}">
            <i class="bi bi-x-circle me-1"></i> Cerrado por usuario
        </a>
        <a href="{{ route('admin.admin_solicitudes', ['status' => 'pendiente']) }}"
            class="btn btn-filter {{ request('status') == 'pendiente' ? 'active' : '' }}">
            <i class="bi bi-clock-history me-1"></i> Pendiente
        </a>
        <a href="{{ route('admin.admin_solicitudes', ['status' => 'completado']) }}"
            class="btn btn-filter {{ request('status') == 'completado' ? 'active' : '' }}">
            <i class="bi bi-check2-circle me-1"></i> Completado
        </a>
    </div>
</div>

            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-2">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">ID</th>
                            <th>Descripción</th>
                            <th width="15%">Usuario/Área</th>
                            <th>Fecha Recepción</th>
                            <th>Estatus</th>
                            <th width="20%" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>
                            <td>{{ $ticket->description }}</td>
                            <td class="py-2">
                                <div class="d-flex flex-column">
                                    <strong>{{ $ticket->employee?->full_name ?? '—' }}</strong>
                                    <small class="text-muted">
                                        <i class="bi bi-building"></i>
                                        {{ $ticket->building?->description ?? 'Sin edificio' }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="bi bi-diagram-3"></i>
                                        {{ $ticket->department?->description ?? 'Sin departamento' }}
                                    </small>
                                </div>
                            </td>
                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @php
                                $statusColors = [
                                1 => 'primary', // Nuevo
                                2 => 'warning', // Atendiendo
                                3 => 'warning', // Cerrado
                                4 => 'warning', // Pendiente
                                5 => 'success', // Completado
                                ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$ticket->service_status_id] ?? 'secondary' }}">
                                    {{ $ticket->serviceStatus->description ?? 'Sin estatus' }}
                                </span>
                            </td>
                            <td>
                                @if (request('status') == 'nuevo' || !request()->has('status'))
                                    <div class="d-flex justify-content-center gap-1">
                                        @can('eliminar tickets')
                                        <button class="btn btn-danger btn-sm btn-eliminar" 
                                                title="Eliminar ticket"
                                                data-ticket-id="{{ $ticket->id }}"
                                                data-ticket-data='{
                                                    "descripcion": "{{ addslashes($ticket->description) }}",
                                                    "usuario": "{{ addslashes($ticket->employee?->full_name ?? 'Sin usuario') }}",
                                                    "fecha": "{{ $ticket->created_at->format('d/m/Y H:i') }}",
                                                    "estatus": "{{ $ticket->serviceStatus->description ?? 'Sin estatus' }}"
                                                }'>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endcan
                                        <button class="btn btn-primary btn-sm btn-asignar"
                                            title="Asignar este ticket"
                                            data-ticket-id="{{ $ticket->id }}"
                                            data-ticket-data='{
                                                "employee_name": "{{ $ticket->employee?->full_name ?? '—' }}",
                                                "description": "{{ addslashes($ticket->description) }}",
                                                "building": "{{ $ticket->building?->description ?? '—' }}",
                                                "department": "{{ $ticket->department?->description ?? '—' }}",
                                                "created_at": "{{ $ticket->created_at->format('d/m/Y H:i') }}",
                                                "support_name": "{{ $ticket->supportPersonal?->name ?? '' }} {{ $ticket->supportPersonal?->lastnames ?? '' }}"
                                            }'>
                                            <i class="bi bi-people me-1"></i>
                                        </button>
                                    </div>
                                @elseif (request('status') == 'completado')
                                    <div class="d-flex flex-column gap-1 align-items-center">
                                        <div class="mb-1">
                                            <button class="btn btn-primary btn-sm btn-ver" 
                                                    title="Ver"
                                                    data-ticket-id="{{ $ticket->id }}"
                                                    data-employee-name="{{ $ticket->employee?->full_name ?? '—' }}"
                                                    data-description="{{ $ticket->description ?? '—' }}"
                                                    data-building="{{ $ticket->building?->description ?? '—' }}"
                                                    data-department="{{ $ticket->department?->description ?? '—' }}"
                                                    data-created-at="{{ $ticket->created_at->format('d/m/Y H:i') }}"
                                                    data-support-name="{{ $ticket->supportPersonal ? ($ticket->supportPersonal->name . ' ' . $ticket->supportPersonal->lastnames) : 'Pendiente de asignar' }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="text-center small">
                                            @if($ticket->supportPersonal)
                                                <span class="text-muted">
                                                    <i class="bi bi-person-check me-1"></i>
                                                    {{ $ticket->supportPersonal->name }} {{ $ticket->supportPersonal->lastnames }}
                                                </span>
                                            @else
                                                <span class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    Pendiente de asignar
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex flex-column gap-1">
                                        
                                        <div class="d-flex justify-content-center gap-1 mb-1">
                                            <button class="btn btn-warning btn-sm btn-editar-ticket"
                                                title="Editar ticket"
                                                data-ticket-id="{{ $ticket->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            
                                            @can('eliminar tickets')
                                            <button class="btn btn-danger btn-sm btn-eliminar" 
                                                    title="Eliminar ticket"
                                                    data-ticket-id="{{ $ticket->id }}"
                                                    data-ticket-data='{
                                                        "descripcion": "{{ addslashes($ticket->description) }}",
                                                        "usuario": "{{ addslashes($ticket->employee?->full_name ?? 'Sin usuario') }}",
                                                        "fecha": "{{ $ticket->created_at->format('d/m/Y H:i') }}",
                                                        "estatus": "{{ $ticket->serviceStatus->description ?? 'Sin estatus' }}"
                                                    }'>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @endcan
                                            
                                            <button class="btn btn-primary btn-sm btn-seguimiento" 
                                                    title="Seguimiento"
                                                    data-ticket-id="{{ $ticket->id }}"
                                                    data-employee-name="{{ $ticket->employee?->full_name ? addslashes($ticket->employee->full_name) : '—' }}"
                                                    data-description="{{ $ticket->description ? addslashes($ticket->description) : '—' }}"
                                                    data-building="{{ $ticket->building?->description ? addslashes($ticket->building->description) : '—' }}"
                                                    data-department="{{ $ticket->department?->description ? addslashes($ticket->department->description) : '—' }}"
                                                    data-created-at="{{ $ticket->created_at->format('d/m/Y H:i') }}"
                                                    data-support-name="{{ $ticket->supportPersonal ? addslashes($ticket->supportPersonal->name . ' ' . $ticket->supportPersonal->lastnames) : 'Pendiente de asignar' }}">
                                                <i class="bi bi-chat-left-text"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="text-center small">
                                            @if($ticket->supportPersonal)
                                                <span class="text-muted">
                                                    <i class="bi bi-person-check me-1"></i>
                                                    {{ $ticket->supportPersonal->name }} {{ $ticket->supportPersonal->lastnames }}
                                                </span>
                                            @else
                                                <span class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    Pendiente de asignar
                                                </span>
                                            @endif
                                        </div>
                                        
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay tickets con este filtro</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted" id="registros-counter">
                    Mostrando {{ $tickets->firstItem() }} a {{ $tickets->lastItem() }} de {{ $tickets->total() }} registros
                </small>
                                                    
                <nav aria-label="Page navigation">
                    {{ $tickets->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
                </nav>
            </div>

        </div>
    </div>
</div>

@include('administrador.admin.modals.admin_solicitudes-modals.filter_modal')
@include('administrador.admin.modals.admin_solicitudes-modals.asignar_solicitud')
@include('administrador.admin.modals.admin_solicitudes-modals.editar_solicitud')
@include('administrador.admin.modals.admin_solicitudes-modals.seguimiento_modal')
@include('administrador.admin.modals.admin_solicitudes-modals.ver_modal')
@include('administrador.admin.modals.admin_solicitudes-modals.eliminar_modal')
@endsection

@section('scripts')
    @include('administrador.admin.scripts.filtros_solicitudes')
    @include('administrador.admin.scripts.asignar_solicitud')
    @include('administrador.admin.scripts.seguimiento_modal')
    @include('administrador.admin.scripts.ver_modal')
    @include('administrador.admin.scripts.editar_solicitud')
    @include('administrador.admin.scripts.eliminar_solicitud')
  
<script>
$(document).ready(function() {   
    let refreshInterval = null;
    let isFetching = false;
    let currentTicketCount = {{ $tickets->total() }};
    let lastTimestamp = '{{ now()->toISOString() }}';
    
    function mostrarNotificacion(mensaje, tipo = 'success') {
        $('.alert.position-fixed').remove();
        
        const icono = tipo === 'success' ? 'check-circle' : 'exclamation-circle';
        const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
        
        const $notificacion = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <i class="bi bi-${icono} me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        
        $('body').append($notificacion);
        
        setTimeout(() => {
            $notificacion.alert('close');
        }, 3000);
    }
    
    function shouldActivateAutoRefresh() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        return !status || status === 'nuevo';
    }
    
    function checkForNewTickets() {
        if (isFetching || !shouldActivateAutoRefresh()) {
            return;
        }
        
        isFetching = true;
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('partial', 'true');
        currentUrl.searchParams.set('_', new Date().getTime());
        
        $.ajax({
            url: currentUrl.toString(),
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    updateBadgeCount(response.total_count || response.count);
                    
                    if (shouldUpdateTable(response)) {
                        updateTableAndPagination(response);
                        
                        const newTickets = (response.total_count || response.count) - currentTicketCount;
                        if (newTickets > 0) {
                            showNewTicketsNotification(newTickets);
                        }
                        
                        currentTicketCount = response.total_count || response.count;
                    }
                    
                    if (response.timestamp) {
                        lastTimestamp = response.timestamp;
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al verificar tickets:', error);
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                    refreshInterval = setInterval(checkForNewTickets, 10000);
                }
            },
            complete: function() {
                isFetching = false;
            }
        });
    }
    
    function shouldUpdateTable(response) {
        const newTotal = response.total_count || 0;
        
        if (newTotal !== currentTicketCount) {
            return true;
        }
        
        if (response.timestamp && response.timestamp !== lastTimestamp) {
            return true;
        }
        
        return false;
    }
    
    function updateTableAndPagination(response) {
        const scrollTop = $(window).scrollTop();
        
        const $tableResponsive = $('.table-responsive');
        const $existingTable = $tableResponsive.find('table');
        
        if (response.table_html) {
            const $newContent = $(response.table_html);
            
            if ($newContent.is('table')) {
                if ($existingTable.length) {
                    $existingTable.replaceWith($newContent);
                } else {
                    $tableResponsive.html($newContent);
                }
            } 
            else if ($newContent.find('table').length) {
                const $newTable = $newContent.find('table');
                if ($existingTable.length) {
                    $existingTable.replaceWith($newTable);
                } else {
                    $tableResponsive.html($newTable);
                }
            }
            else {
                const tableMatch = response.table_html.match(/<table[^>]*>[\s\S]*?<\/table>/i);
                if (tableMatch) {
                    if ($existingTable.length) {
                        $existingTable.replaceWith(tableMatch[0]);
                    } else {
                        $tableResponsive.html(tableMatch[0]);
                    }
                } else {
                    console.error('No se pudo extraer una tabla válida de la respuesta:', response.table_html);
                }
            }
        }
        
        if (response.pagination_html) {
            const $paginationContainer = $('nav[aria-label="Page navigation"]');
            if ($paginationContainer.length) {
                $paginationContainer.html(response.pagination_html);
            }
        }
        
        if (response.metadata_html) {
            $('#registros-counter').text(response.metadata_html);
        }
        
        $(window).scrollTop(scrollTop);
        
        reinitializeButtonEvents();
        
        console.log('Tabla actualizada:', new Date().toLocaleTimeString());
    }
    
    function updateBadgeCount(count) {
        const badge = $('#newTicketsBadge');
        const countSpan = $('#newTicketsCount');
        
        if (count > 0) {
            countSpan.text(count);
            badge.show();
        } else {
            badge.hide();
        }
    }
    
    function showNewTicketsNotification(count) {
        if (!shouldActivateAutoRefresh()) return;
        
        const notificationHtml = `
            <div class="alert alert-info alert-dismissible fade show mb-2" role="alert" 
                 style="position: fixed; top: 70px; right: 20px; z-index: 9999; max-width: 300px;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-bell-fill me-2"></i>
                    <div>
                        <strong>${count} nueva(s) solicitud(es)</strong>
                        <div class="small mt-1">
                            ${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                        </div>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            </div>
        `;
        
        $('.alert[role="alert"]').remove();
        
        $('#notificationContainer').prepend(notificationHtml);
        
        setTimeout(() => {
            $('.alert').alert('close');
        }, 4000);
    }
    
    function reinitializeButtonEvents() {
        $('.btn-asignar').off('click').on('click', function() {
            const ticketId = $(this).data('ticket-id');
            const ticketData = $(this).data('ticket-data');
            
            $('#ticket_id').val(ticketId);
            $('#service_status_id').val(2);
            $('#modal_employee_name').text(ticketData.employee_name || '—');
            $('#modal_description').text(ticketData.description || '—');
            $('#modal_building').text(ticketData.building || '—');
            $('#modal_department').text(ticketData.department || '—');
            $('#modal_created_at').text(ticketData.created_at || '—');
            
            $('#support_personal_id').val('').trigger('change');
            $('#asignarSolicitudModal').modal('show');
        });
        
        $('.btn-ver').off('click').on('click', function() {
            const ticketId = $(this).data('ticket-id');
            const employeeName = $(this).data('employee-name');
            const description = $(this).data('description');
            const building = $(this).data('building');
            const department = $(this).data('department');
            const createdAt = $(this).data('created-at');
            const supportName = $(this).data('support-name');
            
            $('#ver_employee_name').text(employeeName);
            $('#ver_description').text(description);
            $('#ver_building').text(building);
            $('#ver_department').text(department);
            $('#ver_created_at').text(createdAt);
            $('#ver_support_name').text(supportName);
            
            $('#verModal').modal('show');
        });
        
        $('.pagination a').off('click').on('click', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            loadPage(url);
        });
    }
    
    function loadPage(url) {
        stopAutoRefresh();
        
        const pageUrl = new URL(url, window.location.origin);
        pageUrl.searchParams.set('partial', 'true');
        
        $.ajax({
            url: pageUrl.toString(),
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateTableAndPagination(response);
                    currentTicketCount = response.total_count || response.count;
                    updateBadgeCount(currentTicketCount);
                    
                    window.history.pushState({}, '', url);
                }
            },
            error: function() {
                window.location.href = url;
            },
            complete: function() {
                if (shouldActivateAutoRefresh()) {
                    startAutoRefresh();
                }
            }
        });
    }
    
    function startAutoRefresh() {
        if (!shouldActivateAutoRefresh() || refreshInterval) {
            return;
        }
        
        console.log('Auto-refresh activado para tickets "nuevos"');
        
        refreshInterval = setInterval(checkForNewTickets, 10000);
        
        setTimeout(checkForNewTickets, 2000);
    }
    
    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
            console.log('Auto-refresh detenido');
        }
    }
    
    function setupPageEvents() {
        $(document).on('show.bs.modal', '.modal', function() {
            stopAutoRefresh();
        });
        
        $(document).on('hidden.bs.modal', '.modal', function() {
            if (shouldActivateAutoRefresh()) {
                setTimeout(startAutoRefresh, 1000);
            }
        });
        
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoRefresh();
            } else if (shouldActivateAutoRefresh()) {
                startAutoRefresh();
            }
        });
        
        $('.btn-filter').on('click', function(e) {
        });
        
        $(window).on('popstate', function() {
            setTimeout(function() {
                if (shouldActivateAutoRefresh()) {
                    startAutoRefresh();
                } else {
                    stopAutoRefresh();
                }
            }, 500);
        });
    }
    
    function initializeAutoRefreshSystem() {
        setupPageEvents();
        
        if (shouldActivateAutoRefresh()) {
            startAutoRefresh();
        }
        
        reinitializeButtonEvents();
    }
    
    // Inicializar el sistema de auto-refresh
    setTimeout(initializeAutoRefreshSystem, 1000);
});
</script>
@endsection

@push('styles')
<style>
    .table {
        font-size: 0.85rem;
    }

    .table th {
        white-space: nowrap;
        vertical-align: middle;
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        font-weight: 500;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }

    .pagination {
        font-size: 0.8rem;
        margin: 0;
    }

    .card-header h5 {
        font-weight: 600;
    }

    .btn-filter {
        background-color: #6f42c1;
        color: #fff;
        border: none;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .btn-filter:hover {
        background-color: #5a379c;
        color: #fff;
    }

    .btn-filter.active {
        background-color: #4b2982;
        font-weight: 600;
        box-shadow: 0 0 6px rgba(111, 66, 193, 0.6);
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #0a58ca;
        border-color: #0a53be;
    }

    .btn-secondary:hover {
        background-color: #565e64;
        border-color: #51585e;
    }

    .tabla-container {
        background-color: #f8f8f8ff;
        padding: 15px;
        border-radius: 6px;
    }
    .btn-asignar {
        font-size: 0.75rem;
        padding: 0.2rem 0.5rem;
    }

    #auto-refresh-indicator:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    .toast {
        font-size: 0.9rem;
    }
</style>
@endpush