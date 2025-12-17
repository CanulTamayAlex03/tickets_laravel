<script>
$(document).ready(function() {
    
    function formatearFecha(fechaString) {
        if (!fechaString) return '—';
        
        const fecha = new Date(fechaString);
        const opciones = {
            hour: '2-digit',
            minute: '2-digit'
        };
        
        return fecha.toLocaleDateString('es-MX') + ' ' + 
               fecha.toLocaleTimeString('es-MX', opciones);
    }

    function generarEstrellasHTML(cantidad) {
        if (!cantidad || cantidad === 0) {
            return '<span class="text-muted">Sin calificación</span>';
        }

        let estrellasHTML = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= cantidad) {
                estrellasHTML += '<i class="bi bi-star-fill star-rating me-1"></i>';
            } else {
                estrellasHTML += '<i class="bi bi-star text-secondary me-1"></i>';
            }
        }

        estrellasHTML += ` <small class="text-muted">(${cantidad}/5)</small>`;

        return estrellasHTML;
    }

    function obtenerEstadoInfo(ticket) {
        let estadoNombre = '—';
        let estadoColor = 'success';
        
        if (ticket.service_status && ticket.service_status.description) {
            estadoNombre = ticket.service_status.description;
        } else if (ticket.serviceStatus && ticket.serviceStatus.description) {
            estadoNombre = ticket.serviceStatus.description;
        } else if (ticket.service_status_id) {
            const estados = {
                1: { nombre: 'Nuevo', color: 'primary' },
                2: { nombre: 'Atendiendo', color: 'info' },
                3: { nombre: 'Cerrado', color: 'danger' },
                4: { nombre: 'Pendiente', color: 'warning' },
                5: { nombre: 'Completado', color: 'success' }
            };
            const estado = estados[ticket.service_status_id] || { nombre: '—', color: 'secondary' };
            estadoNombre = estado.nombre;
            estadoColor = estado.color;
        }
        
        return { nombre: estadoNombre, color: estadoColor };
    }

    function cargarSeguimientosVer(ticketId) {
        $.ajax({
            url: `/admin/solicitudes/${ticketId}/edit`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const seguimientos = response.extra_infos || [];
                    const container = $('#seguimientos_container_ver');
                    container.empty();
                    
                    if (seguimientos.length === 0) {
                        container.html('<div class="alert alert-info">No hay seguimientos registrados.</div>');
                        return;
                    }

                    seguimientos.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                    seguimientos.forEach(function(seguimiento, index) {
                        const fecha = formatearFecha(seguimiento.created_at);
                        const usuario = seguimiento.user?.email || 'Usuario desconocido';
                        const numero = index + 1;

                        const seguimientoHtml = `
                            <div class="seguimiento-item mb-2">
                                <div class="seguimiento-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <small class="text-muted fw-bold">
                                                ${numero}. <i class="bi bi-person me-1"></i> ${usuario}
                                            </small>
                                        </div>
                                        <small class="text-muted">${fecha}</small>
                                    </div>
                                    <div class="seguimiento-desc mt-2">
                                        ${seguimiento.description}
                                    </div>
                                </div>
                            </div>
                        `;

                        container.append(seguimientoHtml);
                    });
                }
            },
            error: function() {
                $('#seguimientos_container_ver').html('<div class="alert alert-danger">Error al cargar seguimientos.</div>');
            }
        });
    }

    $(document).on('click', '.btn-ver', function() {
        const $boton = $(this);
        const ticketId = $boton.data('ticket-id');
        
        const ticketData = {
            employee_name: $boton.data('employee-name') || '—',
            description: $boton.data('description') || '—',
            building: $boton.data('building') || '—',
            department: $boton.data('department') || '—',
            created_at: $boton.data('created-at') || '—',
            support_name: $boton.data('support-name') || 'Pendiente de asignar'
        };
        
        $('#seguimientos_container_ver').html('<div class="alert alert-info">Cargando seguimientos...</div>');
        
        $('#ver_ticket_id').text(ticketId);
        
        $('#ver_employee_name').text(ticketData.employee_name);
        $('#ver_description').text(ticketData.description);
        $('#ver_building').text(ticketData.building);
        $('#ver_department').text(ticketData.department);
        $('#ver_created_at').text(ticketData.created_at);
        
        if (ticketData.support_name && ticketData.support_name !== 'Pendiente de asignar') {
            $('#ver_support_personal').html(`
                <i class="bi bi-person-check me-1"></i>
                ${ticketData.support_name}
            `);
        } else {
            $('#ver_support_personal').html(`
                <span class="text-muted">
                    <i class="bi bi-clock me-1"></i>
                    Pendiente de asignar
                </span>
            `);
        }
        
        $.ajax({
            url: `/admin/solicitudes/${ticketId}/edit`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const ticket = response.ticket;
                    
                    if (ticket.support_closing) {
                        $('#ver_support_closing').text(formatearFecha(ticket.support_closing));
                    } else {
                        $('#ver_support_closing').text('No liberado aún');
                    }
                    
                    const estadoInfo = obtenerEstadoInfo(ticket);
                    $('#ver_status').html(`
                        <span class="badge badge-estado bg-${estadoInfo.color}">
                            ${estadoInfo.nombre}
                        </span>
                    `);
                    
                    $('#ver_indicator').text(ticket.indicator_type?.description || '—');
                    
                    $('#ver_service').text(ticket.another_service?.description || '—');
                    
                    let equipoInfo = '—';
                    if (ticket.equipment?.description) {
                        equipoInfo = ticket.equipment.description;
                    } else if (ticket.equipment_id) {
                        equipoInfo = `Equipo ID: ${ticket.equipment_id}`;
                    }
                    $('#ver_equipment').text(equipoInfo);
                    
                    if (ticket.retroalimentation) {
                        $('#ver_retroalimentation').text(ticket.retroalimentation);
                    } else {
                        $('#ver_retroalimentation').text('No hay retroalimentación');
                    }
                    
                    $('#ver_stars').html(generarEstrellasHTML(ticket.stars || 0));
                    
                    $('#ver_activity').text(ticket.activity_description || 'No especificada');
                    
                    cargarSeguimientosVer(ticketId);
                }
            },
            error: function() {
                $('#seguimientos_container_ver').html('<div class="alert alert-danger">Error al cargar información completa del ticket.</div>');
            }
        });
        
        $('#verModal').modal('show');
    });
});
</script>