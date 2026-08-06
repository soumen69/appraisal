// const CrudForm = {

//     bind(crud) {

//         $(crud.form).submit(function (e) {
//             e.preventDefault();
//             this.submitButton = $('#btnSave');
//             this.submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
//             $.ajax({
//                 url: crud.editId
//                     ? crud.endpoint + '/update/' + crud.editId
//                     : crud.endpoint + '/store', type: 'POST',
//                 data: $(this).serialize(),
//                 success(response) {
//                     $('#btnSave').prop('disabled', false).html('Save');
//                     if (!response.success) {
//                         $('.is-invalid').removeClass('is-invalid');
//                         $('.invalid-feedback').html('');
//                         if (response.errors) {
//                             Object.keys(response.errors).forEach(function (field) {
//                                 $('[name="' + field + '"]')
//                                     .addClass('is-invalid')
//                                     .next('.invalid-feedback')
//                                     .html(response.errors[field]);
//                             });
//                         }
//                         APP.error(response.message);
//                         return;
//                     }
//                     APP.success(response.message);
//                     bootstrap.Modal
//                         .getInstance(crud.modal)
//                         .hide();
//                     crud.reload();
//                 },
//                 error() {
//                     $('#btnSave').prop('disabled', false).html('Save');
//                     APP.error('Request failed.');
//                 }
//             });
//         });
//     },

//     load(crud, id) {
//         $.get(
//             crud.endpoint + '/edit/' + id,
//             function (response) {
//                 Object.keys(response.data).forEach(function (key) {
//                     $('[name="' + key + '"]').val(response.data[key]);
//                 });
//                 $('#crudModalTitle').text('Edit');
//                 crud.editId = id;
//                 new bootstrap.Modal(crud.modal).show();
//             }
//         );
//     },

//     reset(crud) {
//         crud.form.reset();
//         crud.editId = null;
//         $('.is-invalid').removeClass('is-invalid');
//         $('.invalid-feedback').html('');
//         $('#btnSave').prop('disabled', false).html('Save');
//     }
// };


document.addEventListener('DOMContentLoaded', () => {

    loadPermissions();
    loadModules();
    bindEvents();

    function bindEvents() {

        $('#btnAddPermission').on('click', function () {

            $('#crudModalTitle').text('Custom Permission');

            $('#crudForm')[0].reset();

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').html('');

            permissionCrud.editId = null;

            new bootstrap.Modal('#crudModal').show();

        });

        $(document).on('click', '.btn-edit', function () {

            CrudForm.load(
                permissionCrud,
                $(this).data('id')
            );

            $('#crudModalTitle').text('Edit Permission');

        });

        $(document).on('click', '.btn-delete', function () {

            CrudDelete.bind(permissionCrud);

        });

        CrudForm.bind(permissionCrud);

    }

    const permissionCrud = {

        endpoint: `${APP.baseUrl.replace(/\/$/, '')}/permissions`,

        form: document.querySelector('#crudForm'),

        modal: document.querySelector('#crudModal'),

        editId: null,

        reload() {

            loadPermissions();

        }

    };

    function loadModules() {

        $.get(`${APP.baseUrl}/menus/options`, function (response) {

            let html = '<option value="">Select Module</option>';

            response.data.modules.forEach(module => {

                html += `<option value="${module.id}">${module.name}</option>`;

            });

            $('#module_id').html(html);

        });

    }

    function loadPermissions() {

        $.get(`${APP.baseUrl}/permissions/list`, function (response) {

            if (!response.success) {

                APP.error(response.message);

                return;

            }

            render(response.data);

        });

    }

    function render(groups) {

        let html = `
            <div class="d-flex justify-content-between align-items-center mb-4">

                <h5 class="mb-0 fw-semibold">

                    Permission Registry

                </h5>

                <button
                    class="btn app-btn-primary"
                    id="btnAddPermission">

                    <i class="bi bi-plus-lg"></i>

                    Custom Permission

                </button>

            </div>
        `;

        groups.forEach(group => {

            html += `
                <div class="permission-group">

                    <div class="permission-group-header">

                        <div>

                            <div class="permission-group-title">

                                ${group.module}

                            </div>

                            <div class="permission-count">

                                ${group.permissions.length} Permissions

                            </div>

                        </div>

                    </div>

                    <div class="permission-list">
            `;

            group.permissions.forEach(permission => {

                html += `
                    <div class="permission-card">

                        <div class="permission-name">

                            ${permission.name}

                        </div>

                        <div class="permission-slug">

                            ${permission.slug}

                        </div>

                        <div class="permission-footer">

                            <span class="badge bg-secondary">

                                SYSTEM

                            </span>

                            <div class="d-flex gap-1">

                                <button
                                    class="btn btn-light btn-sm btn-edit"
                                    data-id="${permission.id}">

                                    <i class="bi bi-pencil"></i>

                                </button>

                                <button
                                    class="btn btn-light btn-sm text-danger btn-delete"
                                    data-id="${permission.id}">

                                    <i class="bi bi-trash"></i>

                                </button>

                            </div>

                        </div>

                    </div>
                `;

            });

            html += `
                    </div>

                </div>
            `;

        });

        $('#permissionRegistry').html(html);

    }

});