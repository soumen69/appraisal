<div
    class="modal fade"
    id="questionModal"
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
                    id="questionModalTitle">

                    Add Question

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
                id="questionForm"
                novalidate>


                <div class="modal-body">


                    <input
                        type="hidden"
                        id="question_id"
                        name="question_id">


                    <input
                        type="hidden"
                        id="question_section_id"
                        name="section_id">


                    <!-- Question -->

                    <div class="mb-3">

                        <label
                            for="question"
                            class="form-label">

                            Question

                            <span class="text-danger">

                                *

                            </span>

                        </label>


                        <textarea
                            class="form-control"
                            id="question"
                            name="question"
                            rows="3"
                            placeholder="Enter appraisal question"
                            required>
                        </textarea>


                        <div
                            class="invalid-feedback"
                            id="question_error">

                        </div>

                    </div>


                    <!-- Answer Type -->

                    <div class="mb-3">

                        <label
                            for="answer_type"
                            class="form-label">

                            Answer Type

                            <span class="text-danger">

                                *

                            </span>

                        </label>


                        <select
                            class="form-select"
                            id="answer_type"
                            name="answer_type"
                            required>

                            <option
                                value="rating">

                                Rating

                            </option>


                            <option
                                value="text">

                                Text

                            </option>


                            <option
                                value="number">

                                Number

                            </option>


                            <option
                                value="yes_no">

                                Yes / No

                            </option>

                        </select>


                        <div
                            class="invalid-feedback"
                            id="answer_type_error">

                        </div>

                    </div>


                    <!-- Required -->

                    <div
                        class="form-check
                        form-switch">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            id="is_required"
                            name="is_required"
                            value="1"
                            checked>


                        <label
                            class="form-check-label"
                            for="is_required">

                            Required question

                        </label>

                    </div>


                </div>


                <!-- =========================================================
                    FOOTER
                ========================================================== -->

                <div class="modal-footer">


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
                        id="btnSaveQuestion">


                        <span
                            class="btn-text">

                            Save Question

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