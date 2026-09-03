<?= $this->extend('layouts/master') ?>


<?= $this->section('content') ?>


<div class="container-fluid">


    <!-- Page Header -->

    <div
        class="d-flex
        align-items-center
        justify-content-between
        mb-4">

        <div>

            <div
                class="d-flex
                align-items-center
                gap-2
                mb-1">

                <a
                    href="<?= base_url('templates') ?>"
                    class="btn
                    btn-sm
                    btn-light
                    border">

                    <i
                        class="bi
                        bi-arrow-left">
                    </i>

                </a>


                <h4
                    class="mb-0">

                    Template Builder

                </h4>

            </div>


            <div
                class="text-muted">

                Configure sections and questions
                for this appraisal template.

            </div>

        </div>


        <button
            type="button"
            class="btn btn-primary"
            id="btnAddSection">

            <i
                class="bi
                bi-plus-lg
                me-1">
            </i>

            Add Section

        </button>

    </div>



    <!-- Template Information -->

    <div
        class="card
        border-0
        shadow-sm
        mb-4">

        <div
            class="card-body
            py-3">

            <div
                class="row
                align-items-center">

                <div
                    class="col-md-8">

                    <div
                        class="small
                        text-uppercase
                        text-muted
                        fw-semibold
                        mb-1">

                        Appraisal Template

                    </div>


                    <h5
                        class="mb-1"
                        id="templateName">

                        <?= esc(
                            $template['template_name']
                                ?? '-'
                        ) ?>

                    </h5>


                    <div
                        class="text-muted
                        small">

                        <?= esc(
                            $template['organization_name']
                                ?? '-'
                        ) ?>

                    </div>

                </div>


                <div
                    class="col-md-4
                    text-md-end
                    mt-3
                    mt-md-0">

                    <?php if (
                        ($template['is_default'] ?? 0) == 1
                    ): ?>

                        <span
                            class="badge
                            bg-primary-subtle
                            text-primary">

                            Default Template

                        </span>

                    <?php endif; ?>


                    <?php if (
                        ($template['status'] ?? '')
                        === 'active'
                    ): ?>

                        <span
                            class="badge
                            bg-success-subtle
                            text-success">

                            Active

                        </span>

                    <?php else: ?>

                        <span
                            class="badge
                            bg-secondary-subtle
                            text-secondary">

                            Inactive

                        </span>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>



    <!-- Builder -->

    <div
        id="templateBuilder">


        <!-- Loading State -->

        <div
            class="card
            border-0
            shadow-sm">

            <div
                class="card-body
                text-center
                py-5">

                <div
                    class="spinner-border
                    text-primary
                    mb-3">

                </div>


                <div
                    class="text-muted">

                    Loading template structure...

                </div>

            </div>

        </div>


    </div>


</div>



<!-- ============================================================
     SECTION MODAL
============================================================ -->

<div
    class="modal
    fade"
    id="sectionModal"
    tabindex="-1">

    <div
        class="modal-dialog">

        <div
            class="modal-content">


            <div
                class="modal-header">

                <h5
                    class="modal-title"
                    id="sectionModalTitle">

                    Add Section

                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">

                </button>

            </div>


            <form
                id="sectionForm">


                <div
                    class="modal-body">

                    <input
                        type="hidden"
                        id="sectionId">


                    <div
                        class="mb-3">

                        <label
                            class="form-label">

                            Section Name
                        </label>


                        <input
                            type="text"
                            class="form-control"
                            id="sectionName"
                            name="section_name"
                            maxlength="150"
                            autocomplete="off">


                        <div
                            class="invalid-feedback">

                        </div>

                    </div>

                </div>


                <div
                    class="modal-footer">

                    <button
                        type="button"
                        class="btn
                        btn-light"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>


                    <button
                        type="submit"
                        class="btn
                        btn-primary"
                        id="btnSaveSection">

                        Save Section

                    </button>

                </div>


            </form>

        </div>

    </div>

</div>



<!-- ============================================================
     QUESTION MODAL
============================================================ -->

<div
    class="modal
    fade"
    id="questionModal"
    tabindex="-1">

    <div
        class="modal-dialog
        modal-lg">

        <div
            class="modal-content">


            <div
                class="modal-header">

                <h5
                    class="modal-title"
                    id="questionModalTitle">

                    Add Question

                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">

                </button>

            </div>


            <form
                id="questionForm">


                <div
                    class="modal-body">

                    <input
                        type="hidden"
                        id="questionId">


                    <input
                        type="hidden"
                        id="questionSectionId">


                    <div
                        class="mb-3">

                        <label
                            class="form-label">

                            Question
                        </label>


                        <textarea
                            class="form-control"
                            id="questionText"
                            name="question"
                            rows="3"
                            autocomplete="off">

                        </textarea>


                        <div
                            class="invalid-feedback">

                        </div>

                    </div>


                    <div
                        class="row">


                        <div
                            class="col-md-7">

                            <div
                                class="mb-3">

                                <label
                                    class="form-label">

                                    Answer Type

                                </label>


                                <select
                                    class="form-select"
                                    id="answerType"
                                    name="answer_type">

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

                            </div>

                        </div>


                        <div
                            class="col-md-5">

                            <div
                                class="form-check
                                form-switch
                                mt-md-4
                                pt-md-2">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="isRequired"
                                    name="is_required"
                                    value="1"
                                    checked>


                                <label
                                    class="form-check-label"
                                    for="isRequired">

                                    Required Question

                                </label>

                            </div>

                        </div>


                    </div>

                </div>


                <div
                    class="modal-footer">

                    <button
                        type="button"
                        class="btn
                        btn-light"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>


                    <button
                        type="submit"
                        class="btn
                        btn-primary"
                        id="btnSaveQuestion">

                        Save Question

                    </button>

                </div>


            </form>

        </div>

    </div>

</div>



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