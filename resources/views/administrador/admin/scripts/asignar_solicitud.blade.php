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
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        
        $submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Procesando...');
        
        $.ajax({
            url: `{{ route('admin.solicitudes.update', '') }}/${ticketId}`,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                $('#asignarSolicitudModal').modal('hide');
                
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                $('#asignarSolicitudForm')[0].reset();
                $('#support_personal_id').val('').trigger('change');
                
                setTimeout(function() {
                    mostrarNotificacion('¡Solicitud asignada exitosamente!', 'success');
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }, 300);
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                let errorMessage = 'Error al asignar la solicitud';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                }
                
                mostrarNotificacion(errorMessage, 'error');
            }
        });
    });

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

    if (!$('#estilos-notificacion').length) {
        $('head').append(`
            <style id="estilos-notificacion">
                .alert.position-fixed {
                    animation: slideInRight 0.3s ease-out;
                    border: none;
                    border-radius: 8px;
                    font-size: 0.9rem;
                    padding: 12px 20px;
                }
                
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                
                .alert.position-fixed .btn-close {
                    padding: 0.8rem 1rem;
                    font-size: 0.8rem;
                }
                
                .alert-success {
                    background-color: #d1e7dd;
                    color: #0f5132;
                    border-left: 4px solid #0f5132;
                }
                
                .alert-danger {
                    background-color: #f8d7da;
                    color: #842029;
                    border-left: 4px solid #842029;
                }
            </style>
        `);
    }
});
</script>