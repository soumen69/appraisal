<div
    class="modal fade"
    id="crudModal"
    tabindex="-1"
    aria-hidden="true">

    <div
        class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">


            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="crudModalTitle">

                        Create Appraisal Cycle

                    </h5>

                    <small class="text-muted">

                        Configure an appraisal period
                        for the organization.

                    </small>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>


            <form
                id="crudForm"
                novalidate>


                <div class="modal-body">


                    <?= csrf_field() ?>


                    <div class="row g-3">


                        <!-- Organization -->

                        <div class="col-md-12">

                            <label
                                for="organization_id"
                                class="form-label">

                                Organization

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <select
                                class="form-select"
                                id="organization_id"
                                name="organization_id"
                                required>

                                <option value="">
                                    Select Organization
                                </option>

                                <?php foreach (
                                    $organizations ?? []
                                    as $organization
                                ): ?>

                                    <option
                                        value="<?= esc(
                                                    $organization['id']
                                                ) ?>">

                                        <?= esc(
                                            $organization['name']
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>


                            <div class="invalid-feedback">
                            </div>

                        </div>


                        <!-- Cycle Name -->

                        <div class="col-md-8">

                            <label
                                for="cycle_name"
                                class="form-label">

                                Cycle Name

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                class="form-control"
                                id="cycle_name"
                                name="cycle_name"
                                placeholder="e.g. FY 2026-27 Annual Appraisal"
                                maxlength="150"
                                required>


                            <div class="invalid-feedback">
                            </div>

                        </div>


                        <!-- Cycle Code -->

                        <div class="col-md-4">

                            <label
                                for="cycle_code"
                                class="form-label">

                                Cycle Code

                            </label>


                            <input
                                type="text"
                                class="form-control"
                                id="cycle_code"
                                name="cycle_code"
                                placeholder="e.g. FY26-27"
                                maxlength="50">


                            <div class="invalid-feedback">
                            </div>

                        </div>


                        <!-- Start Date -->

                        <div class="col-md-6">

                            <label
                                for="start_date"
                                class="form-label">

                                Start Date

                                <span class="text-danger">
                                    *
                                </span>

                            </label>


                            <input
                                type="date"
                                class="form-control"
                                id="start_date"
                                name="start_date"
                                required>


                            <div class="invalid-feedback">
                            </div>

                        </div>


                        <!-- End Date -->

                        <div class="col-md-6">

                            <label
                                for="end_date"
                                class="form-label">
                                End Date
                                <span class="text-danger">
                                    *
                                </span>
                            </label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                            <div class="invalid-feedback">
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label for="status" class="form-label">
                                Status
                                <span class="text-danger">
                                    *
                                </span>
                            </label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="draft">
                                    Draft
                                </option>
                                <option value="active">
                                    Active
                                </option>
                                <option value="completed">
                                    Completed
                                </option>
                                <option value="closed">
                                    Closed
                                </option>
                            </select>
                            <div class="invalid-feedback">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <label for="description" class="form-label">
                                Description
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter appraisal cycle description...">
                                </textarea>
                            <div class="invalid-feedback">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn app-btn-primary" id="btnSave">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>