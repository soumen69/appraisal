<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<?= view('layouts/components/crud_toolbar', [
    'entity' => 'Review Matrix',
    'entityPlural' => 'Review Matrix',
]) ?>

<?= view('layouts/components/crud_table') ?>

<?= view('appraisal/review_matrix/form') ?>

<?= view('layouts/components/crud_drawer') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= base_url('assets/js/crud/crud.utils.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.api.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.table.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.pagination.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.search.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.modal.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.form.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.delete.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.drawer.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.view.js') ?>"></script>
<script src="<?= base_url('assets/js/crud/crud.js') . '?v=' . time() ?>"></script>

<script>
    $(function() {
        new Crud({
            endpoint: '<?= base_url('review-matrix') ?>',
            viewEndpoint: '<?= base_url('review-matrix/view') ?>',
            table: '#crudTable',
            modal: '#crudModal',
            form: '#crudForm',
            entity: 'Review Matrix',
            entityPlural: 'Review Matrix',
            permissionResource: 'review-matrix',

            columns: [{
                    key: 'organization_name',
                    label: 'Organization',
                    render: function(value) {
                        return CrudUtils.escapeHtml(value || '-');
                    }
                },
                {
                    key: 'reviewer_role_name',
                    label: 'Review Relationship',
                    render: function(value, row) {
                        return `
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-semibold">${CrudUtils.escapeHtml(value || '-')}</span>
                            <i class="bi bi-arrow-right text-muted"></i>
                            <span>${CrudUtils.escapeHtml(row.reviewee_role_name || '-')}</span>
                        </div>
                    `;
                    }
                },
                {
                    key: 'allow_self_review',
                    label: 'Self Review',
                    render: function(value) {
                        return Number(value) === 1 ?
                            '<span class="badge bg-primary-subtle text-primary">Allowed</span>' :
                            '<span class="badge bg-light text-muted border">Not Allowed</span>';
                    }
                },
                {
                    key: 'is_active',
                    label: 'Status',
                    render: function(value) {
                        return Number(value) === 1 ?
                            '<span class="badge bg-success-subtle text-success">Active</span>' :
                            '<span class="badge bg-secondary-subtle text-secondary">Inactive</span>';
                    }
                }
            ],

            actionRenderer: function(row, id, crud) {
                const actions = [];

                if (crud.can('view')) {
                    actions.push(`
                    <li>
                        <a class="dropdown-item btn-view" href="#" data-id="${id}">
                            <i class="bi bi-eye me-2"></i>View
                        </a>
                    </li>
                `);
                }

                if (crud.can('edit')) {
                    actions.push(`
                    <li>
                        <a class="dropdown-item btn-edit" href="#" data-id="${id}">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                    </li>
                `);
                }

                if (crud.can('delete')) {
                    if (actions.length) {
                        actions.push('<li><hr class="dropdown-divider"></li>');
                    }

                    actions.push(`
                    <li>
                        <a class="dropdown-item text-danger btn-delete" href="#" data-id="${id}">
                            <i class="bi bi-trash me-2"></i>Delete
                        </a>
                    </li>
                `);
                }

                if (!actions.length) {
                    actions.push('<li><span class="dropdown-item-text text-muted">No available actions</span></li>');
                }

                return actions.join('');
            },

            drawerRenderer: function(data) {
                return `
                <div class="drawer-row mb-4">
                    <div class="small text-muted text-uppercase fw-semibold mb-1">Review Relationship</div>
                    <div class="d-flex align-items-center gap-2 fs-5">
                        <span class="fw-semibold">${CrudUtils.escapeHtml(data.reviewer_role_name || '-')}</span>
                        <i class="bi bi-arrow-right text-muted"></i>
                        <span class="fw-semibold">${CrudUtils.escapeHtml(data.reviewee_role_name || '-')}</span>
                    </div>
                </div>

                <div class="drawer-row mb-3">
                    <div class="small text-muted text-uppercase fw-semibold mb-1">Organization</div>
                    <div>${CrudUtils.escapeHtml(data.organization_name || '-')}</div>
                </div>

                <div class="drawer-row mb-3">
                    <div class="small text-muted text-uppercase fw-semibold mb-1">Self Review</div>
                    <div>
                        ${Number(data.allow_self_review) === 1
                            ? '<span class="badge bg-primary-subtle text-primary">Allowed</span>'
                            : '<span class="badge bg-light text-muted border">Not Allowed</span>'}
                    </div>
                </div>

                <div class="drawer-row mb-3">
                    <div class="small text-muted text-uppercase fw-semibold mb-1">Status</div>
                    <div>
                        ${Number(data.is_active) === 1
                            ? '<span class="badge bg-success-subtle text-success">Active</span>'
                            : '<span class="badge bg-secondary-subtle text-secondary">Inactive</span>'}
                    </div>
                </div>
            `;
            }
        });

        loadOrganizations();
        loadRoles();
    });

    function loadOrganizations() {
        $.ajax({
            url: '<?= base_url('organizations/options') ?>',
            type: 'GET',

            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to load organizations.');
                    return;
                }

                const $organization = $('#organization_id');
                const selectedValue = $organization.val();

                $organization.empty().append('<option value="">Select Organization</option>');

                (response.data || []).forEach(function(organization) {
                    $organization.append($('<option>', {
                        value: organization.id,
                        text: organization.name
                    }));
                });

                if (selectedValue) {
                    $organization.val(selectedValue);
                }
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;
                APP.error(xhr.responseJSON?.message || 'Unable to load organizations.');
            }
        });
    }

    function loadRoles() {
        $.ajax({
            url: '<?= base_url('roles/options') ?>',
            type: 'GET',
            success(response) {
                if (!response.success) {
                    APP.error(response.message || 'Unable to load roles.');
                    return;
                }
                const roles = response.data?.roles || [];
                populateRoleOptions('#reviewer_role_id', roles, 'Select Reviewer Role');
                populateRoleOptions('#reviewee_role_id', roles, 'Select Reviewee Role');
            },

            error(xhr) {
                if (APP.handleUnauthorized(xhr)) return;
                APP.error(xhr.responseJSON?.message || 'Unable to load roles.');
            }
        });
    }

    function populateRoleOptions(selector, roles, placeholder) {
        const $select = $(selector);
        const selectedValue = $select.val();

        $select.empty().append(`<option value="">${placeholder}</option>`);

        roles.forEach(function(role) {
            $select.append($('<option>', {
                value: role.id,
                text: role.display_name || role.name
            }));
        });

        if (selectedValue) {
            $select.val(selectedValue);
        }
    }

    $(document).on('crud:editLoaded', function(event, data) {
        $('#organization_id').val(data.organization_id).trigger('change');
        $('#reviewer_role_id').val(data.reviewer_role_id).trigger('change');
        $('#reviewee_role_id').val(data.reviewee_role_id).trigger('change');
        $('#allow_self_review').prop('checked', Number(data.allow_self_review) === 1);
        $('#is_active').prop('checked', Number(data.is_active) === 1);
    });
</script>

<?= $this->endSection() ?>