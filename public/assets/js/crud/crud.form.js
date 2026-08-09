const CrudForm = {

    bind(crud) {

        $(crud.form).submit(function (e) {
            e.preventDefault();
            this.submitButton = $('#btnSave');
            this.submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
            $.ajax({
                url: crud.editId
                    ? crud.endpoint + '/update/' + crud.editId
                    : crud.endpoint + '/store', type: 'POST',
                data: $(this).serialize(),
                success(response) {
                    $('#btnSave').prop('disabled', false).html('Save');
                    if (!response.success) {
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').html('');
                        if (response.errors) {
                            Object.keys(response.errors).forEach(function (field) {
                                $('[name="' + field + '"]')
                                    .addClass('is-invalid')
                                    .next('.invalid-feedback')
                                    .html(response.errors[field]);
                            });
                        }
                        APP.error(response.message);
                        return;
                    }
                    APP.success(response.message);
                    bootstrap.Modal
                        .getInstance(crud.modal)
                        .hide();
                    crud.reload();
                },
                error() {
                    $('#btnSave').prop('disabled', false).html('Save');
                    APP.error('Request failed.');
                }
            });
        });
    },

    load(crud, id) {
        $.get(
            crud.endpoint + '/edit/' + id,
            function (response) {
                Object.keys(response.data).forEach(function (key) {
                    $('[name="' + key + '"]').val(response.data[key]);
                });
                $('#crudModalTitle').text('Edit');
                crud.editId = id;
                new bootstrap.Modal(crud.modal).show();
            }
        );
    },

    reset(crud) {
        crud.form.reset();
        crud.editId = null;
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').html('');
        $('#btnSave').prop('disabled', false).html('Save');
    }
};


