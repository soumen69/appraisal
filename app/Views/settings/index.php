<?= $this->extend('layouts/master') ?>
<?= $this->section('content') ?>

<style>
    .settings-page {
        max-width: 1180px;
        margin: 0 auto
    }

    .settings-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        background: #fff
    }

    .settings-section-title {
        font-size: 1rem;
        font-weight: 650;
        margin-bottom: 3px
    }

    .settings-section-subtitle {
        font-size: .82rem;
        color: #6c757d;
        margin-bottom: 0
    }

    .setting-row {
        padding: 15px 0;
        border-bottom: 1px solid #f0f1f3
    }

    .setting-row:last-child {
        border-bottom: 0;
        padding-bottom: 0
    }

    .setting-label {
        font-size: .9rem;
        font-weight: 600;
        margin-bottom: 3px
    }

    .setting-help {
        font-size: .79rem;
        color: #6c757d;
        margin: 0
    }

    .settings-table th {
        font-size: .78rem;
        font-weight: 600;
        color: #6c757d;
        white-space: nowrap;
        background: #f8f9fa
    }

    .settings-table td {
        vertical-align: middle
    }

    .settings-table .form-control,
    .settings-table .form-select {
        min-width: 110px
    }

    .form-switch .form-check-input {
        width: 3em;
    }

    .settings-toggle {
        width: 2.6rem;
        height: 1.35rem;
        cursor: pointer
    }

    .settings-savebar {
        position: sticky;
        bottom: 0;
        z-index: 10;
        background: rgba(255, 255, 255, .96);
        border-top: 1px solid #e9ecef;
        padding: 12px 0;
        margin-top: 18px;
        backdrop-filter: blur(8px)
    }
</style>

