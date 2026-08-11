// const EmployeeForm = {
//     config: window.EmployeeFormConfig || {},

//     endpoint: `${APP.baseUrl}employees`,

//     employee: null,

//     init() {
//         this.bindEvents();

//         this.initializeSelect2();

//         this.initializeDates();

//         this.initializePhoto();

//         if (this.config.mode === 'edit') {
//             this.loadEmployee();
//         }
//     },

//     bindEvents() {
//         $('#employeeForm').on(
//             'submit',
//             (e) => {
//                 e.preventDefault();

//                 this.submit();
//             }
//         );

//         $('#role_ids').on(
//             'change',
//             () => {
//                 this.syncPrimaryRoles();
//             }
//         );

//         $('#organization_id').on(
//             'change',
//             () => {
//                 const organizationId =
//                     $('#organization_id').val();

//                 this.loadBranches(
//                     organizationId
//                 );
//             }
//         );

//         $('#profile_photo').on(
//             'change',
//             (e) => {
//                 this.previewPhoto(
//                     e.target.files[0]
//                 );
//             }
//         );
//     },

//     initializeSelect2() {
//         $('.employee-select2').each(
//             function () {
//                 $(this).select2({
//                     theme: 'bootstrap-5',

//                     width: '100%',

//                     placeholder:
//                         $(this).find(
//                             'option:first'
//                         ).text(),

//                     allowClear:
//                         !$(this).prop('multiple'),

//                     dropdownAutoWidth: true
//                 });
//             }
//         );

//         this.loadOrganizations();

//         this.loadDepartments();

//         this.loadDesignations();

//         this.loadRoles();

//         this.loadManagers();
//     },

//     initializeDates() {
//         flatpickr(
//             '#dob',
//             {
//                 dateFormat: 'Y-m-d',
//                 maxDate: 'today',
//                 allowInput: true
//             }
//         );

//         flatpickr(
//             '#joining_date',
//             {
//                 dateFormat: 'Y-m-d',
//                 allowInput: true
//             }
//         );
//     },

//     initializePhoto() {
//         const existingPhoto =
//             this.config.employee?.profile_photo;

//         if (existingPhoto) {
//             this.setPhotoPreview(
//                 this.resolvePhoto(existingPhoto)
//             );
//         }
//     },

//     loadEmployee() {
//         const id =
//             parseInt(
//                 this.config.employeeId,
//                 10
//             );

//         if (!id) {
//             return;
//         }

//         this.setPageLoading(true);

//         $.ajax({
//             url:
//                 `${this.endpoint} /data/${id}`,

//             type: 'GET',

//             success: (response) => {
//                 if (!response.success) {
//                     APP.error(
//                         response.message ||
//                         'Unable to load employee.'
//                     );

//                     return;
//                 }

//                 this.employee =
//                     response.data || {};

//                 this.populateForm(
//                     this.employee
//                 );
//             },

//             error: () => {
//                 APP.error(
//                     'Unable to load employee.'
//                 );
//             },

//             complete: () => {
//                 this.setPageLoading(false);
//             }
//         });
//     },

//     populateForm(employee) {
//         $('[name="first_name"]')
//             .val(employee.first_name || '');

//         $('[name="last_name"]')
//             .val(employee.last_name || '');

//         $('[name="email"]')
//             .val(employee.email || '');

//         $('[name="phone"]')
//             .val(employee.phone || '');

//         $('[name="gender"]')
//             .val(employee.gender || '')
//             .trigger('change');

//         $('[name="employee_code"]')
//             .val(employee.employee_code || '');

//         $('[name="status"]')
//             .val(employee.status || 'active')
//             .trigger('change');

//         $('[name="dob"]')
//             .val(employee.dob || '');

//         $('[name="joining_date"]')
//             .val(employee.joining_date || '');

//         this.setSelectValue(
//             '#organization_id',
//             employee.organization_id
//         );

//         this.loadBranches(
//             employee.organization_id,
//             employee.branch_id
//         );

//         this.setSelectValue(
//             '#department_id',
//             employee.department_id
//         );

