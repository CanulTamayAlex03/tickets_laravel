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

            <!-- Tabla de solicitudes -->
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
                                    <!-- Botones para Nuevo -->
                                    <div class="d-flex justify-content-center gap-1">
                                        @can('eliminar tickets')
                                        <button class="btn btn-danger btn-sm" title="Eliminar">
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
                                    <!-- SOLO para Completado: solo el botón Ver y nombre del support -->
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
                                        
                                        <!-- FILA 1: botones normales -->
                                        <div class="d-flex justify-content-center gap-1 mb-1">
                                            <button class="btn btn-warning btn-sm btn-editar-ticket"
                                                title="Editar ticket"
                                                data-ticket-id="{{ $ticket->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            
                                            @can('eliminar tickets')
                                            <button class="btn btn-danger btn-sm" title="Eliminar">
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
                                        
                                        <!-- FILA 2: mostrar asignado o pendiente -->
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
@endsection

@section('scripts')
    @include('administrador.admin.scripts.filtros_solicitudes')
    @include('administrador.admin.scripts.asignar_solicitud')
    @include('administrador.admin.scripts.seguimiento_modal')
    @include('administrador.admin.scripts.ver_modal')
    @include('administrador.admin.scripts.editar_solicitud')
  
<script>
$(document).ready(function() {   
    let refreshInterval = null;
    let isFetching = false;
    let currentTicketCount = {{ $tickets->total() }};
    let lastTimestamp = '{{ now()->toISOString() }}';
    
    // 1. VERIFICAR SI ESTAMOS EN TICKETS "NUEVOS"
    function shouldActivateAutoRefresh() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        return !status || status === 'nuevo';
    }
    
    // 2. VERIFICAR NUEVOS TICKETS (CONSOLIDADO)
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
    
    // 3. VERIFICAR SI DEBEMOS ACTUALIZAR LA TABLA
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
    
    // 4. ACTUALIZAR TABLA Y PAGINACIÓN
function updateTableAndPagination(response) {
    const scrollTop = $(window).scrollTop();
    
    // 1. LIMPIAR la tabla ANTES de actualizar
    const $tableResponsive = $('.table-responsive');
    const $existingTable = $tableResponsive.find('table');
    
    // 2. Verificar que response.table_html sea realmente una tabla
    if (response.table_html) {
        const $newContent = $(response.table_html);
        
        // Si es una tabla completa, reemplazar solo la tabla
        if ($newContent.is('table')) {
            if ($existingTable.length) {
                $existingTable.replaceWith($newContent);
            } else {
                $tableResponsive.html($newContent);
            }
        } 
        // Si es solo el tbody (o contenido interno)
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
    
    // 3. ACTUALIZAR PAGINACIÓN (asegurarse de que no se meta en la tabla)
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
    
    // 5. ACTUALIZAR BADGE EN EL MENÚ
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
    
    // 6. NOTIFICACIÓN DE TICKETS NUEVOS
    function showNewTicketsNotification(count) {
        // Solo mostrar si estamos en la vista de "nuevos"
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
    
    // 7. RE-CONECTAR EVENTOS DE BOTONES (COMPLETO)
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
    
    // Botones de EDITAR - CÓDIGO COMPLETO AQUÍ
    $('.btn-editar-ticket').off('click').on('click', function() {
        const ticketId = $(this).data('ticket-id');
        console.log('Abriendo modal de edición para ticket:', ticketId);
        
        $('#editarSolicitudForm')[0].reset();
        $('#edit_another_service_id').prop('disabled', true).html('<option value="">Primero seleccione un indicador</option>');
        $('#form_nuevo_seguimiento').hide();
        $('.btn-agregar-seguimiento').show();
        
        // Cargar datos del ticket
        $.ajax({
            url: `/admin/solicitudes/${ticketId}/edit`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const ticket = response.ticket;
                    
                    function formatearFecha(fechaString) {
                        if (!fechaString) return '—';
                        const fecha = new Date(fechaString);
                        return fecha.toLocaleDateString('es-MX') + ' ' + 
                            fecha.toLocaleTimeString('es-MX', {hour: '2-digit', minute:'2-digit'});
                    }
                    
                    $('#edit_ticket_id').val(ticket.id);
                    $('#edit_support_personal_id').val(ticket.support_personal_id).trigger('change');
                    $('#edit_employee_name').text(ticket.employee?.full_name || '—');
                    $('#edit_description').text(ticket.description || '—');
                    $('#edit_building').text(ticket.building?.description || '—');
                    $('#edit_department').text(ticket.department?.description || '—');
                    $('#edit_created_at').text(formatearFecha(ticket.created_at));
                    $('#edit_retroalimentation').text(ticket.retroalimentation || '—');
                    
                    if (ticket.stars && ticket.stars > 0) {
                        let estrellasHTML = '';
                        for (let i = 1; i <= 5; i++) {
                            if (i <= ticket.stars) {
                                estrellasHTML += '<i class="bi bi-star-fill text-warning me-1"></i>';
                            } else {
                                estrellasHTML += '<i class="bi bi-star text-secondary me-1"></i>';
                            }
                        }
                        estrellasHTML += ` <small class="text-muted">(${ticket.stars}/5)</small>`;
                        $('#edit_stars').html(estrellasHTML);
                    } else {
                        $('#edit_stars').html('<span class="text-muted">Sin calificación</span>');
                    }
                    
                    $('#edit_indicator_type_id').val(ticket.indicator_type_id || '');
                    $('#edit_activity_description').val(ticket.activity_description || '');
                    $('#edit_service_status_id').val(ticket.service_status_id || 1);
                    $('#edit_equipment_id').val(ticket.equipment_id || '');
                    
                    if (ticket.support_closing) {
                        $('#edit_support_closing').text(formatearFecha(ticket.support_closing));
                    } else {
                        $('#edit_support_closing').text('No liberado aún');
                    }
                    
                    if (ticket.indicator_type_id) {
                        cargarServiciosPorIndicador(ticket.indicator_type_id, ticket.another_service_id);
                    }
                    
                    cargarSeguimientos(response.extra_infos || []);
                    
                    $('#edit_support_personal_id').select2({
                        dropdownParent: $('#editarSolicitudModal')
                    });
                    
                    $('#editarSolicitudModal').modal('show');
                }
            },
            error: function(xhr) {
                console.error('Error al cargar ticket:', xhr);
                alert('Error al cargar los datos del ticket');
            }
        });
    });
    
    function cargarServiciosPorIndicador(indicatorId, selectedServiceId = null) {
        if (!indicatorId) {
            $('#edit_another_service_id').prop('disabled', true).html('<option value="">Primero seleccione un indicador</option>');
            return;
        }

        $.ajax({
            url: `/admin/solicitudes/servicios-por-indicador/${indicatorId}`,
            type: 'GET',
            success: function(services) {
                let options = '<option value="">Seleccionar servicio</option>';
                services.forEach(function(service) {
                    const selected = (selectedServiceId == service.id) ? 'selected' : '';
                    options += '<option value="' + service.id + '" ' + selected + '>' + 
                              service.description + '</option>';
                });

                $('#edit_another_service_id').prop('disabled', false).html(options);
            }
        });
    }
    
    function cargarSeguimientos(seguimientos) {
        const container = $('#seguimientos_container');
        container.empty();
        
        if (seguimientos.length === 0) {
            container.html('<div class="alert alert-info">No hay seguimientos registrados.</div>');
            return;
        }

        seguimientos.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

        seguimientos.forEach(function(seguimiento, index) {
            const fecha = new Date(seguimiento.created_at).toLocaleDateString('es-MX') + ' ' + 
                         new Date(seguimiento.created_at).toLocaleTimeString('es-MX', {hour: '2-digit', minute:'2-digit'});
            const usuario = seguimiento.user?.email || 'Usuario desconocido';
            const numero = index + 1;

            const seguimientoHtml = `
                <div class="card mb-2 seguimiento-item">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted fw-bold">
                                    ${numero} <i class="bi bi-person me-1"></i> ${usuario}
                                </small>
                            </div>
                            <small class="text-muted">${fecha}</small>
                        </div>
                        <p class="mb-0 mt-2 ps-4">${seguimiento.description}</p>
                    </div>
                </div>
            `;

            container.append(seguimientoHtml);
        });
    }
    
    $('#edit_indicator_type_id').off('change').on('change', function() {
        const indicatorId = $(this).val();
        cargarServiciosPorIndicador(indicatorId);
    });
    
    $('.btn-agregar-seguimiento').off('click').on('click', function() {
        $('#form_nuevo_seguimiento').slideDown();
        $(this).hide();
        $('#nuevo_seguimiento').focus();
    });
    
    $('.btn-cancelar-seguimiento').off('click').on('click', function() {
        $('#form_nuevo_seguimiento').slideUp();
        $('#nuevo_seguimiento').val('');
        $('.btn-agregar-seguimiento').show();
    });
    
    $('.btn-guardar-seguimiento').off('click').on('click', function() {
        const seguimiento = $('#nuevo_seguimiento').val().trim();
        const ticketId = $('#edit_ticket_id').val();

        if (!seguimiento) {
            alert('Por favor, escriba el seguimiento');
            return;
        }

        const $btnGuardar = $(this);
        $btnGuardar.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Guardando...');

        $.ajax({
            url: `/admin/solicitudes/${ticketId}/agregar-seguimiento`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                description: seguimiento
            },
            success: function(response) {
                if (response.success) {
                    $.ajax({
                        url: `/admin/solicitudes/${ticketId}/edit`,
                        type: 'GET',
                        success: function(resp) {
                            if (resp.success) {
                                cargarSeguimientos(resp.extra_infos || []);
                                $('#nuevo_seguimiento').val('');
                                $('#form_nuevo_seguimiento').slideUp();
                                $('.btn-agregar-seguimiento').show();
                                alert('Seguimiento guardado exitosamente');
                            }
                        }
                    });
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Error al guardar el seguimiento');
            },
            complete: function() {
                $btnGuardar.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Guardar');
            }
        });
    });
    
    // Botones de SEGUIMIENTO
    $('.btn-seguimiento').off('click').on('click', function() {
        const ticketId = $(this).data('ticket-id');
        const employeeName = $(this).data('employee-name');
        const description = $(this).data('description');
        const building = $(this).data('building');
        const department = $(this).data('department');
        const createdAt = $(this).data('created-at');
        const supportName = $(this).data('support-name');
        
        // Llenar datos en el modal
        $('#seguimiento_ticket_id').val(ticketId);
        $('#seguimiento_employee_name').text(employeeName);
        $('#seguimiento_description').text(description);
        $('#seguimiento_building').text(building);
        $('#seguimiento_department').text(department);
        $('#seguimiento_created_at').text(createdAt);
        $('#seguimiento_support_name').text(supportName);
        
        // Cargar seguimientos anteriores
        loadSeguimientos(ticketId);
        
        $('#seguimientoModal').modal('show');
    });
    
    // Botones de VER
    $('.btn-ver').off('click').on('click', function() {
        const ticketId = $(this).data('ticket-id');
        const employeeName = $(this).data('employee-name');
        const description = $(this).data('description');
        const building = $(this).data('building');
        const department = $(this).data('department');
        const createdAt = $(this).data('created-at');
        const supportName = $(this).data('support-name');
        
        // Llenar datos en el modal
        $('#ver_employee_name').text(employeeName);
        $('#ver_description').text(description);
        $('#ver_building').text(building);
        $('#ver_department').text(department);
        $('#ver_created_at').text(createdAt);
        $('#ver_support_name').text(supportName);
        
        $('#verModal').modal('show');
    });
    
    // Botones de paginación
    $('.pagination a').off('click').on('click', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        loadPage(url);
    });
}
    // Evento para enviar el formulario de edición
    $('#editarSolicitudForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        const ticketId = $('#edit_ticket_id').val();
        const formData = $(this).serialize();
        
        $.ajax({
            url: `/admin/solicitudes/${ticketId}`,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                if (response.success) {
                    $('#editarSolicitudModal').modal('hide');
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Error al actualizar la solicitud');
            }
        });
    });
    
    // 8. CARGAR PÁGINA VÍA AJAX
    function loadPage(url) {
        stopAutoRefresh();
        
        // Agregar parámetro partial a la URL
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
                    
                    // Actualizar URL en el navegador sin recargar
                    window.history.pushState({}, '', url);
                }
            },
            error: function() {
                // Si falla AJAX, recargar normalmente
                window.location.href = url;
            },
            complete: function() {
                // Reactivar auto-refresh si corresponde
                if (shouldActivateAutoRefresh()) {
                    startAutoRefresh();
                }
            }
        });
    }
    
    // 9. INICIAR AUTO-REFRESH
    function startAutoRefresh() {
        if (!shouldActivateAutoRefresh() || refreshInterval) {
            return;
        }
        
        console.log('Auto-refresh activado para tickets "nuevos"');
        
        refreshInterval = setInterval(checkForNewTickets, 10000);
        
        setTimeout(checkForNewTickets, 2000);
    }
    
    // 10. DETENER AUTO-REFRESH
    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
            console.log('Auto-refresh detenido');
        }
    }
    
    // 11. CONFIGURAR EVENTOS DE LA PÁGINA
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
        
        // Manejar botones de filtro
        $('.btn-filter').on('click', function(e) {

        });
        
        // Manejar navegación del navegador (atrás/adelante)
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
    
    // 12. INICIALIZAR SISTEMA
    function initializeAutoRefreshSystem() {
        setupPageEvents();
        
        // Iniciar auto-refresh si estamos en vista de "nuevos"
        if (shouldActivateAutoRefresh()) {
            startAutoRefresh();
        }
        
        // Inicializar eventos de botones
        reinitializeButtonEvents();
    }
    
    // Iniciar después de que todo cargue
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

    /* Estilo base de los filtros */
    .btn-filter {
        background-color: #6f42c1;
        color: #fff;
        border: none;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Hover */
    .btn-filter:hover {
        background-color: #5a379c;
        color: #fff;
    }

    /* Botón activo */
    .btn-filter.active {
        background-color: #4b2982;
        font-weight: 600;
        box-shadow: 0 0 6px rgba(111, 66, 193, 0.6);
        color: #fff;
    }

    /* Hover states */
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