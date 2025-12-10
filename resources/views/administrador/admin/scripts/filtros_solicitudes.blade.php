<script>
    $(document).ready(function() {
        // Inicializar Select2 para filtros existentes
        $('.select2-employee').select2({
            placeholder: "Buscar por nombre o nómina",
            language: "es",
            width: '100%',
            dropdownParent: $('#filterModal')
        });

        $('.select2-building').select2({
            placeholder: "Seleccionar edificio",
            language: "es",
            width: '100%',
            dropdownParent: $('#filterModal')
        });

        $('.select2-department').select2({
            placeholder: "Seleccionar departamento",
            language: "es",
            width: '100%',
            dropdownParent: $('#filterModal')
        });

        // Verificar si hay parámetros de filtro en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const hasFilters = urlParams.has('search') ||
            urlParams.has('employee_id') ||
            urlParams.has('building_id') ||
            urlParams.has('department_id') ||
            urlParams.has('status_filter');

        // Si hay filtros aplicados, mostrar indicador
        if (hasFilters) {
            $('.btn[data-bs-target="#filterModal"]').addClass('btn-warning').removeClass('btn-primary')
                .html('<i class="bi bi-funnel-fill me-1"></i> Filtros Activos');
        }
    });
</script>