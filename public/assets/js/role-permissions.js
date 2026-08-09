const PermissionWorkspace = {
  roleId: window.roleId,

  modules: {},

  assigned: [],

  currentModule: null,

  dirty: false,

  init() {
    this.load();

    this.bind();
  },

  load() {
    $.get(

      APP.baseUrl + '/roles/permissions-data/' + this.roleId,

      (response) => {
        if (!response.success) {
          APP.error(response.message);

          return;
        }

        this.modules = response.data.permissions;

        this.assigned = response.data.assigned.map(Number);

        this.renderSidebar();

        const first = Object.keys(this.modules)[0];

        if (first) {
          this.showModule(first);
        }
      }

    );
  },

  bind() {
    const self = this;

    $(document).on(

      'click',

      '.permission-module',

      function () {
        $('.permission-module').removeClass('active');

        $(this).addClass('active');

        self.showModule(

          $(this).data('module')

        );
      }

    );
    $(document).on(
      'change',
      '.permission-checkbox',
      () => {
        this.refreshModuleCheckbox();

        this.setDirty(true);
      }
    );
    $(document).on(
      'change',
      '#moduleSelectAll',
      function () {
        $('.permission-checkbox')
          .prop(
            'checked',
            $(this).prop('checked')
          );

        PermissionWorkspace.setDirty(true);
      }
    );
    $('#moduleSearch').on(
      'keyup',
      function () {
        const keyword = $(this)
          .val()
          .toLowerCase();

        $('.permission-module').each(function () {
          $(this).toggle(

            $(this)
              .text()
              .toLowerCase()
              .includes(keyword)

          );
        });
      }
    );
    $('#permissionSearch').on(
      'keyup',
      function () {
        const keyword = $(this)
          .val()
          .toLowerCase();

        $('.permission-item').each(function () {
          $(this).toggle(

            $(this)
              .text()
              .toLowerCase()
              .includes(keyword)

          );
        });
      }
    );
    $('#btnSavePermissions').on('click', () => {
      this.save();
    }
    );
    $(document).on('keydown', (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        this.save();
      }
    }
    );
    window.addEventListener('beforeunload', (e) => {
      if (!this.dirty) {
        return;
      }
      e.preventDefault();
      e.returnValue = '';
    }
    );
  },
  renderSidebar() {
    let html = '';

    Object.keys(this.modules).forEach(module => {
      const total = this.modules[module].length;

      const assigned = this.modules[module]
        .filter(permission =>
          this.assigned.includes(
            Number(permission.id)
          )
        ).length;

      html += `

        <div
            class="permission-module"
            data-module="${module} ">

            <div>

                <div class="permission-module-name">

                    ${module}

                </div>

                <small class="text-muted">

                    ${assigned} / ${total} Assigned

                </small>

            </div>

            <span class="permission-module-count">

                ${total}

            </span>

        </div>

        `;
    });

    $('#moduleList').html(html);

    $('.permission-module:first').addClass('active');
  },

  showModule(module) {
    this.currentModule = module;

    const rows = this.modules[module];

    let html = `

    <div class="permission-card">

        <div class="permission-card-header">

            <div>

                <div class="permission-card-title">

                    ${module}

                </div>

                <div class="text-muted">

                    ${rows.length} permissions

                </div>

            </div>

            <div>

                <label>

                    <input
                        type="checkbox"
                        id="moduleSelectAll">

                    Select All

                </label>

            </div>

        </div>

        <div class="permission-grid">

    `;

    rows.forEach(permission => {
      const checked = this.assigned.includes(

        Number(permission.id)

      );

      html += this.permissionCard(

        permission,

        checked

      );
    });

    html += `

        </div>

    </div>

    `;

    $('#permissionWorkspace').html(html);

    this.refreshModuleCheckbox();
  },

  permissionCard(permission, checked) {
    let icon = 'bi-shield';

    if (permission.slug.includes('.view'))
      icon = 'bi-eye';

    else if (permission.slug.includes('.create'))
      icon = 'bi-plus-circle';

    else if (permission.slug.includes('.edit'))
      icon = 'bi-pencil-square';

    else if (permission.slug.includes('.delete'))
      icon = 'bi-trash';

    else if (permission.slug.includes('.export'))
      icon = 'bi-download';

    else if (permission.slug.includes('.import'))
      icon = 'bi-upload';

    return `

    <div class="permission-item">
        <div class="permission-item-head">
            <div class="permission-item-title">
                <i class="bi ${icon} "></i>
                ${permission.name}
            </div>
            <span class="permission-system">
                ${permission.is_system == 1 ? 'SYSTEM' : 'CUSTOM'}
            </span>
        </div>

        <small>
          ${this.permissionDescription(permission.slug)}
        </small>

        <div class="mt-3">
            <div class="form-check form-switch">
                <input class="form-check-input permission-checkbox" type="checkbox" value="${permission.id} " ${checked ? 'checked' : ''} >
            </div>
        </div>
    </div>`;
  },

  permissionDescription(slug) {
    if (slug.endsWith('.view'))
      return 'Allows viewing records.';
    if (slug.endsWith('.create'))
      return 'Allows creating new records.';
    if (slug.endsWith('.edit'))
      return 'Allows editing existing records.';
    if (slug.endsWith('.delete'))
      return 'Allows deleting records.';
    if (slug.endsWith('.export'))
      return 'Allows exporting records.';
    if (slug.endsWith('.import'))
      return 'Allows importing records.';
    return 'Custom permission.';
  },

  refreshModuleCheckbox() {
    const total = $('.permission-checkbox').length;
    const checked = $('.permission-checkbox:checked').length;
    $('#moduleSelectAll')
      .prop('checked', total === checked)
      .prop('indeterminate', checked > 0 && checked < total);
    const sidebar = $('.permission-module.active');
    sidebar.find('small').text(
      checked + ' / ' + total + ' Assigned'
    );
  },

  setDirty(state = true) {
    this.dirty = state;

    $('#permissionDirty')
      .text(
        state
          ? 'Unsaved changes'
          : 'No changes'
      );
  },

  save() {
    const permissions = [];

    $('.permission-checkbox:checked').each(function () {
      permissions.push($(this).val());
    });

    $('#btnSavePermissions')
      .prop('disabled', true)
      .html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

    $.ajax({
      url: APP.baseUrl + '/roles/permissions/' + this.roleId,

      type: 'POST',

      data: {
        permissions: permissions
      },

      success: (response) => {
        $('#btnSavePermissions')
          .prop('disabled', false)
          .html('<i class="bi bi-check2"></i> Save Changes');

        if (!response.success) {
          APP.error(response.message);

          return;
        }

        APP.success(response.message);

        this.assigned = permissions.map(Number);

        this.setDirty(false);

        this.renderSidebar();
      },

      error: () => {
        $('#btnSavePermissions')
          .prop('disabled', false)
          .html('<i class="bi bi-check2"></i> Save Changes');

        APP.error('Unable to save permissions.');
      }
    });
  },
};

$(function () {
  PermissionWorkspace.init();
});