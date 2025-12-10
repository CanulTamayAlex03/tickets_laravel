<script>
$(document).ready(function() {
    $('.select2-support-personal').select2({
        placeholder: "Buscar personal de soporte",
        language: "es",
        width: '100%',
        dropdownParent: $('#asignarSolicitudModal')
    });

    let focusInterval;

    $(document).on('select2:open', '.select2-support-personal', function(e) {
        if (focusInterval) clearInterval(focusInterval);
        
        focusInterval = setInterval(function() {
            const searchField = $('.select2-container--open .select2-search__field');
            if (searchField.length) {
                searchField.focus();
                searchField[0].select();
                clearInterval(focusInterval);
                console.log('Campo enfocado exitosamente');
            }
        }, 50);
    });

    $('.btn-asignar').on('click', function() {
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

    $('#asignarSolicitudModal').on('hidden.bs.modal', function() {
        if (focusInterval) clearInterval(focusInterval);
    });

    $('#asignarSolicitudForm').on('submit', function(e) {
        e.preventDefault();
        
        const ticketId = $('#ticket_id').val();
        const formData = $(this).serialize();
        
        $.ajax({
            url: `{{ route('admin.solicitudes.update', '') }}/${ticketId}`,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                // Cerrar el modal primero
                $('#asignarSolicitudModal').modal('hide');
                
                // Mostrar notificación similar a la del seguimiento
                mostrarNotificacionAsignacion('¡Solicitud asignada exitosamente!', 'success');
                
                setTimeout(() => {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                let errorMessage = 'Error al asignar la solicitud';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                mostrarNotificacionAsignacion(errorMessage, 'error');
            }
        });
    });

    // Función para mostrar notificación similar a la del seguimiento
    function mostrarNotificacionAsignacion(mensaje, tipo = 'success') {
        const $notificacion = $(`
            <div class="alert alert-${tipo} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 1056; min-width: 300px;">
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

    // Eliminar la función showNotification antigua si no se usa en otro lugar
    // function showNotification(message, type) {
    //     if (typeof toastr !== 'undefined') {
    //         toastr[type](message);
    //     } else {
    //         alert(message);
    //     }
    // }
});
</script>