<?= $this->extend('layouts/master') ?>


<?= $this->section('content') ?>


<div class="container-fluid">


    <!-- =========================================================
        PAGE HEADER
    ========================================================== -->

    <div
        class="d-flex
        align-items-center
        justify-content-between
        flex-wrap
        gap-3
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

                    <i class="bi bi-arrow-left"></i>

                </a>


                <h4
                    class="mb-0
                    fw-semibold">

                    Template Builder

                </h4>

            </div>


            <div
                class="text-muted
                small">

                Configure sections and appraisal questions.

            </div>

        </div>


        <div
            class="d-flex
            align-items-center
            gap-2">

            <button
                type="button"
                class="btn
                btn-primary"
                id="btnAddSection">

                <i
                    class="bi
                    bi-plus-lg
                    me-1">
                </i>

                Add Section

            </button>

        </div>

    </div>


    <!-- =========================================================
        TEMPLATE INFO
    ========================================================== -->

    <div
        class="card
        border-0
        shadow-sm
        mb-4">

        <div
            class="card-body">

            <div
                class="row
                align-items-center
                g-3">

                <div
                    class="col-md-6">

                    <div
                        class="small
                        text-muted
                        text-uppercase
                        fw-semibold
                        mb-1">

                        Appraisal Template

                    </div>


                    <h5
                        class="mb-1"
                        id="templateName">

                        Loading...

                    </h5>


                    <div
                        class="text-muted
                        small"
                        id="templateOrganization">

                        &nbsp;

                    </div>

                </div>


                <div
                    class="col-md-6">

                    <div
                        class="d-flex
                        justify-content-md-end
                        align-items-center
                        gap-2">

                        <span
                            class="badge
                            bg-primary-subtle
                            text-primary
                            px-3
                            py-2"
                            id="templateType">

                            -

                        </span>


                        <span
                            class="badge
                            bg-success-subtle
                            text-success
                            px-3
                            py-2"
                            id="templateStatus">

                            -

                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
        BUILDER
    ========================================================== -->

    <div
        id="builderContainer">


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
                    text-primary"
                    role="status">

                    <span class="visually-hidden">

                        Loading...

                    </span>

                </div>


                <div
                    class="text-muted
                    mt-3">

                    Loading template builder...

                </div>

            </div>

        </div>


    </div>


</div>


<?= view(
    'appraisal/templates/builder/section-modal'
) ?>


<?= view(
    'appraisal/templates/builder/question-modal'
) ?>


<?= $this->endSection() ?>


<?= $this->section('scripts') ?>


<script>
    const templateId =
        <?= (int) $templateId ?>;


    const builderBaseUrl =
        '<?= base_url('templates') ?>';
</script>


<script>
    $(function() {

        loadBuilderData();


        $('#btnAddSection').on(
            'click',
            function() {

                openSectionModal();

            }
        );

    });
</script>


<?= $this->endSection() ?>