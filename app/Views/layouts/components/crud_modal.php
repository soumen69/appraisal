<div
    class="modal fade"
    id="crudModal"
    tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 id="crudModalTitle">

                    Add

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

                    <button
                        type="button"
                        class="btn app-btn-light"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        class="btn app-btn-primary"
                        id="btnSave">

                        Save

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>