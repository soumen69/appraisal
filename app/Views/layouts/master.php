<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= esc($title ?? 'Appraisal System') ?></title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>">

    <!-- DataTables -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/datatables/css/dataTables.bootstrap5.min.css') ?>">

    <!-- Select2 -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/select2/css/select2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/select2-theme/select2-bootstrap-5-theme.min.css') ?>">
    <!-- Flatpickr -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/flatpickr/flatpickr.min.css') ?>">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/sweetalert2/sweetalert2.min.css') ?>">

    <!-- Toastr -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/toastr/build/toastr.min.css') ?>">

    <!-- App CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/layout.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/navbar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/sidebar.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/cards.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/forms.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/table.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/crud.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/modal.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/utilities.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/responsive.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/employees.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/branches.css') ?>">

    <?= $this->renderSection('styles') ?>
</head>

<body>

    <div class="app-wrapper">

        <?= $this->include('layouts/sidebar') ?>

        <div class="app-main">

            <?= $this->include('layouts/navbar') ?>

            <main class="app-content">

                <?= $this->include('layouts/breadcrumbs') ?>

                <?= $this->renderSection('content') ?>

            </main>

            <?= $this->include('layouts/footer') ?>

        </div>

    </div>


    <script>
        window.APP = {
            baseUrl: "<?= base_url() ?>",
            csrfName: "<?= csrf_token() ?>",
            csrfHash: "<?= csrf_hash() ?>",

            permissions: <?= json_encode(
                                session('permissions') ?? [],
                                JSON_HEX_TAG |
                                    JSON_HEX_APOS |
                                    JSON_HEX_AMP |
                                    JSON_HEX_QUOT
                            ) ?>,

            isSuper: <?= session('is_super') ? 'true' : 'false' ?>,

            can(permission) {
                if (this.isSuper) {
                    return true;
                }

                if (!permission) {
                    return false;
                }

                return Array.isArray(this.permissions) &&
                    this.permissions.includes(permission);
            }
        };
    </script>

    <!-- jQuery -->
    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>

    <!-- Bootstrap -->
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

    <!-- DataTables -->
    <script src="<?= base_url('assets/vendor/datatables-core/dataTables.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/datatables/js/dataTables.bootstrap5.min.js') ?>"></script>

    <!-- Select2 -->
    <script src="<?= base_url('assets/vendor/select2/js/select2.full.min.js') ?>"></script>

    <!-- Flatpickr -->
    <script src="<?= base_url('assets/vendor/flatpickr/flatpickr.js') ?>"></script>

    <!-- SweetAlert2 -->
    <script src="<?= base_url('assets/vendor/sweetalert2/sweetalert2.all.min.js') ?>"></script>

    <!-- Toastr -->
    <script src="<?= base_url('assets/vendor/toastr/build/toastr.min.js') ?>"></script>

    <!-- Chart.js -->
    <script src="<?= base_url('assets/vendor/chartjs/chart.umd.js') ?>"></script>
    <!-- <script>
        window.APP = {
            baseUrl: "<?= base_url() ?>",
            csrfName: "<?= csrf_token() ?>",
            csrfHash: "<?= csrf_hash() ?>"
        };
    </script> -->

    <!-- App JS -->
    <script src="<?= base_url('assets/js/common.js') ?>"></script>
    <script src="<?= base_url('assets/js/ajax.js') ?>"></script>
    <script src="<?= base_url('assets/js/sidebar.js') ?>"></script>
    <script src="<?= base_url('assets/js/validation.js') ?>"></script>
    <script src="<?= base_url('assets/js/dashboard.js') ?>"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>

</body>

</html>