//         this.setSelectValue(
//             '#designation_id',
//             employee.designation_id
//         );

//         this.setSelectValues(
//             '#role_ids',
//             employee.role_ids || []
//         );

//         this.syncPrimaryRoles(
//             employee.primary_role_id
//         );

//         this.setSelectValue(
//             '#reporting_manager_id',
//             employee.reporting_manager_id
//         );

//         if (employee.profile_photo) {
//             this.setPhotoPreview(
//                 this.resolvePhoto(
//                     employee.profile_photo
//                 )
//             );
//         }
//     },

//     loadOrganizations() {
//         this.loadOptions('organizations/options', '#organization_id', 'Select organization');
//     },

//     loadBranches(
//         organizationId,
//         selectedId = null
//     ) {
//         const $branch =
//             $('#branch_id');

//         $branch
//             .prop('disabled', true)
//             .empty()
//             .append(
//                 '<option value="">Select branch</option>'
//             )
//             .trigger('change');

//         if (!organizationId) {
//             return;
//         }

//         this.loadOptions(
//             `branches/options?organization_id=${encodeURIComponent(organizationId)} `,
//             '#branch_id',
//             'Select branch',
//             selectedId
//         );
//     },

//     loadDepartments() {
//         this.loadOptions(
//             'departments/options',
//             '#department_id',
//             'Select department'
//         );
//     },

//     loadDesignations() {
//         this.loadOptions(
//             'designations/options',
//             '#designation_id',
//             'Select designation'
//         );
//     },

//     loadManagers() {
//         this.loadOptions(
//             'employees/options',
//             '#reporting_manager_id',
//             'No reporting manager',
//             this.config.employeeId || null
//         );
//     },

//     loadRoles() {
//         this.loadOptions(
//             'roles/options',
//             '#role_ids',
//             'Select roles'
//         );
//     },

//     loadOptions(
//         path,
//         selector,
//         placeholder,
//         selectedValue = null
//     ) {
//         $.ajax({
//             url:
//                 `${APP.baseUrl}${path}`,

//             type: 'GET',

//             success: (response) => {
//                 const $select =
//                     $(selector);

//                 if (!$select.length) {
//                     return;
//                 }

//                 /*
//                 |--------------------------------------------------------------------------
//                 | Existing project APIs may return :
//                 |
//                 | [
//                 |   { id: 1, name: '...' }
//                 | |]
//                 |
//                 | or:
//                 |
//                 | { success: true, data: [...] }
//                 |--------------------------------------------------------------------------
//                 */

//                 let options =
//                     Array.isArray(response)
//                         ? response
//                         : (
//                             response.data || []
//                         );

//                 if (
//                     !Array.isArray(options)
//                 ) {
//                     options = [];
//                 }

//                 if (
//                     !$select.prop('multiple')
//                 ) {
//                     $select.empty();

//                     $select.append(
//                         $('<option>', {
//                             value: '',
//                             text: placeholder
//                         })
//                     );
//                 } else {
//                     $select.empty();
//                 }

//                 options.forEach(
//                     (option) => {
//                         const id =
//                             option.id ??
//                             option.value;

//                         const text =
//                             option.name ??
//                             option.text ??
//                             option.display_name ??
//                             option.full_name ??
//                             '';

//                         $select.append(
//                             $('<option>', {
//                                 value: id,
//                                 text: text
//                             })
//                         );
//                     }
//                 );

//                 if (selectedValue !== null) {
//                     this.setSelectValue(
//                         selector,
//                         selectedValue
//                     );
//                 }

//                 if (selector === '#branch_id') {
//                     $select.prop(
//                         'disabled',
//                         false
//                     );
//                 }
//             },

//             error: () => {
//                 console.warn(
//                     `Unable to load ${path} `
//                 );
//             }
//         });
//     },

//     syncPrimaryRoles(
//         selectedPrimary = null
//     ) {
//         const selectedRoles =
//             $('#role_ids')
//                 .val() || [];

//         const $primary =
//             $('#primary_role_id');

//         $primary.empty();

