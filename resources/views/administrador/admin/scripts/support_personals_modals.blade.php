@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function() {
            const personalId = this.getAttribute('data-id');
            const personalName = this.getAttribute('data-name');
            const personalLastnames = this.getAttribute('data-lastnames');
            const personalEmail = this.getAttribute('data-email');
            const personalActive = this.getAttribute('data-active') === '1';
            
            document.getElementById('editForm').action = `/admin/soporte/${personalId}`;
            document.getElementById('edit_name').value = personalName;
            document.getElementById('edit_lastnames').value = personalLastnames;
            document.getElementById('edit_email').value = personalEmail;
            document.getElementById('edit_active').checked = personalActive;
            
            const editModal = new bootstrap.Modal(document.getElementById('editPersonalModal'));
            editModal.show();
        });
    });
});
</script>
@endsection