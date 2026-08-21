
// class Crud {

//     constructor(options = {}) {

//         this.endpoint =
//             options.endpoint;

//         this.table =
//             document.querySelector(
//                 options.table
//             );

//         this.body =
//             document.querySelector(
//                 '#crudBody'
//             );

//         this.header =
//             document.querySelector(
//                 '#crudHeader'
//             );

//         this.pagination =
//             document.querySelector(
//                 '#crudPagination'
//             );

//         this.modal =
//             options.modal
//                 ? document.querySelector(
//                     options.modal
//                 )
//                 : null;

//         this.form =
//             options.form
//                 ? document.querySelector(
//                     options.form
//                 )
//                 : null;

//         this.columns =
//             options.columns || [];

//         this.actionRenderer =
//             options.actionRenderer || null;

//         this.onInit =
//             options.onInit || null;

//         this.drawerRenderer =
//             options.drawerRenderer || null;

//         this.page = 1;

//         this.pageSize = 10;

//         this.search = '';

//         this.status = '';

//         this.orderBy = 'id';

//         this.direction = 'desc';

//         this.total = 0;

//         this.data = [];

//         this.editId = null;

//         this.lastPage = 1;

//         this.entity =
//             options.entity || 'Record';

//         this.entityPlural =
//             options.entityPlural ||
//             `${this.entity}s`;

//         this.viewEndpoint =
//             options.viewEndpoint || null;

//         this.init();
//     }


//     init() {

//         this.renderHeader();

//         this.registerEvents();

//         if (
//             typeof this.onInit ===
//             'function'
//         ) {
//             this.onInit(this);
//         }

//         this.load();
//     }


//     registerEvents() {

//         CrudSearch.bind(this);

//         CrudModal.bind(this);

//         CrudDelete.bind(this);

//         CrudView.bind(this);

//         CrudForm.bind(this);
//     }


//     load() {

//         CrudApi.list(this);
//     }


//     reload() {

//         this.load();
//     }


//     renderHeader() {

//         let html = '<tr>';

//         html += `
//             <th width="40">
//                 <input
//                     type="checkbox"
//                     id="checkAll">
//             </th>
//         `;

//         this.columns.forEach(
//             col => {

//                 html += `
//                     <th>
//                         ${col.label}
//                     </th>
//                 `;
//             }
//         );

//         html += `
//             <th width="90">
//                 Action
//             </th>
//         `;

//         html += '</tr>';

//         if (this.header) {
//             this.header.innerHTML = html;
//         }
//     }
// }

class Crud {

    constructor(options = {}) {

        this.endpoint =
            options.endpoint;

        this.table =
            document.querySelector(
                options.table
            );

        this.body =
            document.querySelector(
                '#crudBody'
            );

        this.header =
            document.querySelector(
                '#crudHeader'
            );

        this.pagination =
            document.querySelector(
                '#crudPagination'
            );

        this.modal =
            options.modal
                ? document.querySelector(options.modal)
                : null;

        this.form =
            options.form
                ? document.querySelector(options.form)
                : null;

        this.columns =
            options.columns || [];

        this.actionRenderer =
            options.actionRenderer || null;

        this.onInit =
            options.onInit || null;

        this.drawerRenderer =
            options.drawerRenderer || null;

        this.page = 1;
        this.pageSize = 10;
        this.search = '';
        this.status = '';
        this.orderBy = 'id';
        this.direction = 'desc';

        this.total = 0;
        this.data = [];
        this.editId = null;
        this.lastPage = 1;

        this.entity =
            options.entity || 'Record';

        this.entityPlural =
            options.entityPlural ||
            `${this.entity}s`;

        this.viewEndpoint =
            options.viewEndpoint || null;

        /*
        |--------------------------------------------------------------------------
        | Permission resource
        |--------------------------------------------------------------------------
        |
        | Explicit resource is preferred.
        |
        | Example:
        | permissionResource: 'employee'
        |
        */
        this.permissionResource =
            options.permissionResource ||
            this.resolvePermissionResource(
                this.entity
            );

        this.permissions = {
            view:
                options.permissions?.view ||
                `${this.permissionResource}.view`,

            create:
                options.permissions?.create ||
                `${this.permissionResource}.create`,

            edit:
                options.permissions?.edit ||
                `${this.permissionResource}.edit`,

            delete:
                options.permissions?.delete ||
                `${this.permissionResource}.delete`,

            manage:
                options.permissions?.manage ||
                `${this.permissionResource}.permission`
        };

        this.init();
    }


    resolvePermissionResource(entity) {

        const map = {
            'Module': 'module',
            'Menu': 'menu',
            'Permission': 'permission',
            'Role': 'role',
            'Employee': 'employee',
            'Branch': 'branch',
            'Organization': 'organization',
            'Department': 'department',
            'Designation': 'designation'
        };

        return map[entity] ||
            String(entity || 'record')
                .trim()
                .toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/s$/, '');
    }


    can(action) {
        const permission =
            this.permissions?.[action];

        if (!permission) {
            return false;
        }

        if (typeof APP.can !== 'function') {
            console.warn(`Permission checker is unavailable for "${permission}".`);
            return false;
        }
        return APP.can(permission);
    }


    init() {

        this.renderHeader();

        this.registerEvents();

        this.applyCreatePermission();

        if (
            typeof this.onInit ===
            'function'
        ) {
            this.onInit(this);
        }

        this.load();
    }


    applyCreatePermission() {

        const $button = $('#btnAdd');

        if (!$button.length) {
            return;
        }

        const allowed =
            this.can('create');

        $button
            .toggle(allowed)
            .prop('disabled', !allowed)
            .attr(
                'aria-hidden',
                allowed ? 'false' : 'true'
            );
    }


    registerEvents() {

        CrudSearch.bind(this);
        CrudModal.bind(this);
        CrudDelete.bind(this);
        CrudView.bind(this);
        CrudForm.bind(this);
    }


    load() {
        CrudApi.list(this);
    }


    reload() {
        this.applyCreatePermission();
        this.load();
    }


    renderHeader() {

        let html = '<tr>';

        html += `
            <th width="40">
                <input
                    type="checkbox"
                    id="checkAll">
            </th>
        `;

        this.columns.forEach(
            col => {

                html += `
                    <th>
                        ${col.label}
                    </th>
                `;
            }
        );

        html += `
            <th width="90">
                Action
            </th>
        `;

        html += '</tr>';

        if (this.header) {
            this.header.innerHTML = html;
        }
    }
}