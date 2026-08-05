const CrudDelete = {

    bind(crud) {
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            Swal.fire({
                title: 'Delete Module?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }
                $.ajax({
                    url: crud.endpoint + '/delete/' + id,
                    type: 'POST',
                    success(response) {
                        if (!response.success) {
                            APP.error(response.message);
                            return;
                        }
                        APP.success(response.message);
                        crud.reload();
                    },
                    error() {
                        APP.error('Unable to delete record.');
                    }
                });
            });
        });
    }
};