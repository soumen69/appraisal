<div class="crud-card mb-4">

    <div class="card-body">

        <div class="row g-3 align-items-center">

            <div class="col-xl-6 col-lg-12">

                <div class="d-flex flex-wrap gap-2 align-items-center">

                    <div class="crud-search flex-grow-1">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            id="crudSearch"
                            class="form-control"
                            placeholder="Search modules...">

                    </div>

                    <select
                        class="form-select crud-filter"
                        id="crudStatus">

                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>

                    </select>

                    <select
                        class="form-select crud-filter"
                        id="crudPageSize">

                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>

                    </select>

                </div>

            </div>

            <div class="col-xl-6 col-lg-12">

                <div class="d-flex justify-content-xl-end justify-content-start gap-2">

                    <button
                        class="btn app-btn-light"
                        id="btnRefresh">

                        <i class="bi bi-arrow-clockwise"></i>

                    </button>

                    <button
                        class="btn app-btn-light"
                        id="btnExport">

                        <i class="bi bi-download"></i>

                        Export

                    </button>

                    <button
                        class="btn app-btn-primary"
                        id="btnAdd">

                        <i class="bi bi-plus-lg"></i>

                        New Module

                    </button>

                </div>

            </div>

        </div>

    </div>

</div>