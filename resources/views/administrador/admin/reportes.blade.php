@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-file-excel me-2"></i>Reportes - Descargar Excel</h3>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.reportes.export') }}" method="POST" id="exportForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3"><i class="fas fa-calendar-alt me-2"></i>Rango de Fechas</h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="start_date" class="form-label">Fecha Inicial</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="end_date" class="form-label">Fecha Final</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">Estatus</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Todos los estatus</option>
                                        <option value="nuevo">Nuevo</option>
                                        <option value="atendiendo">Atendiendo</option>
                                        <option value="cerrado">Cerrado</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="completado">Completado</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3"><i class="fas fa-filter me-2"></i>Filtros Adicionales</h5>
                                
                                <div class="mb-3">
                                    <label for="building_id" class="form-label">Edificio</label>
                                    <select class="form-select" id="building_id" name="building_id">
                                        <option value="">Todos los edificios</option>
                                        @foreach($buildings as $building)
                                            <option value="{{ $building->id }}">{{ $building->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Departamento</label>
                                    <select class="form-select" id="department_id" name="department_id">
                                        <option value="">Todos los departamentos</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Información incluida:</strong> 
                            ID Ticket, Fecha Recepción, Fecha Cierre, Tiempo atención (días L-V), Estatus, 
                            Solicitud (detalle), Edificio, Departamento, Quién solicita, Indicador, 
                            Tipo servicio, Actividad realizada, Personal, Último seguimiento, Estrellas.
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg" id="exportBtn">
                                <i class="fas fa-download me-2"></i>Descargar Reporte Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('exportForm').addEventListener('submit', function(e) {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (end < start) {
            e.preventDefault();
            alert('Error: La fecha final no puede ser anterior a la fecha inicial.');
            return false;
        }
    }
    
    // Mostrar mensaje de procesamiento
    const btn = document.getElementById('exportBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando Excel...';
});
</script>
@endpush

@push('styles')
<style>
.btn-success {
    background-color: #28a745;
    border-color: #28a745;
    padding: 12px 30px;
    font-weight: 600;
    min-width: 250px;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    font-size: 1.25rem;
}

.form-label {
    font-weight: 600;
}

.alert-info {
    background-color: #e7f3fe;
    border-left: 4px solid #0066cc;
}
</style>
@endpush