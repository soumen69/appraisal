<div class="row g-4">

    <input
        type="hidden"
        name="id"
        id="id">

    <div class="col-12">

        <label class="form-label">
            Module
            <span class="text-danger">*</span>
        </label>

        <select
            class="form-select"
            id="module_id"
            name="module_id">

            <option value="">
                Select Module
            </option>

        </select>

        <div class="invalid-feedback"></div>

    </div>

    <div class="col-12">

        <label class="form-label">
            Permission Name
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            class="form-control"
            id="name"
            name="name"
            placeholder="Approve Employee">

        <div class="invalid-feedback"></div>

    </div>

    <div class="col-12">

        <label class="form-label">
            Permission Slug
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            class="form-control"
            id="slug"
            name="slug"
            placeholder="employee.approve">

        <div class="invalid-feedback"></div>

        <small class="text-muted">
            Example :
            employee.approve
        </small>

    </div>

</div>