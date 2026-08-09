<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="branch-form-page">

    <div class="branch-form-header">

        <div>

            <a
                href="<?= base_url('branches') ?>"
                class="branch-back-link">
                <i class="bi bi-arrow-left"></i>
                Branches
            </a>

            <h4 class="branch-form-title">
                Create Branch
            </h4>

            <p class="branch-form-subtitle">
                Add a new branch to an organization.
            </p>

        </div>

    </div>


    <?= $this->include(
        'admin/branches/_form'
    ) ?>

</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>
    window.BranchFormConfig = {
        mode: 'create',
        endpoint: "<?= base_url('branches') ?>"
    };
</script>

<script
    src="<?= base_url('assets/js/branches/branch-form.js') ?>"></script>

<?= $this->endSection() ?>