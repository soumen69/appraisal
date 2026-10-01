<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= esc($title ?? 'Login') ?> | Appraisal System</title>

    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/variables.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>

<body>

    <?php
    $otpEmail = session()->getFlashdata('otp_email');
    $showOtpStep = !empty($otpEmail);

    // Preserve an email entered before a validation or request error.
    $enteredEmail = old('email', $otpEmail ?? '');
    ?>

    <div class="login-wrapper">

        <!-- Left branding panel -->
        <aside class="login-left">

            <div class="brand-box">
                <div class="brand-logo">
                    <i class="bi bi-graph-up-arrow" aria-hidden="true"></i>
                </div>

                <h1>Appraisal</h1>

                <p>
                    Modern Performance &amp; Appraisal Management Platform
                </p>
            </div>

            <div class="login-illustration">
                <img
                    src="<?= base_url('assets/images/login-illustration.webp') ?>"
                    alt=""
                    aria-hidden="true">
            </div>

            <div class="login-footer-text">
                Performance isn't measured once a year.
                It's built every day.
            </div>

        </aside>

        <!-- Login panel -->
        <main class="login-right">

            <section class="login-card" aria-labelledby="loginHeading">

                <span class="login-badge">
                    <i class="bi bi-shield-check" aria-hidden="true"></i>
                    Secure sign-in
                </span>

                <h2 id="loginHeading">Sign in</h2>

                <p class="login-subtitle" id="loginSubtitle">
                    <?= $showOtpStep
                        ? 'Enter the verification code sent to your email address.'
                        : 'Enter your work email address to receive a verification code.' ?>
                </p>

                <!-- Server-side feedback -->
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="auth-alert auth-alert-danger" role="alert">
                        <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                        <div><?= esc(session()->getFlashdata('error')) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="auth-alert auth-alert-success" role="status">
                        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                        <div><?= esc(session()->getFlashdata('success')) ?></div>
                    </div>
                <?php endif; ?>

                <!-- STEP 1: Request OTP -->
                <div
                    id="emailStep"
                    class="auth-step <?= !$showOtpStep ? 'active' : '' ?>"
                    <?= $showOtpStep ? 'hidden' : '' ?>>

                    <form
                        id="requestOtpForm"
                        method="post"
                        action="<?= base_url('login/request-otp') ?>"
                        novalidate>

                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label for="email" class="form-label">
                                Work email address
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control auth-input"
                                placeholder="you@company.com"
                                value="<?= esc($enteredEmail) ?>"
                                autocomplete="email"
                                inputmode="email"
                                maxlength="150"
                                required
                                autofocus>

                            <div class="invalid-feedback">
                                Please enter a valid email address.
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="btn login-btn w-100"
                            id="requestOtpButton">

                            <span class="button-label">
                                Continue with email
                            </span>

                            <i class="bi bi-arrow-right" aria-hidden="true"></i>

                            <span
                                class="spinner-border d-none"
                                role="status"
                                aria-hidden="true"></span>
                        </button>

                    </form>
                </div>

                <!-- STEP 2: Verify OTP -->
                <div
                    id="otpStep"
                    class="auth-step <?= $showOtpStep ? 'active' : '' ?>"
                    <?= !$showOtpStep ? 'hidden' : '' ?>>

                    <!-- Email summary with edit button -->
                    <div class="email-summary">

                        <div class="email-summary-content">
                            <span class="email-summary-label">
                                Verification code sent to
                            </span>

                            <span class="email-summary-address" id="displayEmail">
                                <?= esc($otpEmail ?? '') ?>
                            </span>
                        </div>

                        <button
                            type="button"
                            class="edit-email-btn"
                            id="editEmailButton"
                            aria-label="Change email address"
                            title="Change email address">

                            <i class="bi bi-pencil" aria-hidden="true"></i>
                        </button>

                    </div>

                    <form
                        id="verifyOtpForm"
                        method="post"
                        action="<?= base_url('login/verify-otp') ?>"
                        novalidate>

                        <?= csrf_field() ?>

                        <input
                            type="hidden"
                            name="email"
                            id="otpEmail"
                            value="<?= esc($otpEmail ?? '') ?>">

                        <input
                            type="hidden"
                            name="otp"
                            id="otpValue"
                            value="">

                        <label class="form-label otp-label" for="otpDigit1">
                            Enter verification code
                        </label>

                        <p class="otp-hint">
                            Enter the 6-digit code from your email.
                            The code expires in 5 minutes.
                        </p>

                        <div
                            class="otp-inputs"
                            id="otpInputs"
                            role="group"
                            aria-label="Six-digit verification code">

                            <?php for ($i = 1; $i <= 6; $i++) : ?>
                                <input
                                    type="text"
                                    class="otp-digit"
                                    id="otpDigit<?= $i ?>"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    maxlength="1"
                                    autocomplete="<?= $i === 1 ? 'one-time-code' : 'off' ?>"
                                    aria-label="Digit <?= $i ?>"
                                    required>
                            <?php endfor; ?>

                        </div>

                        <div
                            id="otpClientError"
                            class="auth-alert auth-alert-danger"
                            role="alert"
                            hidden>
                            <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                            <div>Please enter all 6 digits of the verification code.</div>
                        </div>

                        <button
                            type="submit"
                            class="btn login-btn w-100"
                            id="verifyOtpButton">

                            <span class="button-label">
                                Verify and sign in
                            </span>

                            <i class="bi bi-arrow-right" aria-hidden="true"></i>

                            <span
                                class="spinner-border d-none"
                                role="status"
                                aria-hidden="true"></span>
                        </button>

                    </form>

                    <div class="otp-actions">

                        <span>Didn't receive the code?</span>

                        <!-- Resend by requesting a new OTP -->
                        <form
                            id="resendOtpForm"
                            method="post"
                            action="<?= base_url('login/request-otp') ?>">

                            <?= csrf_field() ?>

                            <input
                                type="hidden"
                                name="email"
                                value="<?= esc($otpEmail ?? '') ?>">

                            <button
                                type="submit"
                                class="text-action"
                                id="resendOtpButton">

                                Resend code
                            </button>

                        </form>

                    </div>

                </div>

                <div class="auth-divider"></div>

                <div class="security-note">
                    <i class="bi bi-lock-fill" aria-hidden="true"></i>
                    Your sign-in is protected with email verification.
                </div>

            </section>

        </main>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailStep = document.getElementById('emailStep');
            const otpStep = document.getElementById('otpStep');

            const emailInput = document.getElementById('email');
            const displayEmail = document.getElementById('displayEmail');
            const otpEmail = document.getElementById('otpEmail');

            const editEmailButton = document.getElementById('editEmailButton');

            const requestOtpForm = document.getElementById('requestOtpForm');
            const verifyOtpForm = document.getElementById('verifyOtpForm');
            const resendOtpForm = document.getElementById('resendOtpForm');

            const requestOtpButton = document.getElementById('requestOtpButton');
            const verifyOtpButton = document.getElementById('verifyOtpButton');
            const resendOtpButton = document.getElementById('resendOtpButton');

            const otpValue = document.getElementById('otpValue');
            const otpClientError = document.getElementById('otpClientError');
            const otpDigits = Array.from(document.querySelectorAll('.otp-digit'));

            const loginSubtitle = document.getElementById('loginSubtitle');

            /**
             * Switch between email and OTP steps.
             */
            function showStep(step) {
                const showOtp = step === 'otp';

                emailStep.classList.toggle('active', !showOtp);
                otpStep.classList.toggle('active', showOtp);

                emailStep.hidden = showOtp;
                otpStep.hidden = !showOtp;

                loginSubtitle.textContent = showOtp ?
                    'Enter the verification code sent to your email address.' :
                    'Enter your work email address to receive a verification code.';

                if (showOtp) {
                    focusFirstEmptyDigit();
                } else {
                    emailInput.focus();
                }
            }

            /**
             * Focus the first empty OTP box, or the last box when all are filled.
             */
            function focusFirstEmptyDigit() {
                const firstEmpty = otpDigits.find(input => input.value === '');

                if (firstEmpty) {
                    firstEmpty.focus();
                } else {
                    otpDigits[otpDigits.length - 1].focus();
                }
            }

            /**
             * Collect the six digits into the hidden OTP field.
             */
            function syncOtpValue() {
                otpValue.value = otpDigits.map(input => input.value).join('');
            }

            /**
             * Clear OTP fields and client-side validation feedback.
             */
            function clearOtp() {
                otpDigits.forEach(input => {
                    input.value = '';
                    input.classList.remove('is-invalid');
                });

                otpValue.value = '';
                otpClientError.hidden = true;
            }

            /**
             * Set a button to loading or normal state.
             */
            function setButtonLoading(button, loading, loadingText) {
                const label = button.querySelector('.button-label');
                const icon = button.querySelector('.bi-arrow-right');
                const spinner = button.querySelector('.spinner-border');

                if (loading) {
                    button.disabled = true;

                    if (label) {
                        button.dataset.originalText = label.textContent.trim();
                        label.textContent = loadingText;
                    }

                    if (icon) {
                        icon.classList.add('d-none');
                    }

                    if (spinner) {
                        spinner.classList.remove('d-none');
                    }
                } else {
                    button.disabled = false;

                    if (label && button.dataset.originalText) {
                        label.textContent = button.dataset.originalText;
                    }

                    if (icon) {
                        icon.classList.remove('d-none');
                    }

                    if (spinner) {
                        spinner.classList.add('d-none');
                    }
                }
            }

            /**
             * Email validation before sending the request.
             */
            function isValidEmail(value) {
                const email = value.trim();

                // Basic browser-side check. Server-side validation is still required.
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            /**
             * Edit email: return to step one and populate the existing address.
             */
            editEmailButton.addEventListener('click', function() {
                emailInput.value = otpEmail.value;
                clearOtp();
                showStep('email');
            });

            /**
             * Request OTP form validation and loading feedback.
             */
            requestOtpForm.addEventListener('submit', function(event) {
                const email = emailInput.value.trim();

                emailInput.classList.remove('is-invalid');

                if (!isValidEmail(email)) {
                    event.preventDefault();
                    emailInput.classList.add('is-invalid');
                    emailInput.focus();
                    return;
                }

                emailInput.value = email;
                setButtonLoading(requestOtpButton, true, 'Sending code...');
            });

            emailInput.addEventListener('input', function() {
                emailInput.classList.remove('is-invalid');
            });

            /**
             * OTP input behaviour:
             * - digits only
             * - advance focus after typing
             * - move back on Backspace
             * - support pasting a complete code
             */
            otpDigits.forEach((input, index) => {
                input.addEventListener('input', function() {
                    const digits = input.value.replace(/\D/g, '');

                    input.value = digits.slice(-1);
                    input.classList.remove('is-invalid');
                    otpClientError.hidden = true;

                    if (input.value !== '' && index < otpDigits.length - 1) {
                        otpDigits[index + 1].focus();
                    }

                    syncOtpValue();
                });

                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Backspace' && input.value === '' && index > 0) {
                        otpDigits[index - 1].focus();
                    }

                    if (event.key === 'ArrowLeft' && index > 0) {
                        otpDigits[index - 1].focus();
                    }

                    if (event.key === 'ArrowRight' && index < otpDigits.length - 1) {
                        otpDigits[index + 1].focus();
                    }
                });

                input.addEventListener('paste', function(event) {
                    event.preventDefault();

                    const pasted = (event.clipboardData || window.clipboardData)
                        .getData('text')
                        .replace(/\D/g, '')
                        .slice(0, otpDigits.length);

                    if (!pasted) {
                        return;
                    }

                    clearOtp();

                    pasted.split('').forEach((digit, digitIndex) => {
                        if (otpDigits[digitIndex]) {
                            otpDigits[digitIndex].value = digit;
                        }
                    });

                    syncOtpValue();

                    const nextEmpty = otpDigits.find(field => field.value === '');

                    if (nextEmpty) {
                        nextEmpty.focus();
                    } else {
                        otpDigits[otpDigits.length - 1].focus();
                    }
                });
            });

            /**
             * Verify OTP form validation.
             */
            verifyOtpForm.addEventListener('submit', function(event) {
                syncOtpValue();

                const code = otpValue.value;

                if (!/^\d{6}$/.test(code)) {
                    event.preventDefault();

                    otpClientError.hidden = false;

                    otpDigits.forEach(input => {
                        if (input.value === '') {
                            input.classList.add('is-invalid');
                        }
                    });

                    focusFirstEmptyDigit();
                    return;
                }

                setButtonLoading(verifyOtpButton, true, 'Verifying...');
            });

            /**
             * Resend OTP loading feedback.
             */
            resendOtpForm.addEventListener('submit', function() {
                resendOtpButton.disabled = true;
                resendOtpButton.textContent = 'Sending...';
            });

            /**
             * If the page was rendered with an OTP email from flashdata,
             * start directly on the OTP step.
             */
            if (otpEmail && otpEmail.value.trim() !== '') {
                displayEmail.textContent = otpEmail.value;
                showStep('otp');
            } else {
                showStep('email');
            }
        });
    </script>

</body>

</html>