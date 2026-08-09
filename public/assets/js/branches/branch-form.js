const BranchForm = {
    config:
        window.BranchFormConfig || {},

    init() {
        this.bindEvents();
    },

    bindEvents() {
        $('#branchForm').on(
            'submit',
            (e) => {
                e.preventDefault();

                this.submit();
            }
        );
    },

    submit() {
        this.clearErrors();

        const form =
            document.getElementById(
                'branchForm'
            );

        if (!form.checkValidity()) {
            form.classList.add(
                'was-validated'
            );

            return;
        }

        const formData =
            new FormData(form);

        /*
        |--------------------------------------------------------------------------
        | Explicit CSRF
        |--------------------------------------------------------------------------
        */

        if (
            !formData.has(APP.csrfName)
        ) {
            formData.append(
                APP.csrfName,
                APP.csrfHash
            );
        }

        const isEdit =
            this.config.mode === 'edit';

        const url =
            isEdit
                ? `${this.config.endpoint} /update/${this.config.branchId}`
                : `${this.config.endpoint} /store`;

        this.setSaving(true);

        $.ajax({
            url: url,

            type: 'POST',

            data: formData,

            processData: false,

            contentType: false,

            success: (response) => {
                if (!response.success) {
                    this.handleErrors(
                        response
                    );

                    return;
                }

                APP.success(
                    response.message ||
                    'Branch saved successfully.'
                );

                setTimeout(
                    () => {
                        window.location.href =
                            this.config.endpoint;
                    },
                    500
                );
            },

            error: (xhr) => {
                if (
                    xhr.status === 422 &&
                    xhr.responseJSON
                ) {
                    this.handleErrors(
                        xhr.responseJSON
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
                    xhr.responseJSON?.message ||
                    'Unable to save branch.'
                );
            },

            complete: () => {
                this.setSaving(false);
            }
        });
    },

    handleErrors(response) {
        this.clearErrors();

        if (
            response.errors &&
            typeof response.errors === 'object'
        ) {
            Object.keys(
                response.errors
            ).forEach(
                (field) => {
                    this.setFieldError(
                        field,
                        response.errors[field]
                    );
                }
            );
        }

        APP.error(
            response.message ||
            'Please correct the highlighted fields.'
        );
    },

    setFieldError(
        field,
        message
    ) {
        const $field =
            $(`[name="${field} "]`);

        if (!$field.length) {
            return;
        }

        $field.addClass(
            'is-invalid'
        );

        const $error =
            $(
                `[data-field-error="${field} "]`
            );

        if ($error.length) {
            $error
                .text(message)
                .addClass('d-block');
        }
    },

    clearErrors() {
        $('.is-invalid')
            .removeClass('is-invalid');

        $('[data-field-error]')
            .text('')
            .removeClass('d-block');

        $('#branchForm')
            .removeClass('was-validated');
    },

    setSaving(isSaving) {
        const $button =
            $('#branchSaveBtn');

        if (isSaving) {
            $button
                .prop('disabled', true)
                .html(`
                    <span
                        class="spinner-border spinner-border-sm me-2"
                    ></span>
                    Saving...
                `);
        } else {
            $button
                .prop('disabled', false)
                .html(
                    this.config.mode === 'edit'
                        ? '<i class="bi bi-check2 me-1"></i>Save Changes'
                        : '<i class="bi bi-check2 me-1"></i>Create Branch'
                );
        }
    }
};

$(function () {
    BranchForm.init();
});