//         $primary.append(
//             $('<option>', {
//                 value: '',
//                 text: 'Select primary role'
//             })
//         );

//         selectedRoles.forEach(
//             (roleId) => {
//                 const option =
//                     $('#role_ids option[value="' +
//                         roleId +
//                         '"]');

//                 if (!option.length) {
//                     return;
//                 }

//                 $primary.append(
//                     $('<option>', {
//                         value: roleId,
//                         text: option.text()
//                     })
//                 );
//             }
//         );

//         $primary.prop(
//             'disabled',
//             selectedRoles.length === 0
//         );

//         let value =
//             selectedPrimary !== null
//                 ? String(selectedPrimary)
//                 : $primary.data('selected');

//         if (
//             value &&
//             selectedRoles.map(String).includes(
//                 String(value)
//             )
//         ) {
//             $primary
//                 .val(String(value))
//                 .trigger('change');
//         } else if (
//             selectedRoles.length === 1
//         ) {
//             $primary
//                 .val(String(selectedRoles[0]))
//                 .trigger('change');
//         } else {
//             $primary
//                 .val('')
//                 .trigger('change');
//         }
//     },

//     submit() {
//         this.clearErrors();

//         const form =
//             document.getElementById(
//                 'employeeForm'
//             );

//         if (!form.checkValidity()) {
//             form.classList.add(
//                 'was-validated'
//             );

//             return;
//         }

//         const password =
//             $('#password').val();

//         const confirmation =
//             $('#password_confirmation').val();

//         if (
//             this.config.mode === 'create' &&
//             password !== confirmation
//         ) {
//             this.setFieldError(
//                 'password_confirmation',
//                 'Passwords do not match.'
//             );

//             return;
//         }

//         if (
//             this.config.mode === 'edit' &&
//             password &&
//             password !== confirmation
//         ) {
//             this.setFieldError(
//                 'password_confirmation',
//                 'Passwords do not match.'
//             );

//             return;
//         }

//         const roleIds =
//             $('#role_ids').val() || [];

//         if (!roleIds.length) {
//             this.setFieldError(
//                 'role_ids',
//                 'At least one role must be selected.'
//             );

//             return;
//         }

//         const primaryRole =
//             $('#primary_role_id').val();

//         if (!primaryRole) {
//             this.setFieldError(
//                 'primary_role_id',
//                 'Please select a primary role.'
//             );

//             return;
//         }

//         const formData =
//             new FormData(form);

//         /*
//         |--------------------------------------------------------------------------
//         | Explicit CSRF
//         |--------------------------------------------------------------------------
//         */

//         if (
//             !formData.has(APP.csrfName)
//         ) {
//             formData.append(
//                 APP.csrfName,
//                 APP.csrfHash
//             );
//         }

//         this.setSaving(true);

//         const id =
//             this.config.employeeId;

//         const url =
//             this.config.mode === 'edit'
//                 ? `${this.endpoint} /update/${id}`
//                 : `${this.endpoint} /store`;

//         $.ajax({
//             url: url,

//             type: 'POST',

//             data: formData,

//             processData: false,

//             contentType: false,

//             success: (response) => {
//                 if (!response.success) {
//                     this.handleErrors(
//                         response
//                     );

//                     return;
//                 }

//                 APP.success(
//                     response.message ||
//                     'Employee saved successfully.'
//                 );

//                 setTimeout(
//                     () => {
//                         window.location.href =
//                             this.endpoint;
//                     },
//                     500
//                 );
//             },

//             error: (xhr) => {
//                 if (
//                     xhr.status === 422 &&
//                     xhr.responseJSON
//                 ) {
//                     this.handleErrors(
//                         xhr.responseJSON
//                     );

//                     return;
//                 }

//                 if (xhr.status === 403) {
//                     APP.error(
//                         'You are not authorized.'
//                     );

//                     return;
//                 }

//                 APP.error(
//                     'Unable to save employee.'
//                 );
//             },

//             complete: () => {
//                 this.setSaving(false);
//             }
//         });
//     },

