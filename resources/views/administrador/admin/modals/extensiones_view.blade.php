<div class="modal fade" id="extensionsModal" tabindex="-1" aria-labelledby="extensionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="extensionsModalLabel">
                    <i class="bi bi-telephone-fill me-2"></i>
                    Directorio de Extensiones Telefónicas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="searchExtension" placeholder="Buscar por nombre o extensión...">
                    </div>
                </div>
                
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-striped table-hover" id="extensionsTable">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>Nombre de la extensión</th>
                                <th>Número de extensión</th>
                            </tr>
                        </thead>
                        <tbody id="extensionsTableBody">
                            <tr>
                                <td colspan="2" class="text-center text-muted">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Cargando extensiones...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #extensionsModal .table thead th {
        position: sticky;
        top: 0;
        background-color: #212529;
        color: white;
        z-index: 10;
    }
    
    #extensionsModal .modal-body {
        padding: 1rem;
    }
    
    #extensionsModal .table-responsive {
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }
    
    #extensionsModal .table td, 
    #extensionsModal .table th {
        padding: 0.5rem;
        vertical-align: middle;
    }
    
    #searchExtension:focus {
        box-shadow: none;
        border-color: #86b7fe;
    }
</style>