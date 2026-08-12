const EmployeeForm = {
    config: window.EmployeeFormConfig || {},
    endpoint: `${APP.baseUrl}employees`,
    employee: null,

    init() {
        this.bindEvents();
        this.initializeSelect2();
        this.initializeDates();
        this.initializePhoto();
        this.setFormMode();

        this.updateSubmitButtonState();

        if (this.config.mode === 'edit') {
            this.loadEmployee();
        }
    },

    setFormMode() {
        const $button = $('#employeeSaveBtn');

        if (!$button.length) {
            return;
        }

        if (this.config.mode === 'edit') {
            $button.html(`
            <i class="bi bi-check2 me-1"></i>
            Update Employee
        `);
        } else {
            $button.html(`
            <i class="bi bi-check2 me-1"></i>
            Create Employee
        `);
        }
    },

    // bindEvents() {
    //     $('#employeeForm')
    //         .off('submit.employeeForm')
    //         .on(
    //             'submit.employeeForm',
    //             (e) => {

    //                 e.preventDefault();

    //                 this.submit();
    //             }
    //         );

    //     $('#organization_id').off('change.employeeForm').on('change.employeeForm', () => {
    //         const organizationId = $('#organization_id').val();
    //         this.loadBranches(organizationId);
    //         this.loadDepartments(organizationId);
    //         this.loadDesignations(organizationId);
    //     }
    //     );


    //     $('#profile_photo')
    //         .off('change.employeeForm')
    //         .on(
    //             'change.employeeForm',
    //             (e) => {

    //                 this.previewPhoto(
    //                     e.target.files[0]
    //                 );
    //             }
    //         );
    // },


    bindEvents() {
        $('#employeeForm')
            .off('submit.employeeForm')
            .on('submit.employeeForm', (e) => {
                e.preventDefault();
                this.submit();
            });

        $('#employeeForm')
            .off('input.employeeForm change.employeeForm')
            .on(
                'input.employeeForm change.employeeForm',
                'input, select, textarea',
                () => {
                    this.updateSubmitButtonState();
                }
            );

        $('#organization_id')
            .off('change.employeeForm')
            .on('change.employeeForm', () => {
                const organizationId = $('#organization_id').val();

                this.loadBranches(organizationId);
                this.loadDepartments(organizationId);
                this.loadDesignations(organizationId);

                this.updateSubmitButtonState();
            });

        $('#profile_photo')
            .off('change.employeeForm')
            .on('change.employeeForm', (e) => {
                this.previewPhoto(e.target.files[0]);
                this.updateSubmitButtonState();
            });
    },


    initializeSelect2() {
        $('.employee-select2').each(function () {
            const $select = $(this);
            $select.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: $select.find('option:first').text(),
                allowClear: !$select.prop('multiple'),
                dropdownAutoWidth: true
            });
        });

        this.loadOrganizations();
        this.loadManagers();
        if (this.config.mode === 'create') {
            this.loadRoles();
        }
    },

    initializeDates() {
        flatpickr('#dob', { dateFormat: 'Y-m-d', maxDate: 'today', allowInput: true });
        flatpickr('#joining_date', { dateFormat: 'Y-m-d', allowInput: true });
    },

    initializePhoto() {
        const existingPhoto =
            this.config.employee?.profile_photo;
        if (existingPhoto) {
            this.setPhotoPreview(
                this.resolvePhoto(
                    existingPhoto
                )
            );
        }
    },

    loadEmployee() {
        const id = parseInt(this.config.employeeId, 10);

        if (!id) {
            return;
        }

        this.setPageLoading(true);

        $.ajax({
            url: `${this.endpoint}/data/${id}`,
            type: 'GET',
            success: (response) => {
                if (!response.success) {
                    APP.error(
                        response.message ||
                        'Unable to load employee.'
                    );
                    return;
                }

                this.employee = response.data || {};
                this.populateForm(this.employee);
            },

            error: () => {
                APP.error(
                    'Unable to load employee.'
                );
            },

            complete: () => {
                this.setPageLoading(false);
            }
        });
    },

    populateForm(employee) {
        $('[name="first_name"]').val(employee.first_name || '');

        $('[name="last_name"]').val(employee.last_name || '');

        $('[name="email"]').val(employee.email || '');

        $('[name="phone"]').val(employee.phone || '');

        $('[name="gender"]').val(employee.gender || '').trigger('change');

        $('[name="employee_code"]').val(employee.employee_code || '');

        $('[name="status"]').val(employee.status || 'active').trigger('change');

        $('[name="dob"]').val(employee.dob || '');

        $('[name="joining_date"]').val(employee.joining_date || '');

        this.setSelectValue(
            '#organization_id',
            employee.organization_id
        );

        this.loadBranches(
            employee.organization_id,
            employee.branch_id
        );

        this.loadDepartments(
            employee.organization_id,
            employee.department_id
        );

        this.loadDesignations(
            employee.organization_id,
            employee.designation_id
        );
        this.loadRoles(
            employee.role_id || null
        );
        this.setSelectValue('#reporting_manager_id', employee.reporting_manager_id);

        if (employee.profile_photo) {
            this.setPhotoPreview(this.resolvePhoto(employee.profile_photo));
        }
        this.updateSubmitButtonState();
    },

    loadOrganizations() {

        this.loadOptions(
            'organizations/options',
            '#organization_id',
            'Select organization'
        );
    },


    loadBranches(organizationId, selectedId = null) {
        const $branch = $('#branch_id');
        $branch.prop('disabled', true).empty().append('<option value="">Select branch</option>').trigger('change');

        if (!organizationId) {
            return;
        }

        this.loadOptions(
            `branches/options?organization_id=${encodeURIComponent(
                organizationId
            )}`,
            '#branch_id',
            'Select branch',
            selectedId
        );
    },


    loadDepartments(
        organizationId = null,
        selectedId = null
    ) {
        const $department = $('#department_id');

        $department
            .prop('disabled', true)
            .empty()
            .append(
                $('<option>', {
                    value: '',
                    text: 'Select department'
                })
            )
            .trigger('change');

        if (!organizationId) {
            return;
        }

        this.loadOptions(
            `departments/options?organization_id=${encodeURIComponent(
                organizationId
            )}`,
            '#department_id',
            'Select department',
            selectedId
        );
    },


    loadDesignations(
        organizationId = null,
        selectedId = null
    ) {
        const $designation = $('#designation_id');

        $designation
            .prop('disabled', true)
            .empty()
            .append(
                $('<option>', {
                    value: '',
                    text: 'Select designation'
                })
            )
            .trigger('change');

        if (!organizationId) {
            return;
        }

        this.loadOptions(
            `designations/options?organization_id=${encodeURIComponent(
                organizationId
            )}`,
            '#designation_id',
            'Select designation',
            selectedId
        );
    },


    loadManagers() {
        this.loadOptions(
            'employees/options',
            '#reporting_manager_id',
            'No reporting manager',
            this.config.employeeId || null
        );
    },

    loadRoles(selectedValue = null) {

        const $select = $('#role_id');

        if (!$select.length) {
            return;
        }

        $.ajax({

            url: `${APP.baseUrl}roles/options`,
            type: 'GET',

            success: (response) => {

                /*
                |--------------------------------------------------------------------------
                | Validate response
                |--------------------------------------------------------------------------
                */

                if (
                    !response ||
                    response.success !== true
                ) {
                    console.warn(
                        'Invalid roles/options response',
                        response
                    );

                    return;
                }

                const options =
                    response.data &&
                        Array.isArray(response.data.roles)
                        ? response.data.roles
                        : [];

                /*
                |--------------------------------------------------------------------------
                | Reset select
                |--------------------------------------------------------------------------
                */

                $select
                    .empty()
                    .append(
                        $('<option>', {
                            value: '',
                            text: 'Select role'
                        })
                    );

                /*
                |--------------------------------------------------------------------------
                | Populate roles
                |--------------------------------------------------------------------------
                */

                options.forEach((role) => {

                    if (
                        role.id === undefined ||
                        role.display_name === undefined
                    ) {
                        return;
                    }

                    $select.append(
                        $('<option>', {
                            value: String(role.id),
                            text: role.display_name
                        })
                    );
                });

                /*
                |--------------------------------------------------------------------------
                | Restore selected role
                |--------------------------------------------------------------------------
                */

                if (
                    selectedValue !== null &&
                    selectedValue !== undefined &&
                    selectedValue !== ''
                ) {

                    $select
                        .val(String(selectedValue));
                }

                /*
                |--------------------------------------------------------------------------
                | Refresh Select2
                |--------------------------------------------------------------------------
                */

                $select.trigger('change');
            },

            error: (xhr) => {

                console.warn(
                    'Unable to load roles/options',
                    xhr
                );
            }
        });
    },


    loadOptions(
        path,
        selector,
        placeholder,
        selectedValue = null
    ) {

        $.ajax({
            url: `${APP.baseUrl}${path}`,
            type: 'GET',
            success: (response) => {
                const $select = $(selector);
                if (!$select.length) {
                    return;
                }

                let options = Array.isArray(response) ? response : (response.data || []);
                if (!Array.isArray(options)) {
                    options = [];
                }

                if (!$select.prop('multiple')) {
                    $select.empty().append($('<option>', { value: '', text: placeholder }));
                } else {
                    $select.empty();
                }

                options.forEach(
                    (option) => {

                        const id = option.id ?? option.value;
                        const text = option.name ?? option.text ?? option.display_name ?? option.full_name ?? '';
                        $select.append($('<option>', { value: id, text: text }));
                    }
                );

                if (selectedValue !== null) {
                    this.setSelectValue(selector, selectedValue);
                }

                if (selector === '#branch_id' || selector === '#department_id' || selector === '#designation_id') {
                    $select.prop('disabled', false);
                }

                $select.trigger('change');
            },


            error: (xhr) => {

                console.warn(
                    `Unable to load ${path}`,
                    xhr
                );
            }
        });
    },

    updateSubmitButtonState() {
        const $button = $('#employeeSaveBtn');
        const form = document.getElementById('employeeForm');

        if (!$button.length || !form) {
            return;
        }

        if ($button.data('saving') === true) {
            return;
        }

        let isValid = form.checkValidity();

        const roleId = $('#role_id').val();

        if (!roleId) {
            isValid = false;
        }

        const password = $('#password').val();
        const confirmation = $('#password_confirmation').val();

        if (this.config.mode === 'create') {
            if (!password || password !== confirmation) {
                isValid = false;
            }
        } else {
            if (
                password &&
                password !== confirmation
            ) {
                isValid = false;
            }
        }

        $button.prop('disabled', !isValid);
    },

    submit() {
        this.clearErrors();

        const form = document.getElementById('employeeForm');

        if (!form.checkValidity()) {
            form.classList.add('was-validated');

            this.updateSubmitButtonState();

            return;
        }

        const password =
            $('#password').val();


        const confirmation =
            $('#password_confirmation').val();


        if (this.config.mode === 'create' && password !== confirmation) {
            this.setFieldError(
                'password_confirmation',
                'Passwords do not match.'
            );
            this.updateSubmitButtonState();
            return;
        }


        if (
            this.config.mode === 'edit' &&
            password &&
            password !== confirmation
        ) {
            this.setFieldError(
                'password_confirmation',
                'Passwords do not match.'
            );

            this.updateSubmitButtonState();

            return;
        }

        const roleId = $('#role_id').val();

        if (!roleId) {
            this.setFieldError('role_id', 'Please select a role.');
            this.updateSubmitButtonState();
            return;
        }

        const formData = new FormData(form);

        if (!formData.has(APP.csrfName)) {
            formData.append(
                APP.csrfName,
                APP.csrfHash
            );
        }


        this.setSaving(true);
        const id = this.config.employeeId;
        const url = this.config.mode === 'edit' ? `${this.endpoint}/update/${id}` : `${this.endpoint}/store`;

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success: (response) => {
                if (!response.success) {
                    this.handleErrors(response);
                    return;
                }


                APP.success(
                    response.message ||
                    'Employee saved successfully.'
                );


                setTimeout(
                    () => {

                        window.location.href =
                            this.endpoint;

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
                    'Unable to save employee.'
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

                    let message =
                        response.errors[field];


                    if (Array.isArray(message)) {
                        message = message[0];
                    }


                    this.setFieldError(
                        field,
                        message
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

        const cleanField =
            String(field)
                .replace(/\[\]$/, '');


        const $target = $(
            `[name="${cleanField}"],` +
            `[name="${cleanField}[]"]`
        );


        if (!$target.length) {
            return;
        }


        $target.addClass(
            'is-invalid'
        );


        const $error =
            $(
                `[data-field-error="${cleanField}"]`
            );


        if ($error.length) {

            $error
                .text(message)
                .addClass('d-block');
        }

        if (
            $target.hasClass(
                'employee-select2'
            )
        ) {

            $target
                .next('.select2-container')
                .find('.select2-selection')
                .addClass('is-invalid');
        }
    },


    clearErrors() {

        $('.is-invalid')
            .removeClass('is-invalid');


        $('[data-field-error]')
            .text('')
            .removeClass('d-block');


        $('#employeeForm')
            .removeClass(
                'was-validated'
            );
    },

    setSaving(isSaving) {
        const $button = $('#employeeSaveBtn');

        if (!$button.length) {
            return;
        }

        if (isSaving) {
            $button
                .data('saving', true)
                .prop('disabled', true)
                .html(`
                <span
                    class="spinner-border spinner-border-sm me-2"
                    role="status"
                ></span>
                Saving...
            `);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Request finished
        |--------------------------------------------------------------------------
        |
        | Don't blindly enable the button.
        | Recalculate the actual form state.
        |
        */

        $button.data('saving', false);

        $button.html(
            this.config.mode === 'edit'
                ? `
                <i class="bi bi-check2 me-1"></i>
                Update Employee
              `
                : `
                <i class="bi bi-check2 me-1"></i>
                Create Employee
              `
        );

        this.updateSubmitButtonState();
    },


    setPageLoading(isLoading) {

        const $form = $('#employeeForm');
        if (isLoading) {
            $form.addClass('employee-form-loading');
        } else {
            $form.removeClass('employee-form-loading');
        }
    },

    previewPhoto(file) {

        if (!file) {
            return;
        }


        if (

            ![
                'image/jpeg',
                'image/png',
                'image/webp'
            ].includes(file.type)

        ) {

            APP.error(
                'Please select a JPG, PNG or WEBP image.'
            );


            $('#profile_photo')
                .val('');

            return;
        }


        if (
            file.size > 2 * 1024 * 1024
        ) {

            APP.error(
                'Profile photo must not exceed 2MB.'
            );


            $('#profile_photo')
                .val('');

            return;
        }


        const reader =
            new FileReader();


        reader.onload = (event) => {

            this.setPhotoPreview(
                event.target.result
            );
        };


        reader.readAsDataURL(file);
    },


    setPhotoPreview(src) {

        $('#employeePhotoPreview')
            .html(`
                <img
                    src="${this.escapeAttribute(src)}"
                    alt="Profile photo"
                >
            `);
    },


    setSelectValue(selector, value) {

        if (
            value === null ||
            value === undefined
        ) {
            return;
        }


        $(selector)
            .val(String(value))
            .trigger('change');
    },

    resolvePhoto(path) {
        if (!path) {
            return '';
        }


        if (
            path.startsWith('http://') ||
            path.startsWith('https://') ||
            path.startsWith('/')
        ) {

            return path;
        }
        return `${APP.baseUrl}${path}`;
    },

    escapeAttribute(value) {

        return String(value ?? '')
            .replace(
                /&/g,
                '&amp;'
            )
            .replace(
                /"/g,
                '&quot;'
            )
            .replace(
                /</g,
                '&lt;'
            )
            .replace(
                />/g,
                '&gt;'
            );
    }
};

$(function () {
    EmployeeForm.init();
});