// const CrudTable = {

//     render(crud) {

//         if (!crud.data.length) {

//             const entity =
//                 crud.entity || "Record";

//             crud.body.innerHTML = `
//                 <tr>
//                     <td
//                         colspan="${crud.columns.length + 2}"
//                         class="text-center py-5">

//                         <i class="bi bi-database display-5 text-secondary"></i>

//                         <h5 class="mt-3">
//                             No ${entity}s Found
//                         </h5>

//                         <p class="text-muted mb-3">
//                             No ${entity.toLowerCase()} records are available.
//                         </p>

//                         <button
//                             class="btn app-btn-primary"
//                             id="btnEmptyCreate">

//                             <i class="bi bi-plus-lg"></i>
//                             Create ${entity}

//                         </button>

//                     </td>
//                 </tr>
//             `;

//             $(document)
//                 .off(
//                     "click",
//                     "#btnEmptyCreate"
//                 )
//                 .on(
//                     "click",
//                     "#btnEmptyCreate",
//                     function () {

//                         $("#btnAdd")
//                             .trigger("click");
//                     }
//                 );

//             return;
//         }


//         let html = "";


//         crud.data.forEach((row) => {

//             const rowId =
//                 row.id ??
//                 row.group_id ??
//                 null;


//             html += "<tr>";


//             html += `
//                 <td>
//                     <input
//                         type="checkbox"
//                         value="${rowId}">
//                 </td>
//             `;


//             crud.columns.forEach((col) => {

//                 let value =
//                     row[col.key] ?? "";


//                 if (
//                     typeof col.render === "function"
//                 ) {

//                     value =
//                         col.render(
//                             value,
//                             row
//                         );

//                 } else {

//                     switch (col.key) {

//                         case "status":

//                             if (
//                                 row.status === "active"
//                             ) {

//                                 value = `
//                                     <span class="status-badge status-active">
//                                         Active
//                                     </span>
//                                 `;

//                             } else if (
//                                 row.status === "inactive"
//                             ) {

//                                 value = `
//                                     <span class="status-badge status-inactive">
//                                         Inactive
//                                     </span>
//                                 `;

//                             } else {

//                                 value = `
//                                     <span class="badge bg-warning-subtle text-warning">
//                                         Mixed
//                                     </span>
//                                 `;
//                             }

//                             break;


//                         case "is_sidebar":

//                             value =
//                                 row.is_sidebar == 1

//                                     ? `
//                                         <span class="badge bg-success-subtle text-success">
//                                             Yes
//                                         </span>
//                                     `

//                                     : `
//                                         <span class="badge bg-secondary-subtle text-secondary">
//                                             No
//                                         </span>
//                                     `;

//                             break;


//                         case "is_visible":

//                             value =
//                                 row.is_visible == 1

//                                     ? `
//                                         <span class="badge bg-success-subtle text-success">
//                                             Visible
//                                         </span>
//                                     `

//                                     : `
//                                         <span class="badge bg-warning-subtle text-warning">
//                                             Hidden
//                                         </span>
//                                     `;

//                             break;


//                         case "icon":

//                             value =
//                                 value
//                                     ? `<i class="${value} fs-5"></i>`
//                                     : "-";

//                             break;


//                         default:

//                             value =
//                                 value || "-";
//                     }
//                 }


//                 html += `
//                     <td>
//                         ${value}
//                     </td>
//                 `;
//             });


//             /*
//              * Actions
//              */
//             html += `
//                 <td>

//                     <div class="dropdown">

//                         <button
//                             type="button"
//                             class="action-btn"
//                             aria-expanded="false">

//                             <i class="bi bi-three-dots"></i>

//                         </button>


//                         <ul class="dropdown-menu dropdown-menu-end">

//                             <li>

//                                 <a
//                                     class="dropdown-item btn-view"
//                                     href="#"
//                                     data-id="${rowId}">

//                                     <i class="bi bi-eye me-2"></i>
//                                     View

//                                 </a>

//                             </li>


//                             <li>

//                                 <a
//                                     class="dropdown-item btn-edit"
//                                     href="#"
//                                     data-id="${rowId}">

//                                     <i class="bi bi-pencil me-2"></i>
//                                     Edit

//                                 </a>

//                             </li>
//             `;


//             if (
//                 crud.entity === "Role"
//             ) {

//                 html += `
//                             <li>

//                                 <a
//                                     class="dropdown-item"
//                                     href="roles/permissions/${rowId}">

//                                     <i class="bi bi-shield-lock me-2"></i>
//                                     Manage Permissions

//                                 </a>

//                             </li>
//                 `;
//             }


//             html += `
//                             <li>

//                                 <hr class="dropdown-divider">

//                             </li>


