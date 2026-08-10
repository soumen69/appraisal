<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<?= view('layouts/components/crud_toolbar', [
    'entity'       => 'Designation',
    'entityPlural' => 'Designations',
]) ?>

<?= view('layouts/components/crud_modal', [
    'entity' => 'Designation',
    'form'   => view('designations/form', [
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

        const designationCrud = new Crud({

            endpoint: '<?= base_url('designations') ?>',

            viewEndpoint: '<?= base_url('designations/view') ?>',

            table: '#crudTable',

            modal: '#crudModal',

            form: '#crudForm',

            entity: 'Designation',

            entityPlural: 'Designations',

            columns: [

                {
                    key: 'designation_code',
                    label: 'Code'
                },

                {
                    key: 'title',
                    label: 'Designation'
                },

                {
                    key: 'organization_names',
                    label: 'Organizations'
                },

                {
                    key: 'level',
                    label: 'Level'
                },

                {
                    key: 'description',
                    label: 'Description',

                    render: function(value) {

                        if (!value) {
                            return '-';
                        }

                        value = String(value);

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
         * Initialize organization multi-select.
         */
        $('#crudModal').on(
            'shown.bs.modal',
            function() {

                const $organizations =
                    $('#designationOrganizations');

                if (!$organizations.length) {
                    return;
                }

                if (
                    !$organizations.hasClass(
                        'select2-hidden-accessible'
                    )
                ) {

                    $organizations.select2({

                        theme: 'bootstrap-5',

                        width: '100%',

                        placeholder: 'Select organizations',

                        allowClear: true,

                        closeOnSelect: false,

                        dropdownParent: $('#crudModal')

                    });

                }

                /*
                 * Ensure Select2 reflects values
                 * populated by CrudForm.
                 */
                $organizations.trigger('change');

            }
        );

    });
</script>

<?= $this->endSection() ?>