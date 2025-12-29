<script>
class TicketEditarModal {
    constructor() {
        this.csrfToken = $('meta[name="csrf-token"]').attr('content');
        this.baseUrl = window.baseUrl || '';
        this.seguimientosCount = 0;
        this.bindEvents();
        this.initSelect2();
    }
    

    bindEvents() {
        $(document)
            .on('click', '.btn-editar-ticket', (e) => this.openModal(e))
            .on('change', '#edit_indicator_type_id', (e) => this.onIndicatorChange(e))
            .on('click', '.btn-agregar-seguimiento', () => this.showAddSeguimiento())
            .on('click', '.btn-cancelar-seguimiento', () => this.hideAddSeguimiento())
            .on('click', '.btn-guardar-seguimiento', () => this.saveSeguimiento())
            .on('submit', '#editarSolicitudForm', (e) => this.submitForm(e));
    }
    

    //initSelect2() {
    //    if ($('.select2-support-personal-edit').length) {
    //        $('.select2-support-personal-edit').select2({
    //            placeholder: "Buscar personal de soporte",
    //            language: "es",
    //            width: '100%',
    //            dropdownParent: $('#editarSolicitudModal')
    //        });
    //    }
    //}
    

    openModal(e) {
        const ticketId = $(e.currentTarget).data('ticket-id');
        this.resetForm();
        this.loadTicketData(ticketId);
    }
    

    resetForm() {
        $('#editarSolicitudForm')[0].reset();
        $('#edit_support_personal_name').text('No asignado');
        $('#edit_support_personal_id').val('');
        
        this.seguimientosCount = 0;
        $('#seguimientos_validation_message').hide();
        
        $('#edit_another_service_id').prop('disabled', true)
            .html('<option value="">Primero seleccione un indicador</option>');
        $('#form_nuevo_seguimiento').hide();
        $('.btn-agregar-seguimiento').show();
    }
    

    loadTicketData(ticketId) {
        $.ajax({
            url: `${this.baseUrl}/admin/solicitudes/${ticketId}/edit`,
            type: 'GET',
            success: (response) => {
                if (response.success) {
                    this.populateModal(response.ticket, response.extra_infos);
                    $('#editarSolicitudModal').modal('show');
                }
            },
            error: (xhr) => {
                console.error('Error al cargar ticket:', xhr);
                this.showNotification('Error al cargar los datos del ticket', 'error');
            }
        });
    }
    

    populateModal(ticket, extraInfos) {
        $('#edit_ticket_id').val(ticket.id);
        
        //$('#edit_support_personal_id').val(ticket.support_personal_id).trigger('change');
        if (ticket.support_personal_id) {
            const personalName = ticket.support_personal?.name 
                ? `${ticket.support_personal.name} ${ticket.support_personal.lastnames}`
                : ticket.support_personal_name || 'Nombre no disponible';

            $('#edit_support_personal_name').text(personalName);
            $('#edit_support_personal_id').val(ticket.support_personal_id);
        } else {
            $('#edit_support_personal_name').text('No asignado');
            $('#edit_support_personal_id').val('');
        }        

        $('#edit_indicator_type_id').val(ticket.indicator_type_id || '');
        $('#edit_activity_description').val(ticket.activity_description || '');
        $('#edit_service_status_id').val(ticket.service_status_id || 1);
        $('#edit_equipment_id').val(ticket.equipment_id || '');
        
        $('#edit_employee_name').text(ticket.employee?.full_name || '—');
        $('#edit_description').text(ticket.description || '—');
        $('#edit_building').text(ticket.building?.description || '—');
        $('#edit_department').text(ticket.department?.description || '—');
        $('#edit_created_at').text(this.formatDate(ticket.created_at));
        $('#edit_retroalimentation').text(ticket.retroalimentation || '—');
        
        $('#edit_stars').html(this.generateStarsHTML(ticket.stars || 0));
        
        if (ticket.support_closing) {
            $('#edit_support_closing').text(this.formatDate(ticket.support_closing));
        } else {
            $('#edit_support_closing').text('No liberado aún');
        }
        
        if (ticket.indicator_type_id) {
            this.loadServicesByIndicator(ticket.indicator_type_id, ticket.another_service_id);
        }
        
        this.loadSeguimientos(extraInfos || []);

        this.validateSeguimientos();
    }
    

    onIndicatorChange(e) {
        const indicatorId = $(e.currentTarget).val();
        this.loadServicesByIndicator(indicatorId);
    }

    validateSeguimientos() {
        if (this.seguimientosCount === 0) {
            $('#seguimientos_validation_message').show();
            return false;
        } else {
            $('#seguimientos_validation_message').hide();
            return true;
        }
    }

