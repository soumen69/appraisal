<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Appraisal System</title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body>
<div class="login-wrapper">
    <div class="login-left">
        <div class="brand-box">
            <div class="brand-logo">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <h1>
                Appraisal
            </h1>
            <p>
                Modern Performance & Appraisal Management Platform
            </p>
        </div>
        <div class="login-illustration">
            <img src="<?= base_url('assets/images/login-illustration.webp') ?>"alt="">
        </div>
        <div class="login-footer-text">
            Performance isn't measured once a year.
            It's built every day.
        </div>
    </div>
    <div class="login-right">
        <div class="login-card">
            <span class="login-badge">
                Welcome Back
            </span>
            <h2>
                Sign in
            </h2>
            <p>
                Login to continue to your dashboard.
            </p>
            <form>
                <div class="mb-4">
                    <label>
                        Email
                    </label>
                    <input
                            type="email"
                            class="form-control"
                            placeholder="john@example.com">
                </div>
                <div class="mb-3">
                    <label>
                        Password
                    </label>
                    <input
                            type="password"
                            class="form-control"
                            placeholder="••••••••">
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input
                                class="form-check-input"
                                type="checkbox">
                        <label class="form-check-label">
                            Remember me
                        </label>
                    </div>
                    <a href="#">
                        Forgot Password?
                    </a>
                </div>
                <button
                        class="btn btn-primary w-100 login-btn">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>