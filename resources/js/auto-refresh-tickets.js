class TicketAutoRefresh {
    constructor() {
        this.isActive = false;
        this.interval = null;
        this.lastUpdateTime = null;
        this.updateFrequency = 15000; // 15 segundos
        this.isFetching = false;
        
        // Elemento para mostrar estado
        this.statusIndicator = null;
        this.createStatusIndicator();
        
        this.init();
    }
    
    createStatusIndicator() {
        this.statusIndicator = $(`
            <div id="auto-refresh-status" 
                 style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; 
                        background: #343a40; color: white; padding: 8px 12px; 
                        border-radius: 20px; font-size: 12px; display: none;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                <i class="bi bi-arrow-clockwise me-1"></i>
                <span id="refresh-status-text">Actualizando...</span>
                <div class="spinner-border spinner-border-sm ms-2" role="status" style="display: none;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `).appendTo('body');
    }
    
    init() {
        // Solo activar en la vista de administración de solicitudes
        if (this.shouldActivate()) {
            this.start();
            this.setupEventListeners();
        }
    }
    
    shouldActivate() {
        // Verificar si estamos en la página correcta
        const path = window.location.pathname;
        return path.includes('admin/admin_solicitudes') && 
               this.isViewingNewTickets();
    }
    
    isViewingNewTickets() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        // Activar solo para "nuevo" o vista por defecto
        return !status || status === 'nuevo';
    }
    
    start() {
        if (this.isActive) return;
        
        this.isActive = true;
        this.showStatusIndicator('Auto-refresh activado');
        
        // Primer refresh inmediato
        setTimeout(() => this.checkForUpdates(), 1000);
        
        // Configurar intervalo
        this.interval = setInterval(() => this.checkForUpdates(), this.updateFrequency);
        
        console.log('Auto-refresh de tickets activado');
    }
    
    stop() {
        if (!this.isActive) return;
        
        this.isActive = false;
        if (this.interval) {
            clearInterval(this.interval);
            this.interval = null;
        }
        
        this.showStatusIndicator('Auto-refresh pausado', 2000);
        console.log('Auto-refresh de tickets detenido');
    }
    
    async checkForUpdates() {
        if (this.isFetching || !this.isActive) return;
        
        this.isFetching = true;
        this.showStatusIndicator('Buscando nuevos tickets...', 1000);
        
        try {
            const url = new URL(window.location.href);
            url.searchParams.set('partial', 'true');
            url.searchParams.set('_', Date.now()); // Evitar caché
            
            const response = await fetch(url.toString());
            
            if (!response.ok) throw new Error('Error en la respuesta');
            
            const data = await response.json();
            
            // Verificar si hay cambios
            if (this.hasChanges(data)) {
                this.updateTable(data);
                this.showNotification('Nuevos tickets disponibles');
            }
            
        } catch (error) {
            console.error('Error al actualizar tickets:', error);
            this.showStatusIndicator('Error al actualizar', 2000);
        } finally {
            this.isFetching = false;
        }
    }
    
    hasChanges(data) {
        // Comparar con el contenido actual de la tabla
        const currentTicketIds = this.getCurrentTicketIds();
        const newTicketIds = this.extractTicketIdsFromHTML(data.table_html);
        
        // Verificar si hay tickets nuevos
        const hasNewTickets = newTicketIds.some(id => !currentTicketIds.includes(id));
        
        return hasNewTickets || newTicketIds.length !== currentTicketIds.length;
    }
    
    getCurrentTicketIds() {
        const ids = [];
        $('table tbody tr').each(function() {
            const id = $(this).find('td:first').text().trim();
            if (id && !isNaN(id)) {
                ids.push(parseInt(id));
            }
        });
        return ids;
    }
    
    extractTicketIdsFromHTML(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const rows = tempDiv.querySelectorAll('table tbody tr');
        const ids = [];
        
        rows.forEach(row => {
            const idCell = row.querySelector('td:first-child');
            if (idCell) {
                const id = parseInt(idCell.textContent.trim());
                if (!isNaN(id)) ids.push(id);
            }
        });
        
        return ids;
    }
    
    updateTable(data) {
        // Actualizar la tabla
        $('table tbody').html($(data.table_html).find('tbody').html());
        
        // Actualizar paginación
        $('.pagination').replaceWith(data.pagination_html || '');
        
        // Actualizar metadata
        const metadataElement = $('small.text-muted').first();
        if (metadataElement.length && data.metadata_html) {
            metadataElement.html(data.metadata_html);
        }
        
        // Re-inicializar eventos de los botones
        this.reinitializeButtons();
        
        this.showStatusIndicator('Tabla actualizada', 1500);
    }
    
    reinitializeButtons() {
        // Re-inicializar eventos para botones dinámicos
        $(document).off('click', '.btn-asignar');
        $(document).on('click', '.btn-asignar', function() {
            const ticketData = $(this).data('ticket-data');
            const ticketId = $(this).data('ticket-id');
            // Tu código existente para abrir modal de asignación
            // ...
        });
    }
    
    showStatusIndicator(message, duration = 3000) {
        const indicator = $('#auto-refresh-status');
        const text = $('#refresh-status-text');
        const spinner = indicator.find('.spinner-border');
        
        text.text(message);
        indicator.fadeIn(200);
        
        spinner.hide();
        
        if (duration > 0) {
            setTimeout(() => {
                if (this.isActive) {
                    indicator.fadeOut(500);
                }
            }, duration);
        }
    }
    
    showNotification(message) {
        const toast = $(`
            <div class="toast align-items-center text-bg-primary border-0" 
                 role="alert" aria-live="assertive" aria-atomic="true"
                 style="position: fixed; top: 20px; right: 20px; z-index: 10000;">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-bell me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                            data-bs-dismiss="toast"></button>
                </div>
            </div>
        `).appendTo('body');
        
        const bsToast = new bootstrap.Toast(toast[0], { delay: 3000 });
        bsToast.show();
        
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
    
    setupEventListeners() {
        $(document).on('show.bs.modal', '.modal', () => {
            this.stop();
        });
        
        $(document).on('hidden.bs.modal', '.modal', () => {
            if (this.shouldActivate()) {
                setTimeout(() => this.start(), 1000);
            }
        });
        
        $(document).on('click', '.btn-filter', () => {
            setTimeout(() => {
                if (this.shouldActivate()) {
                    this.start();
                } else {
                    this.stop();
                }
            }, 500);
        });
        
        $(window).on('focus', () => {
            if (this.shouldActivate() && !this.isActive) {
                this.start();
                this.checkForUpdates();
            }
        });
        
        $(window).on('blur', () => {
            this.stop();
        });
    }
}

$(document).ready(function() {
    const controlButton = $(`
        <button id="toggle-auto-refresh" class="btn btn-sm btn-outline-secondary ms-2" 
                title="Activar/Desactivar auto-refresh">
            <i class="bi bi-arrow-clockwise"></i>
            <span>Auto-refresh</span>
        </button>
    `);
    
    $('.btn-primary[data-bs-target="#filterModal"]').after(controlButton);
    
    const autoRefresh = new TicketAutoRefresh();
    
    $('#toggle-auto-refresh').click(function() {
        if (autoRefresh.isActive) {
            autoRefresh.stop();
            $(this).removeClass('btn-success').addClass('btn-outline-secondary');
            $(this).find('i').removeClass('bi-pause').addClass('bi-arrow-clockwise');
        } else {
            autoRefresh.start();
            $(this).removeClass('btn-outline-secondary').addClass('btn-success');
            $(this).find('i').removeClass('bi-arrow-clockwise').addClass('bi-pause');
        }
    });
});