<div class="container-fluid py-0">
    <div class="settings-page">
        <form id="settingsForm">
            <?= csrf_field() ?>

            <!-- APPLICATION SETTINGS -->
            <div class="settings-card mb-4">
                <div class="p-3 p-md-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-sliders2 fs-5 text-primary"></i>
                        <div>
                            <h5 class="settings-section-title">Application settings</h5>
                            <p class="settings-section-subtitle">Control email notifications and appraisal reminders.</p>
                        </div>
                    </div>
                </div>
                <div class="px-3 px-md-4 pb-3">
                    <!-- Email notifications -->
                    <div class="setting-row d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <div class="setting-label">Email notifications</div>
                            <p class="setting-help">Allow the application to send appraisal-related emails.</p>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input settings-toggle setting-input" type="checkbox" id="email_notifications" data-key="email_notifications" value="1">
                        </div>
                    </div>

                    <!-- Appraisal reminders -->
                    <div class="setting-row">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                            <div>
                                <div class="setting-label">Appraisal reminders</div>
                                <p class="setting-help">Send reminders to users before their appraisal deadline.</p>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input settings-toggle setting-input" type="checkbox" id="appraisal_reminders" data-key="appraisal_reminders" value="1">
                            </div>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <label for="reminder_frequency" class="form-label small fw-semibold">Reminder frequency</label>
                                <div class="input-group">
                                    <input type="number" class="form-control setting-input" id="reminder_frequency" data-key="reminder_frequency" min="1" max="365" value="3">
                                    <span class="input-group-text">days</span>
                                </div>
                                <div class="form-text">How often reminders are sent before the deadline.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Overdue reminders -->
                    <div class="setting-row">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                            <div>
                                <div class="setting-label">Overdue reminders</div>
                                <p class="setting-help">Continue reminding users when an appraisal is overdue.</p>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input settings-toggle setting-input" type="checkbox" id="overdue_reminders" data-key="overdue_reminders" value="1">
                            </div>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <label for="overdue_reminder_frequency" class="form-label small fw-semibold">Repeat overdue reminder every</label>
                                <div class="input-group">
                                    <input type="number" class="form-control setting-input" id="overdue_reminder_frequency" data-key="overdue_reminder_frequency" min="1" max="365" value="3">
                                    <span class="input-group-text">days</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- APPRAISAL SETTINGS -->
            <div class="settings-card mb-4">
                <div class="p-3 p-md-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clipboard-check fs-5 text-primary"></i>
                        <div>
                            <h5 class="settings-section-title">Appraisal settings</h5>
                            <p class="settings-section-subtitle">Define appraisal eligibility, increment rules, approvals, and deadlines.</p>
                        </div>
                    </div>
                </div>
                <div class="px-3 px-md-4 pb-3">
                    <!-- Minimum service period -->
                    <div class="setting-row">
                        <div class="setting-label">Minimum service period</div>
                        <p class="setting-help mb-3">Minimum completed service required for appraisal eligibility.</p>
                        <div class="row g-2">
                            <div class="col-sm-4 col-md-3">
                                <div class="input-group">
                                    <input type="number" class="form-control setting-input" id="minimum_service_period" data-key="minimum_service_period" min="0" max="600" value="6">
                                    <span class="input-group-text">months</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Probation eligibility -->
                    <div class="setting-row d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <div class="setting-label">Probation eligibility</div>
                            <p class="setting-help">Allow employees who are currently on probation to participate in appraisals.</p>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input settings-toggle setting-input" type="checkbox" id="probation_eligibility" data-key="probation_eligibility" value="1">
                        </div>
                    </div>

                    <!-- Global maximum increment -->
                    <div class="setting-row">
                        <div class="setting-label">Global maximum increment percentage</div>
                        <p class="setting-help mb-3">Application-wide upper limit for an increment percentage.</p>
                        <div class="row g-2">
                            <div class="col-sm-4 col-md-3">
                                <div class="input-group">
                                    <input type="number" class="form-control setting-input" id="maximum_increment_percentage" data-key="maximum_increment_percentage" min="0" max="100" step="0.01" value="30">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reviewer weightage -->
                    <div class="setting-row d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <div class="setting-label">Use reviewer weightage</div>
                            <p class="setting-help">Apply the reviewer and self-review weightages configured in the Review Matrix. When disabled, all completed reviews contribute equally.</p>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input settings-toggle setting-input" type="checkbox" id="use_reviewer_weightage" data-key="use_reviewer_weightage" value="1" checked>
                        </div>
                    </div>

                    <!-- CTC range based increment rules -->
                    <div class="setting-row">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <div>
                                <div class="setting-label">CTC range-based increment rules</div>
                                <p class="setting-help">Define the maximum increment percentage applicable to different annual CTC ranges.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="editCtcRanges">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary d-none" id="addCtcRangeRow">
                                    <i class="bi bi-plus-lg me-1"></i>Add range
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm settings-table align-middle mb-2" id="ctcRangeTable">
                                <thead>
                                    <tr>
                                        <th style="width:35%">CTC from</th>
                                        <th style="width:35%">CTC to</th>
                                        <th style="width:20%">Maximum increment (%)</th>
                                        <th style="width:10%"></th>
                                    </tr>
                                </thead>
                                <tbody id="ctcRangeTableBody"></tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end gap-2 d-none" id="ctcRangeActions">
                            <button type="button" class="btn btn-sm btn-light border" id="cancelCtcRanges">Cancel</button>
                            <button type="button" class="btn btn-sm btn-primary" id="saveCtcRanges">Save changes</button>
                        </div>
                        <div class="form-text">Each CTC range defines the maximum increment percentage applicable to employees whose current CTC falls within that range. Ranges should not overlap.</div>
                    </div>

                    <!-- Exceptional approval -->
                    <div class="setting-row d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <div class="setting-label">Exceptional increment approval</div>
                            <p class="setting-help">Require an additional approval step for exceptional increments.</p>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input settings-toggle setting-input" type="checkbox" id="exceptional_increment_approval" data-key="exceptional_increment_approval" value="1">
                        </div>
                    </div>

                    <!-- Grace period -->
                    <div class="setting-row">
                        <div class="setting-label">Appraisal deadline grace period</div>
                        <p class="setting-help mb-3">Additional time allowed after the appraisal deadline before it is treated as overdue.</p>
                        <div class="row g-2">
                            <div class="col-sm-4 col-md-3">
                                <div class="input-group">
                                    <input type="number" class="form-control setting-input" id="appraisal_deadline_grace_period" data-key="appraisal_deadline_grace_period" min="0" max="365" value="0">
                                    <span class="input-group-text">days</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SAVE BAR -->
            <div class="settings-savebar">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="small text-muted" id="settingsMessage" role="status">Changes are not saved until you click Save settings.</div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light border" id="resetSettings">Reset form</button>
                        <button type="submit" class="btn btn-primary px-4" id="saveSettings"><i class="bi bi-check2 me-1"></i>Save settings</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('settingsForm');
        const saveButton = document.getElementById('saveSettings');
        const message = document.getElementById('settingsMessage');
        const ctcRangeTableBody = document.getElementById('ctcRangeTableBody');
        const getUrl = '<?= site_url('settings/get') ?>';
        const saveUrl = '<?= site_url('settings/save') ?>';
        const settingInputs = () => [...form.querySelectorAll('.setting-input')];
        let ctcRangesEditing = false;

        const defaultValues = {
            email_notifications: '1',
            appraisal_reminders: '1',
            reminder_frequency: '3',
            overdue_reminders: '1',
            overdue_reminder_frequency: '3',
            minimum_service_period: '6',
            probation_eligibility: '0',
            maximum_increment_percentage: '30',
            use_reviewer_weightage: '1',
            exceptional_increment_approval: '0',
            appraisal_deadline_grace_period: '0'
        };

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = String(value ?? '');
            return div.innerHTML;
        }

        function showMessage(text, error = false) {
            message.textContent = text;
            message.className = 'small ' + (error ? 'text-danger' : 'text-muted');
        }

        function setInputValue(input, value) {
            if (input.type === 'checkbox') {
                input.checked = ['1', 'true', 'on'].includes(String(value).toLowerCase());
            } else {
                input.value = value ?? '';
            }
        }

        function getInputValue(input) {
            return input.type === 'checkbox' ? (input.checked ? '1' : '0') : input.value.trim();
        }

        function addCtcRangeRow(ctcFrom = '', ctcTo = '', incrementPercentage = '') {
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>
                <div class="ctc-display">${ctcFrom !== '' ? '₹' + escapeHtml(ctcFrom) : '-'}</div>
                <div class="ctc-edit d-none">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₹</span>
                        <input type="number" class="form-control ctc-from" min="0" step="0.01" placeholder="0" value="${escapeHtml(ctcFrom)}">
                    </div>
                </div>
            </td>
            <td>
                <div class="ctc-display">${ctcTo !== '' ? '₹' + escapeHtml(ctcTo) : '-'}</div>
                <div class="ctc-edit d-none">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">₹</span>
                        <input type="number" class="form-control ctc-to" min="0" step="0.01" placeholder="500000" value="${escapeHtml(ctcTo)}">
                    </div>
                </div>
            </td>
            <td>
                <div class="ctc-display">${incrementPercentage !== '' ? escapeHtml(incrementPercentage) + '%' : '-'}</div>
                <div class="ctc-edit d-none">
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control increment-percentage" min="0" max="100" step="0.01" placeholder="0.00" value="${escapeHtml(incrementPercentage)}">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row ctc-edit d-none" aria-label="Remove CTC range">
                    <i class="bi bi-trash"></i>
                </button>
            </td>`;
            ctcRangeTableBody.appendChild(tr);

            if (ctcRangesEditing) {
                tr.querySelectorAll('.ctc-display').forEach(element => {
                    element.classList.add('d-none');
                });

                tr.querySelectorAll('.ctc-edit').forEach(element => {
                    element.classList.remove('d-none');
                });
            }
        }

        function setCtcEditMode(editing) {
            ctcRangesEditing = editing;

            document.querySelectorAll('.ctc-display').forEach(element => {
                element.classList.toggle('d-none', editing);
            });

            document.querySelectorAll('.ctc-edit').forEach(element => {
                element.classList.toggle('d-none', !editing);
            });

            document.getElementById('editCtcRanges').classList.toggle('d-none', editing);
            document.getElementById('addCtcRangeRow').classList.toggle('d-none', !editing);
            document.getElementById('ctcRangeActions').classList.toggle('d-none', !editing);
        }

        function refreshCtcDisplay() {
            [...ctcRangeTableBody.querySelectorAll('tr')].forEach(row => {
                const ctcFrom = row.querySelector('.ctc-from')?.value.trim() || '';
                const ctcTo = row.querySelector('.ctc-to')?.value.trim() || '';
                const percentage = row.querySelector('.increment-percentage')?.value.trim() || '';

                const displays = row.querySelectorAll('.ctc-display');

                displays[0].textContent = ctcFrom !== '' ? `₹${ctcFrom}` : '-';
                displays[1].textContent = ctcTo !== '' ? `₹${ctcTo}` : '-';
                displays[2].textContent = percentage !== '' ? `${percentage}%` : '-';
            });
        }

        function collectCtcRangeRules() {
            return [...ctcRangeTableBody.querySelectorAll('tr')].map(row => ({
                ctc_from: row.querySelector('.ctc-from').value.trim(),
                ctc_to: row.querySelector('.ctc-to').value.trim(),
                maximum_percentage: row.querySelector('.increment-percentage').value.trim()
            })).filter(rule => rule.ctc_from !== '' || rule.ctc_to !== '' || rule.maximum_percentage !== '');
        }

        function validateCtcRanges() {
            const rows = [...ctcRangeTableBody.querySelectorAll('tr')];
            const ranges = [];

            for (const row of rows) {
                const ctcFrom = row.querySelector('.ctc-from').value.trim();
                const ctcTo = row.querySelector('.ctc-to').value.trim();
                const percentage = row.querySelector('.increment-percentage').value.trim();

                if (ctcFrom === '' && ctcTo === '' && percentage === '') continue;

                if (ctcFrom === '' || ctcTo === '' || percentage === '') {
                    return 'Complete every CTC range before saving.';
                }

                const from = Number(ctcFrom);
                const to = Number(ctcTo);
                const increment = Number(percentage);

                if (!Number.isFinite(from) || !Number.isFinite(to) || !Number.isFinite(increment)) {
                    return 'Enter valid numeric values for every CTC range.';
                }

                if (from < 0) {
                    return 'CTC from cannot be negative.';
                }

                if (to <= from) {
                    return 'CTC to must be greater than CTC from.';
                }

                if (increment < 0 || increment > 100) {
                    return 'Maximum increment percentage must be between 0% and 100%.';
                }

                ranges.push({
                    from,
                    to
                });
            }

            ranges.sort((a, b) => a.from - b.from);

            for (let i = 1; i < ranges.length; i++) {
                if (ranges[i].from < ranges[i - 1].to) {
                    return 'CTC ranges must not overlap.';
                }
            }

            return null;
        }

        function resetToDefaults() {
            settingInputs().forEach(input => {
                setInputValue(input, defaultValues[input.dataset.key] ?? '');
            });
            ctcRangeTableBody.innerHTML = '';
            showMessage('Form reset. These changes have not been saved.');
        }
        document.getElementById('editCtcRanges').addEventListener('click', function() {
            setCtcEditMode(true);
        });

        document.getElementById('cancelCtcRanges').addEventListener('click', function() {
            loadSettings().catch(error => {
                showMessage(error.message || 'Unable to reload settings.', true);
            });
        });

        document.getElementById('saveCtcRanges').addEventListener('click', async function() {
            const ctcValidation = validateCtcRanges();

            if (ctcValidation) {
                showMessage(ctcValidation, true);
                return;
            }

            const settings = {};
            settingInputs().forEach(input => {
                settings[input.dataset.key] = getInputValue(input);
            });

            settings.ctc_range_increment_rules = JSON.stringify(collectCtcRangeRules());

            const button = this;
            button.disabled = true;

            try {
                const csrfInput = form.querySelector(`input[name="<?= csrf_token() ?>"]`);
                const headers = {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                };

                if (csrfInput) {
                    headers['<?= csrf_header() ?>'] = csrfInput.value;
                }

                const response = await fetch(saveUrl, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        scope_type: 'global',
                        scope_id: 0,
                        settings
                    })
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Unable to save CTC ranges.');
                }

                if (csrfInput && result.csrfHash) {
                    csrfInput.value = result.csrfHash;
                }

                refreshCtcDisplay();
                setCtcEditMode(false);
                showMessage('CTC range rules saved successfully.');
            } catch (error) {
                showMessage(error.message || 'Unable to save CTC ranges.', true);
            } finally {
                button.disabled = false;
            }
        });

        document.getElementById('addCtcRangeRow').addEventListener('click', function() {
            addCtcRangeRow();
        });

        ctcRangeTableBody.addEventListener('click', function(event) {
            const removeButton = event.target.closest('.remove-row');
            if (!removeButton) return;

            const row = removeButton.closest('tr');

            if (row) {
                row.remove();
            }
        });
        document.getElementById('resetSettings').addEventListener('click', resetToDefaults);

        async function loadSettings() {
            showMessage('Loading settings...');

            const response = await fetch(getUrl + '?scope_type=global&scope_id=0', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Unable to load settings.');
            }

            const data = result.settings || {};

            settingInputs().forEach(input => {
                const key = input.dataset.key;
                if (Object.prototype.hasOwnProperty.call(data, key)) {
                    setInputValue(input, data[key]);
                }
            });

            let ctcRangeRules = [];

            try {
                const storedRules = data.ctc_range_increment_rules;

                if (storedRules && typeof storedRules === 'string') {
                    ctcRangeRules = JSON.parse(storedRules);
                } else if (Array.isArray(storedRules)) {
                    ctcRangeRules = storedRules;
                }

                if (!Array.isArray(ctcRangeRules)) {
                    ctcRangeRules = [];
                }
            } catch (error) {
                ctcRangeRules = [];
            }

            ctcRangeTableBody.innerHTML = '';

            ctcRangeRules.forEach(rule => {
                addCtcRangeRow(
                    rule.ctc_from ?? '',
                    rule.ctc_to ?? '',
                    rule.maximum_percentage ?? ''
                );
            });

            setCtcEditMode(false);
            showMessage('Settings loaded.');
        }

        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            const ctcValidation = validateCtcRanges();

            if (ctcValidation) {
                showMessage(ctcValidation, true);
                return;
            }

            const settings = {};

            settingInputs().forEach(input => {
                settings[input.dataset.key] = getInputValue(input);
            });

            settings.ctc_range_increment_rules = JSON.stringify(collectCtcRangeRules());

            saveButton.disabled = true;
            showMessage('Saving settings...');

            try {
                const csrfInput = form.querySelector('input[name="<?= csrf_token() ?>"]');
                const headers = {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                };

                if (csrfInput) {
                    headers['<?= csrf_header() ?>'] = csrfInput.value;
                }

                const response = await fetch(saveUrl, {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({
                        scope_type: 'global',
                        scope_id: 0,
                        settings
                    })
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Unable to save settings.');
                }

                if (csrfInput && result.csrfHash) {
                    csrfInput.value = result.csrfHash;
                }

                showMessage('Settings saved successfully.');
            } catch (error) {
                showMessage(error.message || 'Unable to save settings.', true);
            } finally {
                saveButton.disabled = false;
            }
        });

        (async function initializeSettings() {
            try {
                await loadSettings();
            } catch (error) {
                showMessage(error.message || 'Unable to initialize settings.', true);
            }
        })();
    });
</script>

<?= $this->endSection() ?>