//                             <li>

//                                 <a
//                                     class="dropdown-item text-danger btn-delete"
//                                     href="#"
//                                     data-id="${rowId}">

//                                     <i class="bi bi-trash me-2"></i>
//                                     Delete

//                                 </a>

//                             </li>

//                         </ul>

//                     </div>

//                 </td>
//             `;


//             html += "</tr>";
//         });


//         crud.body.innerHTML = html;


//         /*
//          * Initialize CRUD action dropdowns.
//          */
//         CrudTable.bindDropdowns();
//     },

//     bindDropdowns() {

//         /*
//          * Open / toggle action dropdown.
//          */
//         $('#crudBody .action-btn')
//             .off('click.crudDropdown')
//             .on('click.crudDropdown', function (e) {

//                 e.preventDefault();
//                 e.stopPropagation();

//                 const button = this;

//                 const dropdown =
//                     bootstrap.Dropdown
//                         .getOrCreateInstance(
//                             button,
//                             {
//                                 display: 'static'
//                             }
//                         );

//                 dropdown.toggle();
//             });


//         /*
//          * Close dropdown when clicking
//          * any action inside it.
//          */
//         $('#crudBody .dropdown-item')
//             .off('click.crudDropdownAction')
//             .on('click.crudDropdownAction', function () {

//                 const button =
//                     $(this)
//                         .closest('.dropdown')
//                         .find('.action-btn')[0];

//                 if (!button) {
//                     return;
//                 }

//                 const dropdown =
//                     bootstrap.Dropdown
//                         .getInstance(button);

//                 if (dropdown) {
//                     dropdown.hide();
//                 }
//             });


//         /*
//          * Close dropdown when clicking outside.
//          */
//         $(document)
//             .off('click.crudDropdown')
//             .on('click.crudDropdown', function (e) {

//                 if (
//                     $(e.target)
//                         .closest('#crudBody .dropdown')
//                         .length
//                 ) {
//                     return;
//                 }

//                 $('#crudBody .action-btn')
//                     .each(function () {

//                         const dropdown =
//                             bootstrap.Dropdown
//                                 .getInstance(this);

//                         if (dropdown) {
//                             dropdown.hide();
//                         }
//                     });
//             });
//     }
// };

