<script>
$(document).ready(function() {
    $(document).on('click', '.btn-reasignar', function() {
        const ticketId = $(this).data('ticket-id');
        const ticketData = $(this).data('ticket-data');
        
        $('#reasignar_ticket_id').val(ticketId);
        $('#reasignar_ticket_number').text(ticketId);
        $('#reasignar_current_support').text(ticketData.current_support || 'Sin asignar');
        
        $('#reasignar_support_personal_id').val('');
        
        $('#reasignarSolicitudModal').modal('show');
    });

    $('#btnConfirmarReasignacion').click(function() {
        const ticketId = $('#reasignar_ticket_id').val();
        const supportPersonalId = $('#reasignar_support_personal_id').val();
        
        if (!supportPersonalId) {
            mostrarNotificacion('Por favor selecciona un personal de soporte.', 'error');
            return;
        }
        
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<i class="bi bi-hourglass me-1"></i>Procesando...');
        btn.prop('disabled', true);
        
        $.ajax({
            url: `/admin/solicitudes/${ticketId}/reasignar`,
            type: 'POST',
            data: {
                _method: 'PUT',
                _token: $('meta[name="csrf-token"]').attr('content'),
                support_personal_id: supportPersonalId
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    $('#reasignarSolicitudModal').modal('hide');
                    mostrarNotificacion('Ticket reasignado exitosamente', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarNotificacion(response.message || 'Error al reasignar', 'error');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Error en la solicitud';
                mostrarNotificacion(errorMsg, 'error');
                console.error('Error detallado:', xhr.responseJSON);
            },
            complete: function() {
                btn.html(originalText);
                btn.prop('disabled', false);
            }
        });
    });

    function mostrarNotificacion(mensaje, tipo) {
        const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
        const $alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999;">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        $('body').append($alert);
        setTimeout(() => $alert.alert('close'), 3000);
    }
});
</script>