const CrudDrawer = {

    show(title, data, crud = null) {

        const hidden = [
            'id',
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
                    value = value === 'active'
                        ? '<span class="status-badge status-active">Active</span>'
                        : '<span class="status-badge status-inactive">Inactive</span>';
                    break;
                case 'icon':
                    value = value !== '-'
                        ? `<i class="${value} fs-4 me-2"></i> ${value}`
                        : '-';
                    break;
                case 'is_sidebar':
                    value = value == 1
                        ? '<span class="badge bg-success">Yes</span>'
                        : '<span class="badge bg-secondary">No</span>';
                    break;
                case 'is_visible':
                    value = value == 1
                        ? '<span class="badge bg-success">Visible</span>'
                        : '<span class="badge bg-warning text-dark">Hidden</span>';
                    break;
                case 'is_system':
                    value = value == 1
                        ? '<span class="badge bg-danger">System</span>'
                        : '<span class="badge bg-primary">Custom</span>';
                    break;

                default: break;
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
        $('#crudDrawerTitle').text(title);
        $('#crudDrawerBody').html(html);
        new bootstrap.Offcanvas('#crudDrawer').show();
    }
};

function formatLabel(label) {
    return label
        .replace(/_/g, ' ')
        .replace(/\b\w/g, c => c.toUpperCase());
}