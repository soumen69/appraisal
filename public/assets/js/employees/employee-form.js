const EmployeeForm = {

    config: window.EmployeeFormConfig || {},

    endpoint: `${APP.baseUrl}employees`,

    employee: null,

    isSubmitting: false,

    init() {

        this.bindEvents();

        this.initializeSelect2();

        this.initializeDates();

        this.initializePhoto();

        this.setFormMode();

        /*
         * Edit loads employee first because several
         * dropdowns depend on employee values.
         */
        if (this.config.mode === 'edit') {

            this.setPageLoading(true);

            this.loadEmployee();

        } else {

            /*
             * Create has no employee data, so load
             * independent dropdowns immediately.
             */
            this.loadOrganizations();

            this.loadManagers();

            this.loadRoles();

            this.updateSubmitButtonState();

        }
    },


    /*
     * -------------------------------------------------------------
     * Form Mode
     * -------------------------------------------------------------
     */

    setFormMode() {

        const $button = $('#employeeSaveBtn');

        if (!$button.length) {
            return;
        }

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
    },


    /*
     * -------------------------------------------------------------
     * Events
     * -------------------------------------------------------------
     */

    bindEvents() {

        /*
         * Submit
         */
        $('#employeeForm')
            .off('submit.employeeForm')
            .on(
                'submit.employeeForm',
                (e) => {

                    e.preventDefault();

                    this.submit();
                }
            );


        /*
         * User interaction.
         *
         * Once the user starts interacting with the form,
         * Bootstrap validation state becomes visible.
         */
        $('#employeeForm')
            .off(
                'input.employeeForm change.employeeForm',
                'input, select, textarea'
            )
            .on(
                'input.employeeForm change.employeeForm',
                'input, select, textarea',
                () => {

                    this.syncPasswordValidation();

                    this.updateSubmitButtonState();

                }
            );


        /*
         * Blur validation.
         */
        $('#employeeForm')
            .off(
                'blur.employeeForm',
                'input, select, textarea'
            )
            .on(
                'blur.employeeForm',
                'input, select, textarea',
                () => {

                    $('#employeeForm')
                        .addClass('was-validated');

                    this.syncPasswordValidation();

                    this.updateSubmitButtonState();

                }
            );


        /*
         * Organization dependency chain.
         */
        $('#organization_id')
            .off('change.employeeForm')
            .on(
                'change.employeeForm',
                () => {

                    const organizationId =
                        $('#organization_id').val();

                    this.loadBranches(
                        organizationId
                    );

                    this.loadDepartments(
                        organizationId
                    );

                    this.loadDesignations(
                        organizationId
                    );

                    this.updateSubmitButtonState();
                }
            );


        /*
         * Profile photo.
         */
        $('#profile_photo')
            .off('change.employeeForm')
            .on(
                'change.employeeForm',
                (e) => {

                    this.previewPhoto(
                        e.target.files[0]
                    );

                    this.updateSubmitButtonState();
                }
            );
    },


    /*
     * -------------------------------------------------------------
     * Select2
     * -------------------------------------------------------------
     */

    initializeSelect2() {

        $('.employee-select2').each(
            function () {

                const $select = $(this);

                $select.select2({

                    theme: 'bootstrap-5',

                    width: '100%',

                    placeholder:
                        $select
                            .find('option:first')
                            .text(),

                    allowClear:
                        !$select.prop('multiple'),

                    dropdownAutoWidth: true

                });

            }
        );
    },


    /*
     * -------------------------------------------------------------
     * Dates
     * -------------------------------------------------------------
     */

    initializeDates() {

        flatpickr(
            '#dob',
            {
                dateFormat: 'Y-m-d',
                maxDate: 'today',
                allowInput: true
            }
        );


        flatpickr(
            '#joining_date',
            {
                dateFormat: 'Y-m-d',
                allowInput: true
            }
        );
    },


    /*
     * -------------------------------------------------------------
     * Existing Photo
     * -------------------------------------------------------------
     */

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


    /*
     * -------------------------------------------------------------
     * Load Employee
     * -------------------------------------------------------------
     */

    loadEmployee() {

        const id =
            parseInt(
                this.config.employeeId,
                10
            );

        if (!id) {

            this.setPageLoading(false);

            APP.error(
                'Invalid employee ID.'
            );

            return;
        }


        $.ajax({

            url:
                `${this.endpoint}/data/${id}`,

            type: 'GET',

            dataType: 'json',

            success: (response) => {

                if (
                    !response ||
                    response.success !== true
                ) {

                    APP.error(
                        response?.message ||
                        'Unable to load employee.'
                    );

                    return;
                }


                this.employee =
                    response.data || {};

                this.populateForm(
                    this.employee
                );

            },

            error: (xhr) => {

                console.error(
                    'Employee load failed:',
                    xhr
                );

                if (xhr.status === 404) {

                    APP.error(
                        'Employee not found.'
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
                    'Unable to load employee.'
                );
            },

            complete: () => {

                this.setPageLoading(false);

                this.updateSubmitButtonState();
            }

        });
    },


    /*
     * -------------------------------------------------------------
     * Populate Edit Form
     * -------------------------------------------------------------
     */

    populateForm(employee) {

        $('[name="first_name"]')
            .val(employee.first_name || '');

        $('[name="last_name"]')
            .val(employee.last_name || '');

        $('[name="email"]')
            .val(employee.email || '');

        $('[name="phone"]')
            .val(employee.phone || '');

        $('[name="gender"]')
            .val(employee.gender || '')
            .trigger('change');

        $('[name="employee_code"]')
            .val(employee.employee_code || '');

        $('[name="status"]')
            .val(employee.status || 'active')
            .trigger('change');

        $('[name="dob"]')
            .val(employee.dob || '');

        $('[name="joining_date"]')
            .val(employee.joining_date || '');


        /*
         * Organization must load first.
         *
         * Department / designation / branch depend on it.
         */
        this.loadOrganizations(
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


        /*
         * Resolve employee role.
         *
         * Supports both:
         *
         * role_id
         * role_ids
         */
        let roleId =
            employee.role_id ?? null;

        if (
            !roleId &&
            Array.isArray(employee.role_ids) &&
            employee.role_ids.length
        ) {

            roleId =
                employee.role_ids[0];
        }


        this.loadRoles(
            roleId
        );


        /*
         * Reporting manager.
         */
        this.loadManagers(
            employee.reporting_manager_id,
            this.config.employeeId
        );


        /*
         * Profile photo.
         */
        if (employee.profile_photo) {

            this.setPhotoPreview(
                this.resolvePhoto(
                    employee.profile_photo
                )
            );

        }


        this.updateSubmitButtonState();
    },


    /*
     * -------------------------------------------------------------
     * Organization
     * -------------------------------------------------------------
     */

    loadOrganizations(
        selectedId = null
    ) {

        this.loadOptions(
            'organizations/options',
            '#organization_id',
            'Select organization',
            selectedId
        );
    },


    /*
     * -------------------------------------------------------------
     * Branches
     * -------------------------------------------------------------
     */

    loadBranches(
        organizationId,
        selectedId = null
    ) {

        const $branch =
            $('#branch_id');


        $branch
            .prop('disabled', true)
            .empty()
            .append(
                $('<option>', {
                    value: '',
                    text: 'Select branch'
                })
            )
            .trigger('change');


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


    /*
     * -------------------------------------------------------------
     * Departments
     * -------------------------------------------------------------
     */

    loadDepartments(
        organizationId = null,
        selectedId = null
    ) {

        const $department =
            $('#department_id');


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


    /*
     * -------------------------------------------------------------
     * Designations
     * -------------------------------------------------------------
     */

    loadDesignations(
        organizationId = null,
        selectedId = null
    ) {

        const $designation =
            $('#designation_id');


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


    /*
     * -------------------------------------------------------------
     * Reporting Managers
     * -------------------------------------------------------------
     *
     * selectedId:
     *     Current employee's manager
     *
     * excludeId:
     *     Current employee itself
     *
     * This prevents an employee from accidentally
     * being displayed as their own manager.
     */

    loadManagers(
        selectedId = null,
        excludeId = null
    ) {

        const $manager =
            $('#reporting_manager_id');


        $.ajax({

            url:
                `${APP.baseUrl}employees/options`,

            type: 'GET',

            dataType: 'json',

            success: (response) => {

                if (!$manager.length) {
                    return;
                }


                let options =
                    Array.isArray(response)
                        ? response
                        : response?.data || [];


                if (!Array.isArray(options)) {
                    options = [];
                }


                $manager
                    .empty()
                    .append(
                        $('<option>', {
                            value: '',
                            text: 'No reporting manager'
                        })
                    );


                options.forEach(
                    (option) => {

                        const id =
                            option.id ??
                            option.value;

                        /*
                         * Do not allow the employee
                         * to select themselves.
                         */
                        if (
                            excludeId !== null &&
                            String(id) ===
                            String(excludeId)
                        ) {
                            return;
                        }


                        const text =
                            option.name ??
                            option.text ??
                            option.display_name ??
                            option.full_name ??
                            '';


                        $manager.append(
                            $('<option>', {
                                value: id,
                                text: text
                            })
                        );
                    }
                );


                if (
                    selectedId !== null &&
                    selectedId !== undefined &&
                    selectedId !== ''
                ) {

                    $manager.val(
                        String(selectedId)
                    );
                }


                $manager.trigger('change');

            },

            error: (xhr) => {

                console.warn(
                    'Unable to load employees/options',
                    xhr
                );
            }

        });
    },


    /*
     * -------------------------------------------------------------
     * Roles
     * -------------------------------------------------------------
     */

    loadRoles(
        selectedValue = null
    ) {

        const $select =
            $('#role_id');


        if (!$select.length) {
            return;
        }


        $.ajax({

            url:
                `${APP.baseUrl}roles/options`,

            type: 'GET',

            dataType: 'json',

            success: (response) => {

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
                        Array.isArray(
                            response.data.roles
                        )
                        ? response.data.roles
                        : [];


                $select
                    .empty()
                    .append(
                        $('<option>', {
                            value: '',
                            text: 'Select role'
                        })
                    );


                options.forEach(
                    (role) => {

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
                    }
                );


                /*
                 * Restore edit value only after
                 * all options have been loaded.
                 */
                if (
                    selectedValue !== null &&
                    selectedValue !== undefined &&
                    selectedValue !== ''
                ) {

                    $select.val(
                        String(selectedValue)
                    );
                }


                $select.trigger('change');


                this.updateSubmitButtonState();

            },

            error: (xhr) => {

                console.error(
                    'Unable to load roles/options',
                    xhr
                );

                APP.error(
                    'Unable to load roles.'
                );
            }

        });
    },


    /*
     * -------------------------------------------------------------
     * Generic Options Loader
     * -------------------------------------------------------------
     */

    loadOptions(
        path,
        selector,
        placeholder,
        selectedValue = null
    ) {

        $.ajax({

            url:
                `${APP.baseUrl}${path}`,

            type: 'GET',

            dataType: 'json',

            success: (response) => {

                const $select =
                    $(selector);


                if (!$select.length) {
                    return;
                }


                let options =
                    Array.isArray(response)
                        ? response
                        : response?.data || [];


                if (!Array.isArray(options)) {
                    options = [];
                }


                $select
                    .empty()
                    .append(
                        $('<option>', {
                            value: '',
                            text: placeholder
                        })
                    );


                options.forEach(
                    (option) => {

                        const id =
                            option.id ??
                            option.value;

                        const text =
                            option.name ??
                            option.text ??
                            option.display_name ??
                            option.full_name ??
                            '';


                        $select.append(
                            $('<option>', {
                                value: id,
                                text: text
                            })
                        );
                    }
                );


                /*
                 * Selected value is applied only
                 * AFTER options exist.
                 */
                if (
                    selectedValue !== null &&
                    selectedValue !== undefined &&
                    selectedValue !== ''
                ) {

                    $select.val(
                        String(selectedValue)
                    );
                }


                /*
                 * Dependency selects become
                 * available only after successful load.
                 */
                if (
                    selector === '#branch_id' ||
                    selector === '#department_id' ||
                    selector === '#designation_id'
                ) {

                    $select.prop(
                        'disabled',
                        false
                    );
                }


                $select.trigger('change');


                this.updateSubmitButtonState();
            },

            error: (xhr) => {

                console.warn(
                    `Unable to load ${path}`,
                    xhr
                );

                this.updateSubmitButtonState();
            }

        });
    },


    /*
     * -------------------------------------------------------------
     * Password Validation
     * -------------------------------------------------------------
     */

    syncPasswordValidation() {

        /*
         * Edit form has no password fields.
         */
        if (
            this.config.mode !== 'create'
        ) {
            return;
        }


        const $password =
            $('#password');

        const $confirmation =
            $('#password_confirmation');


        if (
            !$password.length ||
            !$confirmation.length
        ) {
            return;
        }


        const password =
            $password.val() || '';

        const confirmation =
            $confirmation.val() || '';


        if (
            confirmation &&
            password !== confirmation
        ) {

            $confirmation[0]
                .setCustomValidity(
                    'Passwords do not match.'
                );

        } else {

            $confirmation[0]
                .setCustomValidity('');
        }


        const $error =
            $('[data-field-error="password_confirmation"]');


        if (
            confirmation &&
            password !== confirmation
        ) {

            $error
                .text('Passwords do not match.')
                .addClass('d-block');

        } else {

            $error
                .text('')
                .removeClass('d-block');
        }
    },


    /*
     * -------------------------------------------------------------
     * Submit Button State
     * -------------------------------------------------------------
     */

    updateSubmitButtonState() {

        const $button =
            $('#employeeSaveBtn');

        const form =
            document.getElementById(
                'employeeForm'
            );


        if (
            !$button.length ||
            !form
        ) {
            return;
        }


        if (this.isSubmitting) {
            return;
        }


        this.syncPasswordValidation();


        let isValid =
            form.checkValidity();


        /*
         * Explicit role check.
         */
        const roleId =
            $('#role_id').val();


        if (!roleId) {
            isValid = false;
        }


        /*
         * Create password requirements.
         *
         * Edit does not have password fields.
         */
        if (
            this.config.mode === 'create'
        ) {

            const password =
                $('#password').val() || '';

            const confirmation =
                $('#password_confirmation').val() || '';


            if (
                !password ||
                !confirmation ||
                password !== confirmation
            ) {

                isValid = false;
            }
        }


        $button.prop(
            'disabled',
            !isValid
        );
    },


    /*
     * -------------------------------------------------------------
     * Submit
     * -------------------------------------------------------------
     */

    submit() {

        this.clearErrors();


        const form =
            document.getElementById(
                'employeeForm'
            );


        if (!form) {
            return;
        }


        this.syncPasswordValidation();


        /*
         * Show all validation feedback
         * when submission is attempted.
         */
        form.classList.add(
            'was-validated'
        );


        if (!form.checkValidity()) {

            this.updateSubmitButtonState();

            return;
        }


        const roleId =
            $('#role_id').val();


        if (!roleId) {

            this.setFieldError(
                'role_id',
                'Please select a role.'
            );

            this.updateSubmitButtonState();

            return;
        }


        const formData =
            new FormData(form);


        /*
         * CSRF fallback.
         */
        if (
            !formData.has(
                APP.csrfName
            )
        ) {

            formData.append(
                APP.csrfName,
                APP.csrfHash
            );
        }


        const id =
            this.config.employeeId;


        const url =
            this.config.mode === 'edit'
                ? `${this.endpoint}/update/${id}`
                : `${this.endpoint}/store`;


        this.isSubmitting = true;

        this.setSaving(true);


        $.ajax({

            url: url,

            type: 'POST',

            data: formData,

            processData: false,

            contentType: false,

            dataType: 'json',

            success: (response) => {

                if (
                    !response ||
                    response.success !== true
                ) {

                    this.handleErrors(
                        response || {}
                    );

                    return;
                }


                APP.success(
                    response.message ||
                    (
                        this.config.mode === 'edit'
                            ? 'Employee updated successfully.'
                            : 'Employee created successfully.'
                    )
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


                if (xhr.status === 404) {

                    APP.error(
                        'Employee not found.'
                    );

                    return;
                }


                console.error(
                    'Employee save failed:',
                    xhr
                );


                APP.error(
                    'Unable to save employee.'
                );
            },


            complete: () => {

                this.isSubmitting = false;

                this.setSaving(false);
            }

        });
    },


    /*
     * -------------------------------------------------------------
     * Server Validation Errors
     * -------------------------------------------------------------
     */

    handleErrors(response) {

        this.clearErrors();


        $('#employeeForm')
            .addClass(
                'was-validated'
            );


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


                    if (
                        Array.isArray(message)
                    ) {

                        message =
                            message[0];
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


        this.updateSubmitButtonState();
    },


    /*
     * -------------------------------------------------------------
     * Field Error
     * -------------------------------------------------------------
     */

    setFieldError(
        field,
        message
    ) {

        const cleanField =
            String(field)
                .replace(
                    /\[\]$/,
                    ''
                );


        const $target =
            $(
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
                .addClass(
                    'is-invalid'
                );
        }
    },


    /*
     * -------------------------------------------------------------
     * Clear Errors
     * -------------------------------------------------------------
     */

    clearErrors() {

        $('#employeeForm')
            .find('.is-invalid')
            .removeClass(
                'is-invalid'
            );


        $('#employeeForm')
            .find('[data-field-error]')
            .text('')
            .removeClass(
                'd-block'
            );
    },


    /*
     * -------------------------------------------------------------
     * Save Button
     * -------------------------------------------------------------
     */

    setSaving(
        isSaving
    ) {

        const $button =
            $('#employeeSaveBtn');


        if (!$button.length) {
            return;
        }


        if (isSaving) {

            $button
                .prop(
                    'disabled',
                    true
                )
                .html(`
                    <span
                        class="spinner-border spinner-border-sm me-2"
                        role="status">
                    </span>
                    Saving...
                `);

            return;
        }


        $button
            .html(
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


    /*
     * -------------------------------------------------------------
     * Page Loading
     * -------------------------------------------------------------
     */

    setPageLoading(
        isLoading
    ) {

        const $form =
            $('#employeeForm');


        if (isLoading) {

            $form.addClass(
                'employee-form-loading'
            );

        } else {

            $form.removeClass(
                'employee-form-loading'
            );
        }
    },


    /*
     * -------------------------------------------------------------
     * Photo Preview
     * -------------------------------------------------------------
     */

    previewPhoto(file) {

        if (!file) {
            return;
        }


        if (
            ![
                'image/jpeg',
                'image/png',
                'image/webp'
            ].includes(
                file.type
            )
        ) {

            APP.error(
                'Please select a JPG, PNG or WEBP image.'
            );


            $('#profile_photo')
                .val('');


            return;
        }


        if (
            file.size >
            2 * 1024 * 1024
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


        reader.onload =
            (event) => {

                this.setPhotoPreview(
                    event.target.result
                );
            };


        reader.readAsDataURL(
            file
        );
    },


    /*
     * -------------------------------------------------------------
     * Photo Preview UI
     * -------------------------------------------------------------
     */

    setPhotoPreview(src) {
        $('#employeePhotoPreview')
            .html(`
                <img
                    src="${this.escapeAttribute(src)}"
                    alt="Profile photo">
            `);
    },


    /*
     * -------------------------------------------------------------
     * Select Value
     * -------------------------------------------------------------
     */

    setSelectValue(
        selector,
        value
    ) {

        if (
            value === null ||
            value === undefined ||
            value === ''
        ) {
            return;
        }


        $(selector)
            .val(
                String(value)
            )
            .trigger(
                'change'
            );
    },


    /*
     * -------------------------------------------------------------
     * Photo URL
     * -------------------------------------------------------------
     */

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
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

};


$(function () {
    EmployeeForm.init();
});