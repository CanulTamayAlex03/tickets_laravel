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

    function renumerarSeguimientos() {
        $('.seguimiento-item-simple').each(function(index) {
            const $texto = $(this).find('.text-muted.fw-bold');
            const textoActual = $texto.html();
            const nuevoTexto = textoActual.replace(/^\d+\s|^Nuevo\s/, (index + 1) + ' ');
            $texto.html(nuevoTexto);
        });
    }

    function cargarSeguimientosSimples(ticketId) {
        $.ajax({
            url: `/admin/solicitudes/${ticketId}/edit`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const seguimientos = response.extra_infos || [];
                    const container = $('#seguimientos_container_simple');
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
                            <div class="card mb-2 seguimiento-item-simple">
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
            },
            error: function() {
                $('#seguimientos_container_simple').html('<div class="alert alert-danger">Error al cargar seguimientos.</div>');
            }
        });
    }

$(document).on('click', '.btn-seguimiento', function() {
    const ticketId = $(this).data('ticket-id');
    
    const ticketData = {
        employee_name: $(this).data('employee-name') || '—',
        description: $(this).data('description') || '—',
        building: $(this).data('building') || '—',
        department: $(this).data('department') || '—',
        created_at: $(this).data('created-at') || '—',
        support_name: $(this).data('support-name') || 'Pendiente de asignar'
    };
    
    $('#form_nuevo_seguimiento_simple').hide();
    $('#nuevo_seguimiento_simple').val('');
    $('.btn-agregar-seguimiento-simple').show();
    $('#seguimientos_container_simple').html('<div class="alert alert-info">Cargando seguimientos...</div>');
    
    $('#seguimiento_ticket_id').val(ticketId);
    
    $('#seguimiento_employee_name').text(ticketData.employee_name);
    $('#seguimiento_description').text(ticketData.description);
    $('#seguimiento_building').text(ticketData.building);
    $('#seguimiento_department').text(ticketData.department);
    $('#seguimiento_created_at').text(ticketData.created_at);
    $('#seguimiento_assigned_to').text(ticketData.support_name);
    
    $.ajax({
        url: `/admin/solicitudes/${ticketId}/edit`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const ticket = response.ticket;
                
                if (ticket.support_closing) {
                    $('#seguimiento_support_closing').text(formatearFecha(ticket.support_closing));
                } else {
                    $('#seguimiento_support_closing').text('No liberado aún');
                }
                
                $('#seguimiento_status').text(ticket.service_status?.description || '—');
                $('#seguimiento_activity').text(ticket.activity_description || 'No especificada');
                
                cargarSeguimientosSimples(ticketId);
            }
        },
        error: function() {
            $('#seguimientos_container_simple').html('<div class="alert alert-danger">Error al cargar información completa del ticket.</div>');
        }
    });
    
    $('#seguimientoModal').modal('show');
});

    $(document).on('click', '.btn-agregar-seguimiento-simple', function() {
        $('#form_nuevo_seguimiento_simple').slideDown();
        $(this).hide();
        $('#nuevo_seguimiento_simple').focus();
    });
    
    $(document).on('click', '.btn-cancelar-seguimiento-simple', function() {
        $('#form_nuevo_seguimiento_simple').slideUp();
        $('#nuevo_seguimiento_simple').val('');
        $('.btn-agregar-seguimiento-simple').show();
    });
    
    $(document).on('click', '.btn-guardar-seguimiento-simple', function() {
        const seguimiento = $('#nuevo_seguimiento_simple').val().trim();
        const ticketId = $('#seguimiento_ticket_id').val();

        if (!seguimiento) {
            alert('Por favor, escriba el seguimiento');
            return;
        }

        if (!ticketId) {
            alert('Error: No se encontró el ID del ticket');
            return;
        }

        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const $btnGuardar = $(this);
        $btnGuardar.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Guardando...');

        $.ajax({
            url: `/admin/solicitudes/${ticketId}/agregar-seguimiento`,
            type: 'POST',
            data: {
                _token: csrfToken,
                description: seguimiento
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                if (response.success) {
                    const fecha = formatearFecha(response.seguimiento.created_at);
                    const usuario = response.seguimiento.user?.email || 'Usuario';
                    
                    const nuevoSeguimientoHTML = `
                        <div class="card mb-2 border-success seguimiento-item-simple">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted fw-bold">
                                            Nuevo <i class="bi bi-person me-1"></i> ${usuario}
                                        </small>
                                    </div>
                                    <small class="text-muted">${fecha}</small>
                                </div>
                                <p class="mb-0 mt-2 ps-4">${seguimiento}</p>
                                <small class="text-success d-block mt-1">
                                    <i class="bi bi-check-circle"></i> Guardado
                                </small>
                            </div>
                        </div>
                    `;
                    
                    $('#seguimientos_container_simple .alert').remove();
                    $('#seguimientos_container_simple').append(nuevoSeguimientoHTML);
                    
                    renumerarSeguimientos();
                    
                    $('#nuevo_seguimiento_simple').val('');
                    $('#form_nuevo_seguimiento_simple').slideUp();
                    $('.btn-agregar-seguimiento-simple').show();
                    
                    mostrarNotificacionSimple('Seguimiento guardado exitosamente', 'success');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error al guardar el seguimiento';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            },
            complete: function() {
                $btnGuardar.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Guardar');
            }
        });
    });

    function mostrarNotificacionSimple(mensaje, tipo = 'success') {
        const $notificacion = $(`
            <div class="alert alert-${tipo} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="bi bi-${tipo === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append($notificacion);
        
        setTimeout(() => {
            $notificacion.alert('close');
        }, 3000);
    }
});
</script>