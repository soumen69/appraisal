<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="unauthorized-page">

    <div class="unauthorized-card">

        <div class="unauthorized-icon">
            <i class="bi bi-shield-lock"></i>
        </div>

        <div class="unauthorized-code">
            403
        </div>

        <h1 class="unauthorized-title">
            Access Denied
        </h1>

        <p class="unauthorized-description">
            You don't have permission to access this page or perform this action.
            Please contact your administrator if you believe this is a mistake.
        </p>

        <div class="unauthorized-actions">

            <button
                type="button"
                class="btn app-btn-light"
                id="unauthorizedBackBtn">
                <i class="bi bi-arrow-left me-1"></i>
                Go Back
            </button>

            <a
                href="<?= base_url('dashboard') ?>"
                class="btn app-btn-primary">
                <i class="bi bi-grid-1x2 me-1"></i>
                Dashboard
            </a>

        </div>

    </div>

</div>

<?= $this->endSection() ?>


<?= $this->section('styles') ?>

<style>
    .unauthorized-page {
        min-height: calc(100vh - 170px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 32px 16px;
    }

    .unauthorized-card {
        width: 100%;
        max-width: 620px;
        padding: 48px 40px;
        text-align: center;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 18px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.06);
    }

    .unauthorized-icon {
        width: 76px;
        height: 76px;
        margin: 0 auto 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        background: #fff1f2;
        color: #dc3545;
        font-size: 34px;
    }

    .unauthorized-code {
        font-size: 68px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: -3px;
        color: #212529;
    }

    .unauthorized-title {
        margin: 18px 0 10px;
        font-size: 26px;
        font-weight: 700;
        color: #212529;
    }

    .unauthorized-description {
        max-width: 470px;
        margin: 0 auto;
        color: #6c757d;
        font-size: 15px;
        line-height: 1.7;
    }

    .unauthorized-actions {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .unauthorized-actions .btn {
        min-width: 130px;
    }

    @media (max-width: 576px) {

        .unauthorized-page {
            min-height: calc(100vh - 130px);
            padding: 20px 12px;
        }

        .unauthorized-card {
            padding: 38px 22px;
            border-radius: 16px;
        }

        .unauthorized-icon {
            width: 68px;
            height: 68px;
            font-size: 30px;
        }

        .unauthorized-code {
            font-size: 56px;
        }

        .unauthorized-title {
            font-size: 22px;
        }

        .unauthorized-actions {
            flex-direction: column;
        }

        .unauthorized-actions .btn {
            width: 100%;
        }
    }
</style>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>
    (() => {
        const button = document.getElementById('unauthorizedBackBtn');

        if (!button) {
            return;
        }

        button.addEventListener('click', () => {

            if (window.history.length > 1) {
                window.history.back();
                return;
            }

            window.location.href =
                <?= json_encode(base_url('dashboard')) ?>;
        });
    })();
</script>

<?= $this->endSection() ?>