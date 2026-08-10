const CrudDrawer = {
    show(title, data, crud = null) {
        /*
        |--------------------------------------------------------------------------
        | Custom Drawer Renderer
        |--------------------------------------------------------------------------
        */

        if (
            crud &&
            typeof crud.drawerRenderer === 'function'
        ) {
            const html =
                crud.drawerRenderer(
                    data,
                    crud
                );

            $('#crudDrawerTitle')
                .text(title);

            $('#crudDrawerBody')
                .html(html);

            const drawerElement =
                document.getElementById('crudDrawer');

            const drawer =
                bootstrap.Offcanvas.getOrCreateInstance(
                    drawerElement
                );

            drawer.show();

            return;
        }


        const hidden = [
            'id',
            'group_id',
            'organization_id',
            'organization_ids',
            'created_at',
            'updated_at'
        ];

        let html = '';

        Object.keys(data).forEach(key => {
            if (hidden.includes(key)) {
                return;
            }

            let value = data[key];

            if (
                value === null ||
                value === undefined ||
                value === ''
            ) {
                value = '-';
            }

            switch (key) {
                case 'status':

                    if (value === 'active') {

                        value = `
            <span class="status-badge status-active">
                Active
            </span>
        `;

                    } else if (value === 'inactive') {

                        value = `
            <span class="status-badge status-inactive">
                Inactive
            </span>
        `;

                    } else {

                        value = `
            <span class="badge bg-warning text-dark">
                Mixed
            </span>
        `;
                    }

                    break;

                case 'icon':

                    value =
                        value !== '-'

                            ? `
                                <i class="${value} fs-4 me-2"></i>
                                ${value}
                            `

                            : '-';

                    break;

                case 'is_sidebar':

                    value =
                        value == 1

                            ? `
                                <span class="badge bg-success">
                                    Yes
                                </span>
                            `

                            : `
                                <span class="badge bg-secondary">
                                    No
                                </span>
                            `;

                    break;

                case 'is_visible':

                    value =
                        value == 1

                            ? `
                                <span class="badge bg-success">
                                    Visible
                                </span>
                            `

                            : `
                                <span class="badge bg-warning text-dark">
                                    Hidden
                                </span>
                            `;

                    break;

                case 'is_system':

                    value =
                        value == 1

                            ? `
                                <span class="badge bg-danger">
                                    System
                                </span>
                            `

                            : `
                                <span class="badge bg-primary">
                                    Custom
                                </span>
                            `;

                    break;
            }

            html += `
                <div class="drawer-row mb-3">

                    <div class="small text-muted text-uppercase fw-semibold mb-1">
                        ${formatLabel(key)}
                    </div>

                    <div>
                        ${value}
                    </div>

                </div>
            `;
        });

        $('#crudDrawerTitle')
            .text(title);

        $('#crudDrawerBody')
            .html(html);

        const drawerElement =
            document.getElementById('crudDrawer');

        const drawer =
            bootstrap.Offcanvas.getOrCreateInstance(
                drawerElement
            );

        drawer.show();
    }
};

function formatLabel(label) {
    return label
        .replace(/\_/g, ' ')
        .replace(/\b\w/g, c => c.toUpperCase());
}