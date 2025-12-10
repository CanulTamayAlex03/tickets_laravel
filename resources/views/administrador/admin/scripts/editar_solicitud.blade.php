<script>
// MÓDULO PARA EL MODAL DE EDICIÓN DE TICKETS
class TicketEditarModal {
    constructor() {
        this.csrfToken = $('meta[name="csrf-token"]').attr('content');
        this.baseUrl = window.baseUrl || '';
        this.bindEvents();
        this.initSelect2();
    }
    
    /**
     * Inicializar eventos
     */
    bindEvents() {
        // Delegación de eventos para botones dinámicos
        $(document)
            .on('click', '.btn-editar-ticket', (e) => this.openModal(e))
            .on('change', '#edit_indicator_type_id', (e) => this.onIndicatorChange(e))
            .on('click', '.btn-agregar-seguimiento', () => this.showAddSeguimiento())
            .on('click', '.btn-cancelar-seguimiento', () => this.hideAddSeguimiento())
            .on('click', '.btn-guardar-seguimiento', () => this.saveSeguimiento())
            .on('submit', '#editarSolicitudForm', (e) => this.submitForm(e));
    }
    
    /**
     * Inicializar Select2 para personal de soporte
     */
    initSelect2() {
        if ($('.select2-support-personal-edit').length) {
            $('.select2-support-personal-edit').select2({
                placeholder: "Buscar personal de soporte",
                language: "es",
                width: '100%',
                dropdownParent: $('#editarSolicitudModal')
            });
        }
    }
    
    /**
     * Abrir modal de edición
     */
    openModal(e) {
        const ticketId = $(e.currentTarget).data('ticket-id');
        this.resetForm();
        this.loadTicketData(ticketId);
    }
    
    /**
     * Resetear formulario
     */
    resetForm() {
        $('#editarSolicitudForm')[0].reset();
        $('#edit_another_service_id').prop('disabled', true)
            .html('<option value="">Primero seleccione un indicador</option>');
        $('#form_nuevo_seguimiento').hide();
        $('.btn-agregar-seguimiento').show();
    }
    
    /**
     * Cargar datos del ticket
     */
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
                alert('Error al cargar los datos del ticket');
            }
        });
    }
    
    /**
     * Llenar modal con datos del ticket
     */
    populateModal(ticket, extraInfos) {
        // ID del ticket
        $('#edit_ticket_id').val(ticket.id);
        
        // Selects
        $('#edit_support_personal_id').val(ticket.support_personal_id).trigger('change');
        $('#edit_indicator_type_id').val(ticket.indicator_type_id || '');
        $('#edit_activity_description').val(ticket.activity_description || '');
        $('#edit_service_status_id').val(ticket.service_status_id || 1);
        $('#edit_equipment_id').val(ticket.equipment_id || '');
        
        // Información de solo lectura
        $('#edit_employee_name').text(ticket.employee?.full_name || '—');
        $('#edit_description').text(ticket.description || '—');
        $('#edit_building').text(ticket.building?.description || '—');
        $('#edit_department').text(ticket.department?.description || '—');
        $('#edit_created_at').text(this.formatDate(ticket.created_at));
        $('#edit_retroalimentation').text(ticket.retroalimentation || '—');
        
        // Calificación con estrellas
        $('#edit_stars').html(this.generateStarsHTML(ticket.stars || 0));
        
        // Fecha de cierre
        if (ticket.support_closing) {
            $('#edit_support_closing').text(this.formatDate(ticket.support_closing));
        } else {
            $('#edit_support_closing').text('No liberado aún');
        }
        
        // Cargar servicios si hay indicador
        if (ticket.indicator_type_id) {
            this.loadServicesByIndicator(ticket.indicator_type_id, ticket.another_service_id);
        }
        
        // Cargar seguimientos
        this.loadSeguimientos(extraInfos || []);
    }
    
    /**
     * Manejar cambio de indicador
     */
    onIndicatorChange(e) {
        const indicatorId = $(e.currentTarget).val();
        this.loadServicesByIndicator(indicatorId);
    }
    
    /**
     * Cargar servicios por indicador
     */
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
    
    /**
     * Cargar seguimientos en el modal
     */
    loadSeguimientos(seguimientos) {
        const container = $('#seguimientos_container');
        container.empty();
        
        if (seguimientos.length === 0) {
            container.html('<div class="alert alert-info">No hay seguimientos registrados.</div>');
            return;
        }
        
        // Ordenar por fecha (más antiguo primero)
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
    }
    
    /**
     * Mostrar formulario para agregar seguimiento
     */
    showAddSeguimiento() {
        $('#form_nuevo_seguimiento').slideDown();
        $('.btn-agregar-seguimiento').hide();
        $('#nuevo_seguimiento').focus();
    }
    
    /**
     * Ocultar formulario de seguimiento
     */
    hideAddSeguimiento() {
        $('#form_nuevo_seguimiento').slideUp();
        $('#nuevo_seguimiento').val('');
        $('.btn-agregar-seguimiento').show();
    }
    
    /**
     * Guardar nuevo seguimiento
     */
    saveSeguimiento() {
        const seguimiento = $('#nuevo_seguimiento').val().trim();
        const ticketId = $('#edit_ticket_id').val();
        
        if (!seguimiento) {
            alert('Por favor, escriba el seguimiento');
            return;
        }
        
        if (!ticketId) {
            alert('Error: No se encontró el ID del ticket');
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
                    this.addSeguimientoToList(response.seguimiento, seguimiento);
                    this.hideAddSeguimiento();
                    this.showNotification('Seguimiento guardado exitosamente', 'success');
                    this.renumberSeguimientos();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: (xhr) => {
                const errorMessage = xhr.responseJSON?.message || 'Error al guardar el seguimiento';
                alert(errorMessage);
            },
            complete: () => {
                $btnGuardar.prop('disabled', false)
                    .html('<i class="bi bi-check-circle me-1"></i> Guardar');
            }
        });
    }
    
    /**
     * Agregar seguimiento a la lista
     */
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
        
        $('#seguimientos_container').append(nuevoSeguimientoHTML);
        $('#seguimientos_container .alert').remove();
    }
    
    /**
     * Renumerar seguimientos
     */
    renumberSeguimientos() {
        $('.seguimiento-item').each((index, element) => {
            const $texto = $(element).find('.text-muted.fw-bold');
            const textoActual = $texto.html();
            const nuevoTexto = textoActual.replace(/^\d+\s|^Nuevo\s/, (index + 1) + ' ');
            $texto.html(nuevoTexto);
        });
    }
    
    /**
     * Enviar formulario de edición
     */
    submitForm(e) {
        e.preventDefault();
        
        const ticketId = $('#edit_ticket_id').val();
        const formData = $(e.currentTarget).serialize();
        
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
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: (xhr) => {
                const errorMessage = xhr.responseJSON?.message || 'Error al actualizar la solicitud';
                alert(errorMessage);
            }
        });
    }
    
    // ============================================
    // FUNCIONES DE UTILERÍA
    // ============================================
    
    /**
     * Formatear fecha
     */
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
    
    /**
     * Generar HTML de estrellas
     */
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
    
    /**
     * Mostrar notificación
     */
    showNotification(mensaje, tipo = 'success') {
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
}

$(document).ready(function() {
    if ($('#editarSolicitudModal').length > 0) {
        window.ticketEditarModal = new TicketEditarModal();
    }
});
</script>