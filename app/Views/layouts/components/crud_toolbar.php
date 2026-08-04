<div class="crud-toolbar">

    <div class="crud-toolbar-left">

        <div class="crud-search">

            <i class="bi bi-search"></i>

            <input

                type="text"

                id="crudSearch"

                class="form-control"

                placeholder="Search...">

        </div>

        <select
            class="form-select"
            id="crudStatus">

            <option value="">All Status</option>

            <option value="active">Active</option>

            <option value="inactive">Inactive</option>

        </select>

        <select
            class="form-select"
            id="crudPageSize">

            <option value="10">10 Rows</option>

            <option value="25">25 Rows</option>

            <option value="50">50 Rows</option>

            <option value="100">100 Rows</option>

        </select>

    </div>

    <div class="crud-toolbar-right">

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

            Add New

        </button>

    </div>

</div>