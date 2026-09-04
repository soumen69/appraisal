<?= $this->extend('layouts/master') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="<?= base_url('templates') ?>" class="btn btn-sm btn-light border">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0">Template Builder</h4>
                    <div class="text-muted small">Configure sections and questions for this appraisal template.</div>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light border btn-sm" id="btnExpandAll">
                <i class="bi bi-arrows-expand me-1"></i>
                Expand All
            </button>
            <button type="button" class="btn btn-light border btn-sm" id="btnCollapseAll">
                <i class="bi bi-arrows-collapse me-1"></i>
                Collapse All
            </button>
            <button type="button" class="btn btn-primary" id="btnAddSection">
                <i class="bi bi-plus-lg me-1"></i>
                Add Section
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3 px-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="small text-uppercase text-muted fw-semibold mb-1">Appraisal Template</div>
                    <h5 class="mb-1"><?= esc($template['template_name'] ?? '-') ?></h5>
                    <div class="text-muted small"><?= esc($template['organization_name'] ?? '-') ?></div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-layout-text-sidebar-reverse me-1"></i>
                        <span id="sectionCount">0</span> Sections
                    </span>
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-question-circle me-1"></i>
                        <span id="questionCount">0</span> Questions
                    </span>
                    <?php if (($template['is_default'] ?? 0) == 1): ?>
                        <span class="badge bg-primary-subtle text-primary">Default Template</span>
                    <?php endif; ?>
                    <?php if (($template['status'] ?? '') === 'active'): ?>
                        <span class="badge bg-success-subtle text-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="templateBuilder">
        <div class="text-center py-5 text-muted">
            <div class="spinner-border spinner-border-sm me-2"></div>
            Loading template...
        </div>
    </div>
</div>

<div class="modal fade" id="sectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h5 class="modal-title" id="sectionModalTitle">Add Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sectionForm">
                <div class="modal-body">
                    <input type="hidden" id="sectionId">
                    <div class="mb-0">
                        <label class="form-label">Section Name</label>
                        <input type="text" class="form-control" id="sectionName" name="section_name" maxlength="150" autocomplete="off">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveSection">Save Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="questionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-3">
                <h5 class="modal-title" id="questionModalTitle">Add Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="questionForm">
                <div class="modal-body">
                    <input type="hidden" id="questionId">
                    <input type="hidden" id="questionSectionId">
                    <div class="mb-3">
                        <label class="form-label">Question</label>
                        <textarea class="form-control" id="questionText" name="question" rows="3" autocomplete="off"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <label class="form-label">Answer Type</label>
                            <select class="form-select" id="answerType" name="answer_type">
                                <option value="rating">Rating</option>
                                <option value="text">Text</option>
                                <option value="number">Number</option>
                                <option value="yes_no">Yes / No</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <div class="form-check form-switch mt-md-4 pt-md-2">
                                <input class="form-check-input" type="checkbox" id="isRequired" name="is_required" value="1" checked>
                                <label class="form-check-label" for="isRequired">Required Question</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveQuestion">Save Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .builder-section {
        overflow: hidden;
        transition: box-shadow .2s ease;
    }

    .builder-section:hover {
        box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .06) !important;
    }

    .builder-section-header {
        min-height: 58px;
        cursor: pointer;
    }

    .section-drag-handle,
    .question-drag-handle {
        cursor: grab;
    }

    .section-drag-handle:active,
    .question-drag-handle:active {
        cursor: grabbing;
    }

    .builder-question {
        transition: background-color .15s ease;
    }

    .builder-question:hover {
        background: rgba(var(--bs-primary-rgb), .025);
    }

    .question-text {
        min-width: 0;
    }

    .question-title {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .section-collapse-icon {
        transition: transform .2s ease;
    }

    .builder-section.collapsed .section-collapse-icon {
        transform: rotate(-90deg);
    }

    .builder-section.collapsed .section-content {
        display: none;
    }

    .sortable-ghost {
        opacity: .4;
    }

    .sortable-chosen {
        background: rgba(var(--bs-primary-rgb), .04);
    }

    /* Add this to your existing <style> section */

    .builder-section,
    .builder-question {
        overflow: visible !important;
    }

    .question-list {
        overflow: visible !important;
    }

    .card,
    .card-body,
    .card-header {
        overflow: visible;
    }

    .dropdown-menu {
        z-index: 1080 !important;
    }

    @media (max-width: 767.98px) {
        .question-title {
            white-space: normal;
        }

        .builder-section-header {
            min-height: auto;
        }
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
    window.templateBuilderConfig = {
        templateId: <?= (int) $template['id'] ?>,
        baseUrl: '<?= base_url('templates') ?>'
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script src="<?= base_url('assets/js/appraisal/template-builder.js') . '?v=' . time() ?>"></script>

<?= $this->endSection() ?>