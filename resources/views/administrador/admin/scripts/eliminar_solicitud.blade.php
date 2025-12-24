<script>
$(function () {

    let ticketId = null;
    const csrf = $('meta[name="csrf-token"]').attr('content');

    $(document).on('click', '.btn-eliminar', function () {
        ticketId = $(this).data('ticket-id');
        $('#ticketId').text(ticketId);
        $('#ticketDescripcion').text($(this).data('descripcion') || '');

        $('#confirmarEliminar').prop('checked', false);
        $('#btnEliminar').prop('disabled', true);

        $('#eliminarModal').modal('show');
    });

    $('#confirmarEliminar').on('change', function () {
        $('#btnEliminar').prop('disabled', !this.checked);
    });

    $('#btnEliminar').on('click', function () {

        $(this).prop('disabled', true).text('Eliminando...');

        $.ajax({
            url: `/tickets/${ticketId}`,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf },
            success: () => {
                $('#eliminarModal').modal('hide');
                mostrarNotificacion?.('Ticket eliminado', 'success');
                setTimeout(() => location.reload(), 1200);
            },
            error: () => {
                mostrarNotificacion?.('Error al eliminar ticket', 'danger');
                $('#btnEliminar').prop('disabled', false).html('<i class="bi bi-trash"></i> Eliminar');
            }
        });
    });

});
</script>
