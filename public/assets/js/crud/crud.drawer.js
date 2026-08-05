const CrudDrawer = {

    show(title, data) {
        let html = '';
        Object.keys(data).forEach(function (key) {
            html += `
                <div class="drawer-row">
                    <label>${key.replaceAll('_', ' ')}</label>
                    <div>${data[key] ?? '-'}</div>
                </div>
            `;
        });
        $('#crudDrawerTitle').text(title);
        $('#crudDrawerBody').html(html);
        new bootstrap.Offcanvas('#crudDrawer').show();
    }
};