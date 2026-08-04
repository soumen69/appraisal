const CrudForm = {

    bind(crud) {

        $(crud.form).submit(function (e) {

            e.preventDefault();

            this.submitButton = $('#btnSave');

            this.submitButton.prop('disabled', true);

            $.ajax({

                url: crud.endpoint + '/store',

                type: 'POST',

                data: $(this).serialize(),

                success(response) {

                    $('#btnSave').prop('disabled', false);

                    if (!response.status) {

                        APP.error('Validation failed.');

                        return;

                    }

                    APP.success(response.message);

                    bootstrap.Modal
                        .getInstance(crud.modal)
                        .hide();

                    crud.reload();

                },

                error() {

                    $('#btnSave').prop('disabled', false);

                    APP.error('Request failed.');

                }

            });

        });

    },

    load(crud, id) {

        $.get(

            crud.endpoint + '/edit/' + id,

            function (response) {

                Object.keys(response).forEach(key => {

                    $('[name="' + key + '"]').val(response[key]);

                });

                $('#crudModalTitle').text('Edit');

                new bootstrap.Modal(crud.modal).show();

            }

        );

    }

};