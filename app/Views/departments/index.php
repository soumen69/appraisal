<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<?= view('layouts/components/crud_toolbar', [
    'entity'       => 'Department',
    'entityPlural' => 'Departments',
]) ?>

<?= view('layouts/components/crud_modal', [
    'entity' => 'Department',
    'form'   => view('departments/form', [
        'organizations' => $organizations,
    ]),
]) ?>

<?= view('layouts/components/crud_table') ?>

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

        const departmentCrud = new Crud({

            endpoint: '<?= base_url('departments') ?>',

            table: '#crudTable',

            modal: '#crudModal',

            form: '#crudForm',

            entity: 'Department',

            entityPlural: 'Departments',

            columns: [

                {
                    key: 'department_code',
                    label: 'Code'
                },

                {
                    key: 'name',
                    label: 'Department'
                },

                {
                    key: 'organization_names',
                    label: 'Organizations',
                    render: function(value) {

                        if (!value) {
                            return '-';
                        }

                        return value;

                    }
                },

                {
                    key: 'description',
                    label: 'Description',
                    render: function(value) {

                        if (!value) {
                            return '-';
                        }

                        return value.length > 70 ?
                            value.substring(0, 70) + '...' :
                            value;

                    }
                },

                {
                    key: 'status',
                    label: 'Status'
                }

            ]

        });


        /*
         * Initialize Organizations Select2
         * after the CRUD modal becomes visible.
         */
        $('#crudModal')
            .on('shown.bs.modal', function() {

                const $select =
                    $('#departmentOrganizations');

                if (
                    !$select.hasClass(
                        'select2-hidden-accessible'
                    )
                ) {

                    $select.select2({

                        theme: 'bootstrap-5',

                        width: '100%',

                        placeholder: 'Select organizations',

                        allowClear: true,

                        closeOnSelect: false,

                        dropdownParent: $('#crudModal')

                    });

                }

                /*
                 * Make sure hydrated edit values
                 * are reflected in Select2.
                 */
                $select.trigger('change');

            });

    });
</script>

<?= $this->endSection() ?>