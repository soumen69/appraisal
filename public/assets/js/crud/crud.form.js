const CrudForm = {

    bind(crud) {

        if (!crud.form) {
            return;
        }

        $(crud.form)
            .off('submit.crudForm')
            .on(
                'submit.crudForm',
                function (e) {

                    e.preventDefault();

                    const form = this;

                    const $button =
                        $('#btnSave');

                    $button
                        .prop('disabled', true)
                        .html(
                            '<span class="spinner-border spinner-border-sm me-2"></span>Saving...'
                        );


                    $.ajax({

                        url: crud.editId
                            ? crud.endpoint +
                            '/update/' +
                            crud.editId
                            : crud.endpoint +
                            '/store',

                        type: 'POST',

                        data: $(form).serialize(),

                        success(response) {

                            $button
                                .prop(
                                    'disabled',
                                    false
                                )
                                .html('Save');


                            if (!response.success) {

                                CrudForm.clearErrors();

                                if (response.errors) {

                                    CrudForm.showErrors(
                                        response.errors
                                    );
                                }

                                APP.error(
                                    response.message ||
                                    'Please correct the highlighted fields.'
                                );

                                return;
                            }


                            APP.success(
                                response.message
                            );


                            const modal =
                                bootstrap.Modal
                                    .getInstance(
                                        crud.modal
                                    );

                            if (modal) {
                                modal.hide();
                            }


                            crud.reload();
                        },

                        error(xhr) {

                            $button
                                .prop(
                                    'disabled',
                                    false
                                )
                                .html('Save');


                            if (
                                xhr.responseJSON &&
                                xhr.responseJSON.errors
                            ) {

                                CrudForm.clearErrors();

                                CrudForm.showErrors(
                                    xhr.responseJSON.errors
                                );

                                APP.error(
                                    xhr.responseJSON.message ||
                                    'Please correct the highlighted fields.'
                                );

                                return;
                            }


                            if (xhr.status === 403) {

                                APP.error(
                                    'You are not authorized.'
                                );

                                return;
                            }


                            APP.error(
                                'Request failed.'
                            );
                        }
                    });
                }
            );
    },


    load(crud, id) {

        if (!crud.form || !crud.modal) {
            return;
        }

        $.get(
            crud.endpoint + '/edit/' + id,

            function (response) {

                if (!response.success) {

                    APP.error(
                        response.message ||
                        'Unable to load record.'
                    );

                    return;
                }


                /*
                 * Populate the form first.
                 */
                CrudForm.populate(
                    crud.form,
                    response.data
                );

                $(document).trigger(
                    'crud:editLoaded',
                    [response.data, crud]
                );
                
                $('#crudModalTitle')
                    .text(
                        `Edit ${crud.entity}`
                    );


                crud.editId = id;


                const modal =
                    bootstrap.Modal
                        .getOrCreateInstance(
                            crud.modal
                        );


                /*
                 * Show modal after form hydration.
                 */
                modal.show();


                /*
                 * Re-trigger Select2 after Bootstrap has
                 * finished rendering the modal.
                 *
                 * This is especially important for
                 * multi-select fields inside modals.
                 */
                $(crud.modal)
                    .one(
                        'shown.bs.modal',
                        function () {

                            $(crud.form)
                                .find('select[multiple]')
                                .each(
                                    function () {

                                        const $select =
                                            $(this);

                                        if (
                                            $select.hasClass(
                                                'select2-hidden-accessible'
                                            )
                                        ) {

                                            $select
                                                .trigger('change');
                                        }
                                    }
                                );
                        }
                    );
            }
        );
    },


    populate(form, data) {

        const $form = $(form);

        Object.keys(data).forEach(
            function (key) {

                let value = data[key];

                /*
                 * Normalize organization_ids.
                 *
                 * Supports:
                 *
                 * [1, 2]
                 *
                 * ["1", "2"]
                 *
                 * "1,2"
                 *
                 * 1
                 */
                if (key === 'organization_ids') {

                    if (typeof value === 'string') {

                        value = value
                            .split(',')
                            .map(
                                item => item.trim()
                            )
                            .filter(Boolean);

                    } else if (!Array.isArray(value)) {

                        value = value !== null &&
                            value !== undefined &&
                            value !== ''
                            ? [value]
                            : [];
                    }

                    /*
                     * Select values are strings.
                     */
                    value = value.map(
                        item => String(item)
                    );
                }


                /*
                 * Handle multi-select fields.
                 *
                 * Example:
                 *
                 * name="organization_ids[]"
                 */
                const $multiField =
                    $form.find(
                        `[name="${key}[]"]`
                    );


                if (
                    $multiField.length &&
                    $multiField.is('select[multiple]')
                ) {

                    $multiField
                        .val(
                            Array.isArray(value)
                                ? value
                                : [String(value)]
                        )
                        .trigger('change');

                    return;
                }


                /*
                 * Also support a multiple select where
                 * the backend key itself already contains [].
                 *
                 * Example:
                 *
                 * organization_ids[]
                 */
                const $arrayField =
                    $form.find(
                        `[name="${key}"]`
                    );


                if (
                    $arrayField.length &&
                    $arrayField.is('select[multiple]')
                ) {

                    $arrayField
                        .val(
                            Array.isArray(value)
                                ? value
                                : []
                        )
                        .trigger('change');

                    return;
                }


                /*
                 * Normal fields.
                 */
                const $normalField =
                    $form.find(
                        `[name="${key}"]`
                    );


                if ($normalField.length) {

                    $normalField
                        .val(
                            value ?? ''
                        )
                        .trigger('change');
                }
            }
        );
    },


    clearErrors() {

        $('.is-invalid')
            .removeClass('is-invalid');

        $('.invalid-feedback')
            .html('');
    },


    showErrors(errors) {

        Object.keys(errors).forEach(
            function (field) {

                const cleanField =
                    field.replace(
                        /\[\]$/,
                        ''
                    );


                const $field =
                    $(
                        `[name="${field}"],` +
                        `[name="${cleanField}"],` +
                        `[name="${cleanField}[]"]`
                    );


                if (!$field.length) {
                    return;
                }


                $field.addClass(
                    'is-invalid'
                );


                if (
                    $field.hasClass(
                        'select2-hidden-accessible'
                    )
                ) {

                    $field
                        .next('.select2')
                        .find(
                            '.select2-selection'
                        )
                        .addClass(
                            'is-invalid'
                        );
                }


                let message =
                    errors[field];


                if (
                    Array.isArray(message)
                ) {

                    message =
                        message[0];
                }


                $field
                    .closest(
                        '.col-md-12, .col-md-6, .col-md-8, .col-md-4, .col-12'
                    )
                    .find(
                        '.invalid-feedback'
                    )
                    .first()
                    .html(
                        message
                    );
            }
        );
    },



    reset(crud) {

        if (!crud.form) {
            return;
        }

        crud.form.reset();

        crud.editId = null;

        CrudForm.clearErrors();

        $(crud.form)
            .find('select[multiple]')
            .val([])
            .trigger('change');

        $('#btnSave')
            .prop('disabled', false)
            .html('Save');
    },
};