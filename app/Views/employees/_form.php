<form
    id="employeeForm"
    enctype="multipart/form-data"
    novalidate>

    <?= csrf_field() ?>


    <!-- Personal Information -->
    <div class="employee-form-card">

        <div class="employee-form-card-header">

            <div class="employee-section-icon">
                <i class="bi bi-person"></i>
            </div>

            <div>
                <h6>Personal Information</h6>
                <p>Basic employee information.</p>
            </div>

        </div>


        <div class="employee-form-card-body">

            <div class="row g-4">

                <!-- Profile Photo -->
                <div class="col-12">

                    <div class="employee-photo-field">

                        <div
                            class="employee-photo-preview"
                            id="employeePhotoPreview">
                            <i class="bi bi-person"></i>
                        </div>

                        <div>

                            <label
                                for="profile_photo"
                                class="employee-photo-label">
                                Profile Photo
                            </label>

                            <p class="employee-photo-help">
                                JPG, PNG or WEBP. Maximum 2MB.
                            </p>

                            <label
                                for="profile_photo"
                                class="btn employee-upload-btn">
                                <i class="bi bi-upload me-1"></i>
                                Choose Photo
                            </label>

                            <input
                                type="file"
                                id="profile_photo"
                                name="profile_photo"
                                class="d-none"
                                accept="image/jpeg,image/png,image/webp">

                            <div
                                class="invalid-feedback"
                                data-field-error="profile_photo"></div>

                        </div>

                    </div>

                </div>


                <div class="col-md-6">

                    <label class="employee-form-label">
                        First Name
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="first_name"
                        class="form-control employee-form-control"
                        maxlength="100"
                        autocomplete="given-name"
                        required>

                    <div
                        class="invalid-feedback"
                        data-field-error="first_name"></div>

                </div>


                <div class="col-md-6">

                    <label class="employee-form-label">
                        Last Name
                    </label>

                    <input
                        type="text"
                        name="last_name"
                        class="form-control employee-form-control"
                        maxlength="100"
                        autocomplete="family-name">

                    <div
                        class="invalid-feedback"
                        data-field-error="last_name"></div>

                </div>


                <div class="col-md-6">

                    <label class="employee-form-label">
                        Email Address
                        <span>*</span>
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control employee-form-control"
                        maxlength="150"
                        autocomplete="email"
                        required>

                    <div
                        class="invalid-feedback"
                        data-field-error="email"></div>

                </div>


                <div class="col-md-6">

                    <label class="employee-form-label">
                        Phone
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control employee-form-control"
                        maxlength="30"
                        autocomplete="tel">

                    <div
                        class="invalid-feedback"
                        data-field-error="phone"></div>

                </div>


                <div class="col-md-4">

                    <label class="employee-form-label">
                        Date of Birth
                    </label>

                    <div class="employee-date-input">

                        <i class="bi bi-calendar3"></i>

                        <input
                            type="text"
                            name="dob"
                            id="dob"
                            class="form-control employee-form-control"
                            placeholder="Select date"
                            autocomplete="off">

                    </div>

                    <div
                        class="invalid-feedback"
                        data-field-error="dob"></div>

                </div>


                <div class="col-md-4">

                    <label class="employee-form-label">
                        Gender
                    </label>

                    <select
                        name="gender"
                        class="form-select employee-form-control">
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>

                    <div
                        class="invalid-feedback"
                        data-field-error="gender"></div>

                </div>

            </div>

        </div>

    </div>


    <!-- Employment -->
    <div class="employee-form-card">

        <div class="employee-form-card-header">

            <div class="employee-section-icon">
                <i class="bi bi-briefcase"></i>
            </div>

            <div>
                <h6>Employment Information</h6>
                <p>Employee identification and organizational placement.</p>
            </div>

        </div>


        <div class="employee-form-card-body">

            <div class="row g-4">

                <div class="col-md-4">

                    <label class="employee-form-label">
                        Employee Code
                    </label>

                    <input
                        type="text"
                        name="employee_code"
                        class="form-control employee-form-control"
                        maxlength="50"
                        autocomplete="off">

                    <div
                        class="invalid-feedback"
                        data-field-error="employee_code"></div>

                </div>


                <div class="col-md-4">

                    <label class="employee-form-label">
                        Joining Date
                    </label>

                    <div class="employee-date-input">

                        <i class="bi bi-calendar3"></i>

                        <input
                            type="text"
                            name="joining_date"
                            id="joining_date"
                            class="form-control employee-form-control"
                            placeholder="Select date"
                            autocomplete="off">

                    </div>

                    <div
                        class="invalid-feedback"
                        data-field-error="joining_date"></div>

                </div>


                <div class="col-md-4">

                    <label class="employee-form-label">
                        Status
                        <span>*</span>
                    </label>

                    <select
                        name="status"
                        class="form-select employee-form-control">
                        <option value="active">
                            Active
                        </option>

                        <option value="inactive">
                            Inactive
                        </option>
                    </select>

                    <div
                        class="invalid-feedback"
                        data-field-error="status"></div>

                </div>


                <div class="col-md-6">

                    <label class="employee-form-label">
                        Organization
                        <span>*</span>
                    </label>

                    <select
                        name="organization_id"
                        id="organization_id"
                        class="form-select employee-select2"
                        required>
                        <option value="">
                            Select organization
                        </option>
                    </select>

                    <div
                        class="invalid-feedback"
                        data-field-error="organization_id"></div>

                </div>


                <div class="col-md-6">

                    <label class="employee-form-label">
                        Branch
                    </label>

                    <select
                        name="branch_id"
                        id="branch_id"
                        class="form-select employee-select2"
                        disabled>
                        <option value="">
                            Select branch
                        </option>
                    </select>

                    <div
                        class="invalid-feedback"
                        data-field-error="branch_id"></div>

                </div>


                <div class="col-md-6">

                    <label class="employee-form-label">
                        Department
                    </label>

                    <select
                        name="department_id"
                        id="department_id"
                        class="form-select employee-select2">
                        <option value="">
                            Select department
                        </option>
                    </select>

                    <div
                        class="invalid-feedback"
                        data-field-error="department_id"></div>

                </div>


                <div class="col-md-6">

                    <label class="employee-form-label">
                        Designation
                    </label>

                    <select
                        name="designation_id"
                        id="designation_id"
                        class="form-select employee-select2">
                        <option value="">
                            Select designation
                        </option>
                    </select>

                    <div
                        class="invalid-feedback"
                        data-field-error="designation_id"></div>

                </div>


                <div class="col-md-6">

                    <label class="employee-form-label">
                        Reporting Manager
                    </label>

                    <select
                        name="reporting_manager_id"
                        id="reporting_manager_id"
                        class="form-select employee-select2">
                        <option value="">
                            No reporting manager
                        </option>
                    </select>

                    <div
                        class="invalid-feedback"
                        data-field-error="reporting_manager_id"></div>

                </div>

            </div>

        </div>

    </div>


    <!-- Access -->
    <div class="employee-form-card">

        <div class="employee-form-card-header">

            <div class="employee-section-icon">
                <i class="bi bi-shield-lock"></i>
            </div>

            <div>
                <h6>System Access</h6>
                <p>Assign roles and configure account access.</p>
            </div>

        </div>


        <div class="employee-form-card-body">

            <div class="row g-4">

                <div class="col-md-8">

                    <label class="employee-form-label">
                        Roles
                        <span>*</span>
                    </label>

                    <select
                        name="role_ids[]"
                        id="role_ids"
                        class="form-select employee-select2"
                        multiple
                        required>
                    </select>

                    <div class="employee-form-help">
                        Select one or more roles for this employee.
                    </div>

                    <div
                        class="invalid-feedback"
                        data-field-error="role_ids">
                    </div>

                </div>


                <!-- <div class="col-md-4">

                    <label class="employee-form-label">
                        Primary Role
                        <span>*</span>
                    </label>

                    <select
                        name="primary_role_id"
                        id="primary_role_id"
                        class="form-select employee-select2"
                        required
                        disabled>
                        <option value="">
                            Select primary role
                        </option>
                    </select>

                    <div class="employee-form-help">
                        Used as the employee's primary role.
                    </div>

                    <div
                        class="invalid-feedback"
                        data-field-error="primary_role_id"></div>

                </div> -->


                <div class="col-md-6">

                    <label class="employee-form-label">
                        Initial Password
                        <span>*</span>
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control employee-form-control"
                        minlength="8"
                        autocomplete="new-password"
                        required>

                    <div class="employee-form-help">
                        Minimum 8 characters.
                    </div>

                    <div
                        class="invalid-feedback"
                        data-field-error="password"></div>

                </div>


                <div class="col-md-6">

                    <label class="employee-form-label">
                        Confirm Password
                        <span>*</span>
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control employee-form-control"
                        minlength="8"
                        autocomplete="new-password"
                        required>

                    <div
                        class="invalid-feedback"
                        data-field-error="password_confirmation"></div>

                </div>

            </div>

        </div>

    </div>


    <!-- Actions -->
    <div class="employee-form-actions">

        <a
            href="<?= base_url('employees') ?>"
            class="btn employee-cancel-btn">
            Cancel
        </a>

        <button
            type="submit"
            class="btn app-btn-primary"
            id="employeeSaveBtn">
            <i class="bi bi-check2 me-1"></i>
            <?= isset($employee) ? 'Update Employee' : 'Create Employee' ?>
        </button>

    </div>

</form>