const CrudTable = {

    render(crud) {

        if (!crud.data.length) {

            const entity =
                crud.entity || 'Record';

            crud.body.innerHTML = `
                <tr>
                    <td
                        colspan="${crud.columns.length + 2}"
                        class="text-center py-5">

                        <i class="bi bi-database display-5 text-secondary"></i>

                        <h5 class="mt-3">
                            No ${crud.entityPlural || entity + 's'} Found
                        </h5>

                        <p class="text-muted mb-3">
                            No ${entity.toLowerCase()} records are available.
                        </p>

                        <button
                            type="button"
                            class="btn app-btn-primary"
                            id="btnEmptyCreate">

                            <i class="bi bi-plus-lg me-1"></i>

                            Create ${entity}

                        </button>

                    </td>
                </tr>
            `;

            $(document)
                .off(
                    'click.crudEmpty',
                    '#btnEmptyCreate'
                )
                .on(
                    'click.crudEmpty',
                    '#btnEmptyCreate',
                    function () {

                        $('#btnAdd')
                            .trigger('click');

                    }
                );

            return;
        }


        let html = '';


        crud.data.forEach((row) => {

            const rowId =
                row.id ??
                row.group_id ??
                null;


            html += '<tr>';


            /*
             * Checkbox
             */
            html += `
                <td>
                    <input
                        type="checkbox"
                        class="form-check-input crud-row-check"
                        value="${rowId}">
                </td>
            `;


            /*
             * Columns
             */
            crud.columns.forEach((col) => {

                let value =
                    row[col.key] ?? '';


                if (
                    typeof col.render ===
                    'function'
                ) {

                    value =
                        col.render(
                            value,
                            row
                        );

                } else {

                    switch (col.key) {

                        case 'status':

                            if (
                                row.status ===
                                'active'
                            ) {

                                value = `
                                    <span
                                        class="status-badge status-active">
                                        Active
                                    </span>
                                `;

                            } else if (
                                row.status ===
                                'inactive'
                            ) {

                                value = `
                                    <span
                                        class="status-badge status-inactive">
                                        Inactive
                                    </span>
                                `;

                            } else {

                                value = `
                                    <span
                                        class="badge bg-warning-subtle text-warning">
                                        Mixed
                                    </span>
                                `;
                            }

                            break;


                        case 'is_sidebar':

                            value =
                                row.is_sidebar == 1

                                    ? `
                                        <span
                                            class="badge bg-success-subtle text-success">
                                            Yes
                                        </span>
                                    `

                                    : `
                                        <span
                                            class="badge bg-secondary-subtle text-secondary">
                                            No
                                        </span>
                                    `;

                            break;


                        case 'is_visible':

                            value =
                                row.is_visible == 1

                                    ? `
                                        <span
                                            class="badge bg-success-subtle text-success">
                                            Visible
                                        </span>
                                    `

                                    : `
                                        <span
                                            class="badge bg-warning-subtle text-warning">
                                            Hidden
                                        </span>
                                    `;

                            break;


                        case 'icon':

                            value =
                                value

                                    ? `
                                        <i
                                            class="${value} fs-5">
                                        </i>
                                    `

                                    : '-';

                            break;


                        default:

                            value =
                                value || '-';
                    }
                }


                html += `
                    <td>
                        ${value}
                    </td>
                `;
            });


            /*
 * ---------------------------------------------------------
 * Actions
 * ---------------------------------------------------------
 */

            if (
                typeof crud.actionRenderer ===
                'function'
            ) {

                html += `
        <td>

            <div class="dropdown">

                <button
                    type="button"
                    class="action-btn"
                    aria-expanded="false">

                    <i class="bi bi-three-dots"></i>

                </button>


                <ul class="dropdown-menu dropdown-menu-end">

                    ${crud.actionRenderer(
                    row,
                    rowId,
                    crud
                )}

                </ul>

            </div>

        </td>
    `;

            } else {

                /*
                 * -----------------------------------------------------
                 * Default CRUD actions
                 *
                 * Used by modal-based CRUD modules.
                 * -----------------------------------------------------
                 */

                html += `
        <td>

            <div class="dropdown">

                <button
                    type="button"
                    class="action-btn"
                    aria-expanded="false">

                    <i class="bi bi-three-dots"></i>

                </button>


                <ul class="dropdown-menu dropdown-menu-end">

                    <li>

                        <a
                            class="dropdown-item btn-view"
                            href="#"
                            data-id="${rowId}">

                            <i class="bi bi-eye me-2"></i>
                            View

                        </a>

                    </li>


                    <li>

                        <a
                            class="dropdown-item btn-edit"
                            href="#"
                            data-id="${rowId}">

                            <i class="bi bi-pencil me-2"></i>
                            Edit

                        </a>

                    </li>


                    ${crud.entity === 'Role'
                        ? `
                                <li>

                                    <a
                                        class="dropdown-item"
                                        href="roles/permissions/${rowId}">

                                        <i class="bi bi-shield-lock me-2"></i>
                                        Manage Permissions

                                    </a>

                                </li>
                              `
                        : ''
                    }


                    <li>

                        <hr class="dropdown-divider">

                    </li>


                    <li>

                        <a
                            class="dropdown-item text-danger btn-delete"
                            href="#"
                            data-id="${rowId}">

                            <i class="bi bi-trash me-2"></i>
                            Delete

                        </a>

                    </li>

                </ul>

            </div>

        </td>
    `;
            }


            html += '</tr>';
        });


        crud.body.innerHTML = html;


        this.bindDropdowns();
    },


    bindDropdowns() {

        /*
         * Open / toggle action dropdown.
         */
        $('#crudBody .action-btn')
            .off('click.crudDropdown')
            .on(
                'click.crudDropdown',
                function (e) {

                    e.preventDefault();
                    e.stopPropagation();

                    const button = this;

                    const dropdown =
                        bootstrap.Dropdown
                            .getOrCreateInstance(
                                button,
                                {
                                    display: 'static'
                                }
                            );

                    dropdown.toggle();
                }
            );


        /*
         * Close dropdown after action.
         */
        $('#crudBody .dropdown-item')
            .off('click.crudDropdownAction')
            .on(
                'click.crudDropdownAction',
                function () {

                    const button =
                        $(this)
                            .closest('.dropdown')
                            .find('.action-btn')[0];

                    if (!button) {
                        return;
                    }

                    const dropdown =
                        bootstrap.Dropdown
                            .getInstance(button);

                    if (dropdown) {
                        dropdown.hide();
                    }
                }
            );


        /*
         * Close dropdown when clicking outside.
         */
        $(document)
            .off('click.crudDropdown')
            .on(
                'click.crudDropdown',
                function (e) {

                    if (
                        $(e.target)
                            .closest(
                                '#crudBody .dropdown'
                            )
                            .length
                    ) {
                        return;
                    }

                    $('#crudBody .action-btn')
                        .each(function () {

                            const dropdown =
                                bootstrap.Dropdown
                                    .getInstance(
                                        this
                                    );

                            if (dropdown) {
                                dropdown.hide();
                            }

                        });
                }
            );
    }
};