<script>
    // Script para manejar la reasignación de tickets
$(document).ready(function() {
    // Inicializar Select2 para el modal de reasignación
    $('#reasignar_support_personal_id').select2({
        dropdownParent: $('#reasignarSolicitudModal'),
        placeholder: 'Seleccionar personal...',
        allowClear: true,
        width: '100%'
    });

    // Manejar clic en botón de reasignación
    $(document).on('click', '.btn-reasignar', function() {
        const ticketId = $(this).data('ticket-id');
        const ticketData = $(this).data('ticket-data');
        
        // Configurar el formulario
        const formAction = "{{ route('admin.solicitudes.update', ':id') }}".replace(':id', ticketId);
        $('#reasignarForm').attr('action', formAction);
        $('#reasignar_ticket_id').val(ticketId);
        
        // Mostrar información del ticket
        $('#reasignar_employee_name').text(ticketData.employee_name || '—');
        $('#reasignar_description').text(ticketData.description || '—');
        $('#reasignar_building').text(ticketData.building || '—');
        $('#reasignar_department').text(ticketData.department || '—');
        $('#reasignar_created_at').text(ticketData.created_at || '—');
        $('#reasignar_current_support').text(ticketData.current_support || 'Sin asignar');
        
        // Resetear formulario
        $('#reasignar_support_personal_id').val('').trigger('change');
        $('#reasignar_comentario').val('');
        $('#reasignar_service_status_id').val('2');
        
        // Mostrar modal
        $('#reasignarSolicitudModal').modal('show');
    });

    // Manejar confirmación de reasignación
    $('#btnConfirmarReasignacion').click(function(e) {
        e.preventDefault();
        
        const form = $('#reasignarForm');
        const formData = new FormData(form[0]);
        const url = form.attr('action');
        
        // Agregar comentario como nuevo seguimiento si existe
        const comentario = $('#reasignar_comentario').val().trim();
        if (comentario) {
            formData.append('nuevo_seguimiento', `[REASIGNACIÓN] ${comentario}`);
        }
        
        // Mostrar loading
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<i class="bi bi-hourglass-split me-1"></i>Procesando...');
        btn.prop('disabled', true);
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    // Cerrar modal
                    $('#reasignarSolicitudModal').modal('hide');
                    
                    // Mostrar notificación
                    mostrarNotificacion('Ticket reasignado exitosamente', 'success');
                    
                    // Recargar la tabla
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    mostrarNotificacion(response.message || 'Error al reasignar el ticket', 'error');
                }
            },
            error: function(xhr) {
                let message = 'Error en la solicitud';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                mostrarNotificacion(message, 'error');
            },
            complete: function() {
                // Restaurar botón
                btn.html(originalText);
                btn.prop('disabled', false);
            }
        });
    });

    // Función para mostrar notificaciones (usar la misma que ya tienes)
    function mostrarNotificacion(mensaje, tipo = 'success') {
        // Usa tu función existente o implementa una básica
        const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
        const icon = tipo === 'success' ? 'check-circle' : 'exclamation-circle';
        
        const $alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="bi bi-${icon} me-2"></i>
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append($alert);
        setTimeout(() => $alert.alert('close'), 3000);
    }

    // Reinicializar eventos cuando se recarga la tabla
    function reinitializeReasignarEvents() {
        $('.btn-reasignar').off('click').on('click', function() {
            const ticketId = $(this).data('ticket-id');
            const ticketData = $(this).data('ticket-data');
            
            const formAction = "{{ route('admin.solicitudes.update', ':id') }}".replace(':id', ticketId);
            $('#reasignarForm').attr('action', formAction);
            $('#reasignar_ticket_id').val(ticketId);
            
            $('#reasignar_employee_name').text(ticketData.employee_name || '—');
            $('#reasignar_description').text(ticketData.description || '—');
            $('#reasignar_building').text(ticketData.building || '—');
            $('#reasignar_department').text(ticketData.department || '—');
            $('#reasignar_created_at').text(ticketData.created_at || '—');
            $('#reasignar_current_support').text(ticketData.current_support || 'Sin asignar');
            
            $('#reasignar_support_personal_id').val('').trigger('change');
            $('#reasignar_comentario').val('');
            $('#reasignar_service_status_id').val('2');
            
            $('#reasignarSolicitudModal').modal('show');
        });
    }

    // Llamar a la función de reinicialización
    reinitializeReasignarEvents();
});
</script>