    loadServicesByIndicator(indicatorId, selectedServiceId = null) {
        if (!indicatorId) {
            $('#edit_another_service_id').prop('disabled', true)
                .html('<option value="">Primero seleccione un indicador</option>');
            return;
        }
        
        $.ajax({
            url: `${this.baseUrl}/admin/solicitudes/servicios-por-indicador/${indicatorId}`,
            type: 'GET',
            success: (services) => {
                let options = '<option value="">Seleccionar servicio</option>';
                services.forEach((service) => {
                    const selected = (selectedServiceId == service.id) ? 'selected' : '';
                    options += '<option value="' + service.id + '" ' + selected + '>' + 
                              service.description + '</option>';
                });
                
                $('#edit_another_service_id').prop('disabled', false).html(options);
            }
        });
    }
    

    loadSeguimientos(seguimientos) {
        const container = $('#seguimientos_container');
        container.empty();

        this.seguimientosCount = seguimientos.length;
        
        if (seguimientos.length === 0) {
            container.html('<div class="alert alert-info">No hay seguimientos registrados.</div>');
            return;
        }
        
        seguimientos.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        
        seguimientos.forEach((seguimiento, index) => {
            const fecha = this.formatDate(seguimiento.created_at);
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
        this.validateSeguimientos();
    }
    

    showAddSeguimiento() {
        $('#form_nuevo_seguimiento').slideDown();
        $('.btn-agregar-seguimiento').hide();
        $('#nuevo_seguimiento').focus();
    }
    

    hideAddSeguimiento() {
        $('#form_nuevo_seguimiento').slideUp();
        $('#nuevo_seguimiento').val('');
        $('.btn-agregar-seguimiento').show();
    }

    saveSeguimiento() {
        const seguimiento = $('#nuevo_seguimiento').val().trim();
        const ticketId = $('#edit_ticket_id').val();
        
        if (!seguimiento) {
            this.showNotification('Por favor, escriba el seguimiento', 'error');
            return;
        }
        
        if (!ticketId) {
            this.showNotification('Error: No se encontró el ID del ticket', 'error');
            return;
        }
        
        const $btnGuardar = $('.btn-guardar-seguimiento');
        $btnGuardar.prop('disabled', true)
            .html('<i class="bi bi-hourglass-split me-1"></i> Guardando...');
        
        $.ajax({
            url: `${this.baseUrl}/admin/solicitudes/${ticketId}/agregar-seguimiento`,
            type: 'POST',
            data: {
                _token: this.csrfToken,
                description: seguimiento
            },
            success: (response) => {
                if (response.success) {
                    this.seguimientosCount++;
                    this.addSeguimientoToList(response.seguimiento, seguimiento);
                    this.hideAddSeguimiento();
                    this.showNotification('Seguimiento guardado exitosamente', 'success');
                    this.renumberSeguimientos();

                    this.validateSeguimientos();
                } else {
                    this.showNotification('Error: ' + response.message, 'error');
                }
            },
            error: (xhr) => {
                const errorMessage = xhr.responseJSON?.message || 'Error al guardar el seguimiento';
                this.showNotification(errorMessage, 'error');
            },
            complete: () => {
                $btnGuardar.prop('disabled', false)
                    .html('<i class="bi bi-check-circle me-1"></i> Guardar');
            }
        });
    }
    

    addSeguimientoToList(seguimientoData, texto) {
        const fecha = this.formatDate(seguimientoData.created_at);
        const usuario = seguimientoData.user?.email || 'Usuario';
        
        const nuevoSeguimientoHTML = `
            <div class="card mb-2 border-success seguimiento-item">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <small class="text-muted fw-bold">
                                Nuevo <i class="bi bi-person me-1"></i> ${usuario}
                            </small>
                        </div>
                        <small class="text-muted">${fecha}</small>
                    </div>
                    <p class="mb-0 mt-2 ps-4">${texto}</p>
                    <small class="text-success d-block mt-1">
                        <i class="bi bi-check-circle"></i> Guardado
                    </small>
                </div>
            </div>
        `;
        
        $('#seguimientos_container .alert.alert-info').remove();
        
        $('#seguimientos_container').append(nuevoSeguimientoHTML);
    }
    

    renumberSeguimientos() {
        $('.seguimiento-item').each((index, element) => {
            const $texto = $(element).find('.text-muted.fw-bold');
            const textoActual = $texto.html();
            const nuevoTexto = textoActual.replace(/^\d+\s|^Nuevo\s/, (index + 1) + ' ');
            $texto.html(nuevoTexto);
        });
    }
    
    validateForm() {
        let isValid = true;
        let firstErrorField = null;
        
        const requiredFields = [
            '#edit_indicator_type_id',
            '#edit_another_service_id',
            '#edit_equipment_id',
            '#edit_activity_description',
            '#edit_service_status_id'
        ];
        
        requiredFields.forEach(field => {
            const $field = $(field);
            const value = $field.val();
            
            if ($field.is(':disabled') && field === '#edit_another_service_id') {
                const indicatorSelected = $('#edit_indicator_type_id').val();
                if (indicatorSelected) {
                    this.showNotification('Debe seleccionar un servicio para el indicador elegido', 'error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = $field;
                }
            } else if ($field.prop('required') && (!value || value.trim() === '')) {
                $field.addClass('is-invalid');
                isValid = false;
                if (!firstErrorField) firstErrorField = $field;
            } else {
                $field.removeClass('is-invalid');
            }
        });
        
        const supportPersonalId = $('#edit_support_personal_id').val();
        if (!supportPersonalId) {
            this.showNotification('Debe asignar personal de soporte antes de guardar', 'error');
            $('#edit_support_personal_display').addClass('border-danger');
            isValid = false;
        } else {
            $('#edit_support_personal_display').removeClass('border-danger');
        }
        
        if (this.seguimientosCount === 0) {
            this.showNotification('Debe agregar al menos un seguimiento técnico antes de guardar', 'error');
            
            $('#lista_seguimientos').addClass('border border-danger rounded p-2');
            
            $('html, body').animate({
                scrollTop: $('#lista_seguimientos').offset().top - 100
            }, 500);
            
            isValid = false;
        } else {
            $('#lista_seguimientos').removeClass('border border-danger rounded p-2');
        }
        
        if (firstErrorField && !isValid) {
            $('html, body').animate({
                scrollTop: firstErrorField.offset().top - 100
            }, 500);
        }
        
        return isValid;
    }

    submitForm(e) {
        e.preventDefault();

        if (!this.validateForm()) {
            return;
        }
        
        const ticketId = $('#edit_ticket_id').val();
        const formData = $(e.currentTarget).serialize();
        const $submitBtn = $(e.currentTarget).find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        
        $submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Guardando...');
        
        $.ajax({
            url: `${this.baseUrl}/admin/solicitudes/${ticketId}`,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'X-HTTP-Method-Override': 'PUT'
            },
            success: (response) => {
                if (response.success) {
                    $('#editarSolicitudModal').modal('hide');
                    
                    $submitBtn.prop('disabled', false).html(originalBtnText);
                    
                    setTimeout(() => {
                        this.showNotification(response.message || 'Cambios guardados exitosamente', 'success');
                        
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }, 300);
                } else {
                    $submitBtn.prop('disabled', false).html(originalBtnText);
                    
                    this.showNotification('Error: ' + response.message, 'error');
                }
            },
            error: (xhr) => {
                $submitBtn.prop('disabled', false).html(originalBtnText);
                
                let errorMessage = 'Error al actualizar la solicitud';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('<br>');
                }
                
                this.showNotification(errorMessage, 'error');
            }
        });
    }
    

    formatDate(fechaString) {
        if (!fechaString) return '—';
        
        const fecha = new Date(fechaString);
        const opciones = {
            hour: '2-digit',
            minute: '2-digit'
        };
        
        return fecha.toLocaleDateString('es-MX') + ' ' + 
               fecha.toLocaleTimeString('es-MX', opciones);
    }
    
    generateStarsHTML(cantidad) {
        if (!cantidad || cantidad === 0) {
            return '<span class="text-muted">Sin calificación</span>';
        }
        
        let estrellasHTML = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= cantidad) {
                estrellasHTML += '<i class="bi bi-star-fill text-warning me-1"></i>';
            } else {
                estrellasHTML += '<i class="bi bi-star text-secondary me-1"></i>';
            }
        }
        
        estrellasHTML += ` <small class="text-muted">(${cantidad}/5)</small>`;
        return estrellasHTML;
    }
    

    showNotification(mensaje, tipo = 'success') {
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
}

$(document).ready(function() {
    if ($('#editarSolicitudModal').length > 0) {
        window.ticketEditarModal = new TicketEditarModal();
        
        if (!$('#estilos-notificacion-edicion').length) {
            $('head').append(`
                <style id="estilos-notificacion-edicion">
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
                    
                    .alert-info {
                        background-color: #cff4fc;
                        color: #055160;
                        border-left: 4px solid #055160;
                    }
                </style>
            `);
        }
    }
});
</script>