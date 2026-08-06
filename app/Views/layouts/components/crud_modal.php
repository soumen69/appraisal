<div class="modal fade" id="crudModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="fw-bold" id="crudModalTitle">
                    <?= esc($modalTitle ?? ('Create ' . ($entity ?? 'Record'))) ?>
                </h5>
                <button
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>
            <form id="crudForm">
                <div class="modal-body">
                    <?= $form ?? '' ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn app-btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn app-btn-primary px-4" id="btnSave">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>