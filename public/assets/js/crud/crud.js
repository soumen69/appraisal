class Crud {

    constructor(options = {}) {
        this.endpoint = options.endpoint;
        this.table = document.querySelector(options.table);
        this.body = document.querySelector('#crudBody');
        this.header = document.querySelector('#crudHeader');
        this.pagination = document.querySelector('#crudPagination');
        this.modal = document.querySelector(options.modal);
        this.form = document.querySelector(options.form);
        this.columns = options.columns || [];
        this.onInit = options.onInit || null;
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
        this.entity = options.entity || 'Record';
        this.entityPlural = options.entityPlural || `${this.entity}s`;
        this.init();
    }

    init() {
        this.renderHeader();
        this.registerEvents();
        if (typeof this.onInit === 'function') {
            this.onInit(this);
        }
        this.load();
    }

    registerEvents() {
        CrudSearch.bind(this);
        CrudModal.bind(this);
        CrudForm.bind(this);
        CrudDelete.bind(this);
        CrudView.bind(this);
    }

    load() {
        CrudApi.list(this);
    }

    reload() {
        this.load();
    }

    renderHeader() {
        let html = '<tr>';
        html += '<th width="40"><input type="checkbox" id="checkAll"></th>';
        this.columns.forEach(col => {
            html += `<th>${col.label}</th>`;
        });
        html += '<th width="90">Action</th>';
        html += '</tr>';
        this.header.innerHTML = html;
    }
}