//     handleErrors(response) {
//         this.clearErrors();

//         if (
//             response.errors &&
//             typeof response.errors === 'object'
//         ) {
//             Object.keys(
//                 response.errors
//             ).forEach(
//                 (field) => {
//                     this.setFieldError(
//                         field,
//                         response.errors[field]
//                     );
//                 }
//             );
//         }

//         APP.error(
//             response.message ||
//             'Please correct the highlighted fields.'
//         );
//     },

//     setFieldError(
//         field,
//         message
//     ) {
//         const $field =
//             $(`[name="${field} "]`);

//         const $namedField =
//             $(`[name="${field} []"]`);

//         const $target =
//             $field.length
//                 ? $field
//                 : $namedField;

//         if (!$target.length) {
//             return;
//         }

//         $target.addClass(
//             'is-invalid'
//         );

//         const $error =
//             $(`[data-field-error="${field} "]`);

//         if ($error.length) {
//             $error
//                 .text(message)
//                 .addClass('d-block');
//         }

//         if (
//             $target.hasClass(
//                 'employee-select2'
//             )
//         ) {
//             $target
//                 .next('.select2-container')
//                 .find('.select2-selection')
//                 .addClass('is-invalid');
//         }
//     },

//     clearErrors() {
//         $('.is-invalid')
//             .removeClass('is-invalid');

//         $('[data-field-error]')
//             .text('')
//             .removeClass('d-block');

//         $('#employeeForm')
//             .removeClass('was-validated');
//     },

//     setSaving(isSaving) {
//         const $button =
//             $('#employeeSaveBtn');

//         if (isSaving) {
//             $button
//                 .prop('disabled', true)
//                 .html(`
//                     <span
//                         class="spinner-border spinner-border-sm me-2"
//                         role="status"
//                     ></span>
//                     Saving...
//                 `);
//         } else {
//             $button
//                 .prop('disabled', false)
//                 .html(
//                     this.config.mode === 'edit'
//                         ? '<i class="bi bi-check2 me-1"></i>Save Changes'
//                         : '<i class="bi bi-check2 me-1"></i>Create Employee'
//                 );
//         }
//     },

//     setPageLoading(isLoading) {
//         const $form =
//             $('#employeeForm');

//         if (isLoading) {
//             $form.addClass(
//                 'employee-form-loading'
//             );
//         } else {
//             $form.removeClass(
//                 'employee-form-loading'
//             );
//         }
//     },

//     previewPhoto(file) {
//         if (!file) {
//             return;
//         }

//         if (
//             ![
//                 'image/jpeg',
//                 'image/png',
//                 'image/webp'
//             ].includes(file.type)
//         ) {
//             APP.error(
//                 'Please select a JPG, PNG or WEBP image.'
//             );

//             $('#profile_photo').val('');

//             return;
//         }

//         if (
//             file.size > 2 * 1024 * 1024
//         ) {
//             APP.error(
//                 'Profile photo must not exceed 2MB.'
//             );

//             $('#profile_photo').val('');

//             return;
//         }

//         const reader =
//             new FileReader();

//         reader.onload = (event) => {
//             this.setPhotoPreview(
//                 event.target.result
//             );
//         };

//         reader.readAsDataURL(file);
//     },

//     setPhotoPreview(src) {
//         $('#employeePhotoPreview')
//             .html(`
//                 <img
//                     src="${this.escapeAttribute(src)} "
//                     alt="Profile photo"
//                 >
//             `);
//     },

//     setSelectValue(
//         selector,
//         value
//     ) {
//         if (
//             value === null ||
//             value === undefined
//         ) {
//             return;
//         }

//         $(selector)
//             .val(String(value))
//             .trigger('change');
//     },

//     setSelectValues(
//         selector,
//         values
//     ) {
//         if (!Array.isArray(values)) {
//             values = [values];
//         }

//         $(selector)
//             .val(
//                 values.map(String)
//             )
//             .trigger('change');
//     },

//     resolvePhoto(path) {
//         if (!path) {
//             return '';
//         }

