<div
    class="modal fade"
    id="sectionModal"
    tabindex="-1"
    aria-hidden="true">

    <div
        class="modal-dialog
        modal-dialog-centered">

        <div class="modal-content">


            <!-- =========================================================
                HEADER
            ========================================================== -->

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="sectionModalTitle">

                    Add Section

                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>


            <!-- =========================================================
                FORM
            ========================================================== -->

            <form
                id="sectionForm"
                novalidate>


                <div class="modal-body">


                    <input
                        type="hidden"
                        id="section_id"
                        name="section_id">


                    <div class="mb-3">

                        <label
                            for="section_name"
                            class="form-label">

                            Section Name

                            <span class="text-danger">

                                *

                            </span>

                        </label>


                        <input
                            type="text"
                            class="form-control"
                            id="section_name"
                            name="section_name"
                            placeholder="e.g. Performance & Results"
                            maxlength="150"
                            required>


                        <div
                            class="invalid-feedback"
                            id="section_name_error">

                        </div>

                    </div>


                </div>


                <!-- =========================================================
                    FOOTER
                ========================================================== -->

                <div
                    class="modal-footer">

                    <button
                        type="button"
                        class="btn
                        btn-light
                        border"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>


                    <button
                        type="submit"
                        class="btn
                        btn-primary"
                        id="btnSaveSection">

                        <span
                            class="btn-text">

                            Save Section

                        </span>


                        <span
                            class="spinner-border
                            spinner-border-sm
                            d-none"
                            role="status">

                        </span>

                    </button>

                </div>


            </form>

        </div>

    </div>

</div>