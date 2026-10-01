const PermissionWorkspace = {
  roleId: Number(window.roleId),
  groups: [],
  assigned: new Set(),
  working: new Set(),
  currentGroup: null,
  dirty: false,
  saving: false,

  init() {
    if (!this.roleId) {
      APP.error('Invalid role.');
      return;
    }

    if (!APP.can('role.permission')) {
      $('#permissionWorkspace').html(`
        <div class="text-center py-5">
          <i class="bi bi-shield-lock display-5 text-muted"></i>
          <h5 class="mt-3">Access Restricted</h5>
          <p class="text-muted mb-0">You are not authorized to manage role permissions.</p>
        </div>
      `);
      $('#btnSavePermissions').prop('disabled', true);
      return;
    }

    this.bind();
    this.load();
  },

  load() {
    $.ajax({
      url: `${APP.baseUrl}/roles/permissions-data/${this.roleId}`,
      type: 'GET',
      success: (response) => {
        if (!response || response.success !== true) {
          APP.error(response?.message || 'Unable to load permissions.');
          return;
        }

        this.groups = response.data?.permissions || [];

        const assigned = response.data?.assigned || [];
        this.assigned = new Set(assigned.map(Number));
        this.working = new Set(this.assigned);

        this.setDirty(false);
        this.renderSidebar();

        if (!this.groups.length) {
          $('#permissionWorkspace').html(`
            <div class="text-center py-5">
              <i class="bi bi-shield-x fs-1 text-muted"></i>
              <h5 class="mt-3">No permissions found</h5>
              <p class="text-muted mb-0">No application capabilities are registered.</p>
            </div>
          `);
          return;
        }

        this.showGroup(0);
      },
      error: (xhr) => {
        if (APP.handleUnauthorized(xhr)) return;
        APP.error(xhr.responseJSON?.message || 'Unable to load role permissions.');
      }
    });
  },

  bind() {
    $(document)
      .off('click.rolePermission', '.permission-area')
      .on('click.rolePermission', '.permission-area', (e) => {
        e.preventDefault();
        this.showGroup(Number($(e.currentTarget).data('index')));
      });

    $(document)
      .off('change.rolePermission', '.permission-checkbox')
      .on('change.rolePermission', '.permission-checkbox', (e) => {
        const id = Number($(e.currentTarget).val());
        if (!id) return;

        $(e.currentTarget).is(':checked')
          ? this.working.add(id)
          : this.working.delete(id);

        this.refreshGroupCheckbox();
        this.setDirty(!this.areSetsEqual(this.assigned, this.working));
      });

    $(document)
      .off('change.rolePermission', '#groupSelectAll')
      .on('change.rolePermission', '#groupSelectAll', (e) => {
        const checked = $(e.currentTarget).is(':checked');
        const rows = this.groups[this.currentGroup]?.permissions || [];

        rows.forEach(permission => {
          const id = Number(permission.id);
          checked ? this.working.add(id) : this.working.delete(id);
        });

        this.showGroup(this.currentGroup, false);
        this.setDirty(!this.areSetsEqual(this.assigned, this.working));
      });

    $('#areaSearch')
      .off('input.rolePermission')
      .on('input.rolePermission', (e) => {
        const keyword = String($(e.currentTarget).val() || '').trim().toLowerCase();

        $('.permission-area').each(function () {
          $(this).toggle(
            $(this).find('.permission-area-name').text().toLowerCase().includes(keyword)
          );
        });
      });

    $('#permissionSearch')
      .off('input.rolePermission')
      .on('input.rolePermission', (e) => {
        const keyword = String($(e.currentTarget).val() || '').trim().toLowerCase();

        $('.permission-item').each(function () {
          $(this).toggle($(this).text().toLowerCase().includes(keyword));
        });

        this.refreshGroupCheckbox();
      });

    $('#btnSavePermissions')
      .off('click.rolePermission')
      .on('click.rolePermission', () => this.save());

    $(document)
      .off('keydown.rolePermission')
      .on('keydown.rolePermission', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
          e.preventDefault();
          this.save();
        }
      });

    window.addEventListener('beforeunload', (e) => {
      if (!this.dirty || this.saving) return;

      e.preventDefault();
      e.returnValue = '';
    });
  },

  renderSidebar() {
    let html = '';

    this.groups.forEach((group, index) => {
      const rows = group.permissions || [];
      const assigned = rows.filter(
        permission => this.working.has(Number(permission.id))
      ).length;

      html += `
        <button type="button" class="permission-area" data-index="${index}">
          <div class="permission-area-main">
            <div class="permission-area-name">${this.escape(group.label)}</div>
            <small class="permission-area-assigned">${assigned} / ${rows.length} Enabled</small>
          </div>
          <span class="permission-area-count">${rows.length}</span>
        </button>
      `;
    });

    $('#areaList').html(html);

    if (this.currentGroup !== null && this.groups[this.currentGroup]) {
      this.setActiveGroup(this.currentGroup);
    } else {
      $('.permission-area').first().addClass('active');
    }
  },

  setActiveGroup(index) {
    $('.permission-area').removeClass('active');
    $(`.permission-area[data-index="${index}"]`).addClass('active');
  },

  showGroup(index, updateSidebar = true) {
    const group = this.groups[index];

    if (!group) return;

    this.currentGroup = index;

    if (updateSidebar) {
      this.setActiveGroup(index);
    }

    const rows = group.permissions || [];

    let html = `
      <div class="permission-card">
        <div class="permission-card-header">
          <div>
            <div class="permission-card-title">${this.escape(group.label)}</div>
            <div class="text-muted">
              ${rows.length} ${rows.length === 1 ? 'capability' : 'capabilities'}
            </div>
          </div>

          <label>
            <input type="checkbox" id="groupSelectAll">
            <span>Enable All</span>
          </label>
        </div>

        <div class="permission-grid">
    `;

    if (!rows.length) {
      html += `
        <div class="text-center text-muted py-4">
          No capabilities available.
        </div>
      `;
    } else {
      rows.forEach(permission => {
        html += this.permissionCard(
          permission,
          this.working.has(Number(permission.id))
        );
      });
    }

    html += `
        </div>
      </div>
    `;

    $('#permissionWorkspace').html(html);
    this.refreshGroupCheckbox();
  },

  permissionCard(permission, checked) {
    const action = String(
      permission.action || permission.slug || ''
    ).split('.').pop();

    const icons = {
      view: 'bi-eye',
      create: 'bi-plus-circle',
      edit: 'bi-pencil-square',
      delete: 'bi-trash',
      import: 'bi-upload',
      export: 'bi-download',
      permission: 'bi-shield-lock'
    };

    const icon = icons[action] || 'bi-shield';

    return `
      <div class="permission-item">
        <div class="permission-item-head">
          <div class="permission-item-title">
            <i class="bi ${icon}"></i>
            ${this.escape(permission.name)}
          </div>
          <span class="permission-system">SYSTEM</span>
        </div>

        <div class="permission-slug">${this.escape(permission.slug)}</div>

        <small>
          ${this.escape(permission.description || 'Application capability.')}
        </small>

        <div class="permission-switch">
          <div class="form-check form-switch">
            <input
              class="form-check-input permission-checkbox"
              type="checkbox"
              value="${Number(permission.id)}"
              ${checked ? 'checked' : ''}
            >
          </div>
        </div>
      </div>
    `;
  },

  refreshGroupCheckbox() {
    const rows = this.groups[this.currentGroup]?.permissions || [];

    if (!rows.length) {
      $('#groupSelectAll')
        .prop('checked', false)
        .prop('indeterminate', false);
      return;
    }

    const checked = rows.filter(
      permission => this.working.has(Number(permission.id))
    ).length;

    $('#groupSelectAll')
      .prop('checked', checked === rows.length)
      .prop('indeterminate', checked > 0 && checked < rows.length);

    const $sidebar = $(
      `.permission-area[data-index="${this.currentGroup}"]`
    );

    $sidebar
      .find('.permission-area-assigned')
      .text(`${checked} / ${rows.length} Enabled`);
  },

  setDirty(state = true) {
    this.dirty = Boolean(state);

    $('#permissionDirty')
      .text(this.dirty ? 'Unsaved changes' : 'No changes')
      .toggleClass('text-muted', !this.dirty)
      .toggleClass('text-warning', this.dirty);

    $('#btnSavePermissions')
      .prop('disabled', !this.dirty || this.saving);
  },

  save() {
    if (!APP.can('role.permission')) {
      APP.error('You are not authorized to update role permissions.');
      return;
    }

    if (this.saving) return;

    if (!this.dirty) {
      APP.info('There are no permission changes to save.');
      return;
    }

    const permissions = Array.from(this.working).map(Number);

    this.saving = true;

    const $button = $('#btnSavePermissions');

    $button
      .prop('disabled', true)
      .html(`
        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
        Saving...
      `);

    const requestData = { permissions };

    if (APP.csrfName && APP.csrfHash) {
      requestData[APP.csrfName] = APP.csrfHash;
    }

    $.ajax({
      url: `${APP.baseUrl}/roles/permissions/${this.roleId}`,
      type: 'POST',
      data: requestData,

      success: (response) => {
        if (!response || response.success !== true) {
          APP.error(response?.message || 'Unable to save permissions.');
          return;
        }

        this.assigned = new Set(permissions);
        this.working = new Set(permissions);

        this.setDirty(false);
        this.renderSidebar();
        this.showGroup(this.currentGroup, false);

        APP.success(
          response.message || 'Permissions updated successfully.'
        );
      },

      error: (xhr) => {
        if (APP.handleUnauthorized(xhr)) return;

        APP.error(
          xhr.responseJSON?.message ||
          'Unable to save permissions.'
        );
      },

      complete: () => {
        this.saving = false;

        $button.html(
          '<i class="bi bi-check2"></i> Save Changes'
        );

        this.setDirty(
          !this.areSetsEqual(
            this.assigned,
            this.working
          )
        );
      }
    });
  },

  areSetsEqual(a, b) {
    if (a.size !== b.size) return false;

    for (const value of a) {
      if (!b.has(value)) return false;
    }

    return true;
  },

  escape(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
};

$(function () {
  PermissionWorkspace.init();
});