//         if (
//             path.startsWith('http://') ||
//             path.startsWith('https://') ||
//             path.startsWith('/')
//         ) {
//             return path;
//         }

//         return `${APP.baseUrl}${path}`;
//     },

//     escapeAttribute(value) {
//         return String(value ?? '')
//             .replace(/&/g, '&amp;')
//             .replace(/"/g, '&quot;')
//             .replace(/</g, '&lt;')
//             .replace(/>/g, '&gt;');
//     }
// };

// $(function () {
//     EmployeeForm.init();
// });

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

    bindEvents() {
        $('#employeeForm')
            .off('submit.employeeForm')
            .on(
                'submit.employeeForm',
                (e) => {

                    e.preventDefault();

                    this.submit();
                }
            );

        $('#organization_id').off('change.employeeForm').on('change.employeeForm', () => {
            const organizationId = $('#organization_id').val();
            this.loadBranches(organizationId);
            this.loadDepartments(organizationId);
            this.loadDesignations(organizationId);
        }
        );


        $('#profile_photo')
            .off('change.employeeForm')
            .on(
                'change.employeeForm',
                (e) => {

                    this.previewPhoto(
                        e.target.files[0]
                    );
                }
            );
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
        this.loadRoles();
        this.loadManagers();
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
        this.setSelectValues('#role_ids', employee.role_ids || []);

        this.setSelectValue('#reporting_manager_id', employee.reporting_manager_id);

        if (employee.profile_photo) {
            this.setPhotoPreview(this.resolvePhoto(employee.profile_photo));
        }
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


    loadRoles() {
        $.ajax({
            url: `${APP.baseUrl}roles/options`,
            type: 'GET',

            success: (response) => {
                const $select = $('#role_ids');

                if (!$select.length) {
                    return;
                }

                let options = [];

                if (Array.isArray(response)) {
                    options = response;
                } else if (Array.isArray(response.data)) {
                    options = response.data;
                } else if (
                    response.data &&
                    Array.isArray(response.data.data)
                ) {
                    options = response.data.data;
                } else if (Array.isArray(response.roles)) {
                    options = response.roles;
                }

                $select.empty();

                options.forEach((option) => {
                    const id =
                        option.id ??
                        option.value ??
                        option.role_id;

                    const text =
                        option.name ??
                        option.text ??
                        option.role_name ??
                        option.title ??
                        option.display_name ??
                        '';

                    if (id === undefined || text === '') {
                        return;
                    }

                    $select.append(
                        $('<option>', {
                            value: id,
                            text: text
                        })
                    );
                });

                if (
                    this.config.mode === 'edit' &&
                    this.employee &&
                    Array.isArray(this.employee.role_ids)
                ) {
                    this.setSelectValues(
                        '#role_ids',
                        this.employee.role_ids
                    );
                }

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

    submit() {
        this.clearErrors();

        const form = document.getElementById('employeeForm');


        if (!form.checkValidity()) {

            form.classList.add(
                'was-validated'
            );

            return;
        }

        const password =
            $('#password').val();


        const confirmation =
            $('#password_confirmation').val();


        if (

            this.config.mode === 'create' &&

            password !== confirmation

        ) {

            this.setFieldError(
                'password_confirmation',
                'Passwords do not match.'
            );

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

            return;
        }

        const roleIds = $('#role_ids').val() || [];

        if (!roleIds.length) {
            this.setFieldError(
                'role_ids',
                'At least one role must be selected.'
            );

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

        const $button =
            $('#employeeSaveBtn');


        if (isSaving) {

            $button
                .prop(
                    'disabled',
                    true
                )
                .html(`
                    <span
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                    ></span>
                    Saving...
                `);

        } else {
            $button
                .prop('disabled', false)
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
        }
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


    setSelectValue(
        selector,
        value
    ) {

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


    setSelectValues(
        selector,
        values
    ) {

        if (!Array.isArray(values)) {

            values =
                values !== null &&
                    values !== undefined

                    ? [values]

                    : [];
        }

        $(selector)
            .val(
                values.map(String)
            )
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