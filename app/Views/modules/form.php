<div class="row g-4">

    <input
        type="hidden"
        name="id"
        id="id">

    <div class="col-md-6">

        <div class="form-floating">

    <input
        type="text"
        class="form-control"
        id="name"
        name="name"
        placeholder="Module Name">

    <label for="name">

        Module Name

    </label>

</div>

<div class="invalid-feedback"></div>
    </div>

    <div class="col-md-6">

        <label class="form-label">

            Slug
        </label>

        <input

            type="text"

            class="form-control"

            name="slug"

            id="slug">

        <div class="invalid-feedback"></div>

    </div>

    <div class="col-md-6">

        <label class="form-label">

            Route
        </label>

        <input

            type="text"

            class="form-control"

            name="route"

            id="route">

    </div>

    <div class="col-md-6">

        <label class="form-label">

            Icon
        </label>

        <input

            type="text"

            class="form-control"

            name="icon"

            id="icon"

            placeholder="bi bi-people">

    </div>

    <div class="col-12">

        <div class="form-floating">

    <textarea
        class="form-control"
        id="description"
        name="description"
        placeholder="Description"
        style="height:120px"></textarea>

    <label for="description">

        Description

    </label>

</div>

    </div>

    <div class="col-md-6">

        <label class="form-label">

            Sort Order
        </label>

        <input

            type="number"

            class="form-control"

            name="sort_order"

            value="1">

    </div>

    <div class="col-md-6">

        <label class="form-label">

            Status
        </label>

        <select

            class="form-select"

            name="status">

            <option value="active">

                Active

            </option>

            <option value="inactive">

                Inactive

            </option>

        </select>

    </div>

</div>