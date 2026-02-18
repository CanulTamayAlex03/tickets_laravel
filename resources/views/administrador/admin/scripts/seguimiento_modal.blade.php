<script>
$(document).ready(function() {

    let seguimientoEditandoId = null;

    function formatearFecha(fechaString) {
        if (!fechaString) return '—';
        const fecha = new Date(fechaString);
        const opciones = { hour: '2-digit', minute: '2-digit' };
        return fecha.toLocaleDateString('es-MX') + ' ' + fecha.toLocaleTimeString('es-MX', opciones);
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
                            <div class="card mb-2 seguimiento-item-simple" data-id="${seguimiento.id}">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <small class="text-muted fw-bold">
                                                ${numero} <i class="bi bi-person me-1"></i> ${usuario}
                                            </small>
                                        </div>
                                        <div>
                                            <small class="text-muted">${fecha}</small>
                                            <button class="btn btn-sm btn-warning ms-2 btn-editar-seguimiento" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </div>
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

    function cargarDatosCompletosSolicitud(ticketId) {
        return $.ajax({
            url: `/admin/solicitudes/${ticketId}/edit`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const ticket = response.ticket;
                    
                    $('#seguimiento_employee_name').text(ticket.employee?.full_name || ticket.employee_name || '—');
                    $('#seguimiento_description').text(ticket.description || '—');
                    $('#seguimiento_building').text(ticket.building?.description || ticket.building_name || '—');
                    $('#seguimiento_department').text(ticket.department?.description || ticket.department_name || '—');
                    $('#seguimiento_created_at').text(formatearFecha(ticket.created_at));
                    
                    if (ticket.support_closing) {
                        $('#seguimiento_support_closing').text(formatearFecha(ticket.support_closing));
                    } else {
                        $('#seguimiento_support_closing').text('No liberado aún');
                    }

                    $('#seguimiento_status').text(ticket.service_status?.description || ticket.status_name || '—');
                    
                    if (ticket.support_personal) {
                        $('#seguimiento_assigned_to').text(`${ticket.support_personal.name} ${ticket.support_personal.lastnames}`);
                    } else if (ticket.support_personal_name) {
                        $('#seguimiento_assigned_to').text(ticket.support_personal_name);
                    } else {
                        $('#seguimiento_assigned_to').text('No asignado');
                    }

                    $('#seguimiento_activity').text(ticket.activity_description || 'No especificada');
                }
            }
        });
    }

    // ================= ABRIR MODAL =================
    $(document).on('click', '.btn-seguimiento', function() {
        const ticketId = $(this).data('ticket-id');
        seguimientoEditandoId = null;

        $('#form_nuevo_seguimiento_simple').hide();
        $('#nuevo_seguimiento_simple').val('');
        $('.btn-agregar-seguimiento-simple').show();
        
        $('#seguimiento_employee_name').text('Cargando...');
        $('#seguimiento_description').text('Cargando...');
        $('#seguimiento_building').text('Cargando...');
        $('#seguimiento_department').text('Cargando...');
        $('#seguimiento_created_at').text('Cargando...');
        $('#seguimiento_support_closing').text('Cargando...');
        $('#seguimiento_status').text('Cargando...');
        $('#seguimiento_assigned_to').text('Cargando...');
        $('#seguimiento_activity').text('Cargando...');
        
        $('#seguimientos_container_simple').html('<div class="alert alert-info">Cargando seguimientos...</div>');

        $('#seguimiento_ticket_id').val(ticketId);

        cargarDatosCompletosSolicitud(ticketId)
            .then(function() {
                return cargarSeguimientosSimples(ticketId);
            })
            .catch(function() {
                $('#seguimientos_container_simple').html('<div class="alert alert-danger">Error al cargar información completa del ticket.</div>');
            });
        $('#seguimientoModal').modal('show');
    });

    // ================= MOSTRAR FORM =================
    $(document).on('click', '.btn-agregar-seguimiento-simple', function() {
        seguimientoEditandoId = null;
        $('#form_nuevo_seguimiento_simple').slideDown();
        $(this).hide();
        $('#nuevo_seguimiento_simple').focus();
    });

    $(document).on('click', '.btn-cancelar-seguimiento-simple', function() {
        seguimientoEditandoId = null;
        $('#form_nuevo_seguimiento_simple').slideUp();
        $('#nuevo_seguimiento_simple').val('');
        $('.btn-agregar-seguimiento-simple').show();
    });

    // ================= EDITAR =================
    $(document).on('click', '.btn-editar-seguimiento', function() {
        const card = $(this).closest('.seguimiento-item-simple');
        seguimientoEditandoId = card.data('id');

        const texto = card.find('p').text().trim();
        $('#nuevo_seguimiento_simple').val(texto);

        $('#form_nuevo_seguimiento_simple').slideDown();
        $('.btn-agregar-seguimiento-simple').hide();
    });

    // ================= GUARDAR =================
    $(document).on('click', '.btn-guardar-seguimiento-simple', function() {
        const seguimiento = $('#nuevo_seguimiento_simple').val().trim();
        const ticketId = $('#seguimiento_ticket_id').val();

        if (!seguimiento) {
            alert('Por favor, escriba el seguimiento');
            return;
        }

        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const $btnGuardar = $(this);
        $btnGuardar.prop('disabled', true).html('Guardando...');

        // ========== EDITAR ==========
        if (seguimientoEditandoId) {
            $.ajax({
                url: `/admin/solicitudes/${ticketId}/seguimiento/${seguimientoEditandoId}/editar`,
                type: 'PUT',
                data: {
                    _token: csrfToken,
                    description: seguimiento
                },
                success: function() {
                    cargarSeguimientosSimples(ticketId);

                    seguimientoEditandoId = null;
                    $('#form_nuevo_seguimiento_simple').slideUp();
                    $('#nuevo_seguimiento_simple').val('');
                    $('.btn-agregar-seguimiento-simple').show();

                    mostrarNotificacionSimple('Seguimiento actualizado', 'success');
                },
                error: function() {
                    alert('Error al actualizar seguimiento');
                },
                complete: function() {
                    $btnGuardar.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Guardar');
                }
            });
            return;
        }

        // ========== CREAR ==========
        $.ajax({
            url: `/admin/solicitudes/${ticketId}/agregar-seguimiento`,
            type: 'POST',
            data: {
                _token: csrfToken,
                description: seguimiento
            },
            success: function(response) {
                if (response.success) {
                    cargarSeguimientosSimples(ticketId);

                    $('#nuevo_seguimiento_simple').val('');
                    $('#form_nuevo_seguimiento_simple').slideUp();
                    $('.btn-agregar-seguimiento-simple').show();

                    mostrarNotificacionSimple('Seguimiento guardado exitosamente', 'success');
                }
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Error al guardar el seguimiento';
                mostrarNotificacionSimple(errorMessage, 'danger');
            },
            complete: function() {
                $btnGuardar.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i> Guardar');
            }
        });
    });

    function mostrarNotificacionSimple(mensaje, tipo = 'success') {
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

});
</script>