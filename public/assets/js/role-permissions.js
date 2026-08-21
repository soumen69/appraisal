// const PermissionWorkspace = {
//   roleId: window.roleId,

//   modules: {},

//   assigned: [],

//   currentModule: null,

//   dirty: false,

//   init() {
//     this.load();

//     this.bind();
//   },

//   load() {
//     $.get(

//       APP.baseUrl + '/roles/permissions-data/' + this.roleId,

//       (response) => {
//         if (!response.success) {
//           APP.error(response.message);

//           return;
//         }

//         this.modules = response.data.permissions;

//         this.assigned = response.data.assigned.map(Number);

//         this.renderSidebar();

//         const first = Object.keys(this.modules)[0];

//         if (first) {
//           this.showModule(first);
//         }
//       }

//     );
//   },

//   bind() {
//     const self = this;

//     $(document).on(

//       'click',

//       '.permission-module',

//       function () {
//         $('.permission-module').removeClass('active');

//         $(this).addClass('active');

//         self.showModule(

//           $(this).data('module')

//         );
//       }

//     );
//     $(document).on(
//       'change',
//       '.permission-checkbox',
//       () => {
//         this.refreshModuleCheckbox();

//         this.setDirty(true);
//       }
//     );
//     $(document).on(
//       'change',
//       '#moduleSelectAll',
//       function () {
//         $('.permission-checkbox')
//           .prop(
//             'checked',
//             $(this).prop('checked')
//           );

//         PermissionWorkspace.setDirty(true);
//       }
//     );
//     $('#moduleSearch').on(
//       'keyup',
//       function () {
//         const keyword = $(this)
//           .val()
//           .toLowerCase();

//         $('.permission-module').each(function () {
//           $(this).toggle(

//             $(this)
//               .text()
//               .toLowerCase()
//               .includes(keyword)

//           );
//         });
//       }
//     );
//     $('#permissionSearch').on(
//       'keyup',
//       function () {
//         const keyword = $(this)
//           .val()
//           .toLowerCase();

//         $('.permission-item').each(function () {
//           $(this).toggle(

//             $(this)
//               .text()
//               .toLowerCase()
//               .includes(keyword)

//           );
//         });
//       }
//     );
//     $('#btnSavePermissions').on('click', () => {
//       this.save();
//     }
//     );
//     $(document).on('keydown', (e) => {
//       if ((e.ctrlKey || e.metaKey) && e.key === 's') {
//         e.preventDefault();
//         this.save();
//       }
//     }
//     );
//     window.addEventListener('beforeunload', (e) => {
//       if (!this.dirty) {
//         return;
//       }
//       e.preventDefault();
//       e.returnValue = '';
//     }
//     );
//   },
//   renderSidebar() {
//     let html = '';

//     Object.keys(this.modules).forEach(module => {
//       const total = this.modules[module].length;

//       const assigned = this.modules[module]
//         .filter(permission =>
//           this.assigned.includes(
//             Number(permission.id)
//           )
//         ).length;

//       html += `

//         <div
//             class="permission-module"
//             data-module="${module} ">

//             <div>

//                 <div class="permission-module-name">

//                     ${module}

//                 </div>

//                 <small class="text-muted">

//                     ${assigned} / ${total} Assigned

//                 </small>

//             </div>

//             <span class="permission-module-count">

//                 ${total}

//             </span>

//         </div>

//         `;
//     });

//     $('#moduleList').html(html);

//     $('.permission-module:first').addClass('active');
//   },

//   showModule(module) {
//     this.currentModule = module;

//     const rows = this.modules[module];

//     let html = `

//     <div class="permission-card">

//         <div class="permission-card-header">

//             <div>

//                 <div class="permission-card-title">

//                     ${module}

//                 </div>

//                 <div class="text-muted">

//                     ${rows.length} permissions

//                 </div>

//             </div>

//             <div>

//                 <label>

//                     <input
//                         type="checkbox"
//                         id="moduleSelectAll">

//                     Select All

//                 </label>

//             </div>

//         </div>

//         <div class="permission-grid">

//     `;

//     rows.forEach(permission => {
//       const checked = this.assigned.includes(

//         Number(permission.id)

//       );

//       html += this.permissionCard(

//         permission,

//         checked

//       );
//     });

//     html += `

//         </div>

//     </div>

//     `;

//     $('#permissionWorkspace').html(html);

//     this.refreshModuleCheckbox();
//   },

//   permissionCard(permission, checked) {
//     let icon = 'bi-shield';

//     if (permission.slug.includes('.view'))
//       icon = 'bi-eye';

//     else if (permission.slug.includes('.create'))
//       icon = 'bi-plus-circle';

//     else if (permission.slug.includes('.edit'))
//       icon = 'bi-pencil-square';

//     else if (permission.slug.includes('.delete'))
//       icon = 'bi-trash';

//     else if (permission.slug.includes('.export'))
//       icon = 'bi-download';

//     else if (permission.slug.includes('.import'))
//       icon = 'bi-upload';

//     return `

//     <div class="permission-item">
//         <div class="permission-item-head">
//             <div class="permission-item-title">
//                 <i class="bi ${icon} "></i>
//                 ${permission.name}
//             </div>
//             <span class="permission-system">
//                 ${permission.is_system == 1 ? 'SYSTEM' : 'CUSTOM'}
//             </span>
//         </div>

//         <small>
//           ${this.permissionDescription(permission.slug)}
//         </small>

//         <div class="mt-3">
//             <div class="form-check form-switch">
//                 <input class="form-check-input permission-checkbox" type="checkbox" value="${permission.id} " ${checked ? 'checked' : ''} >
//             </div>
//         </div>
//     </div>`;
//   },

//   permissionDescription(slug) {
//     if (slug.endsWith('.view'))
//       return 'Allows viewing records.';
//     if (slug.endsWith('.create'))
//       return 'Allows creating new records.';
//     if (slug.endsWith('.edit'))
//       return 'Allows editing existing records.';
//     if (slug.endsWith('.delete'))
//       return 'Allows deleting records.';
//     if (slug.endsWith('.export'))
//       return 'Allows exporting records.';
//     if (slug.endsWith('.import'))
//       return 'Allows importing records.';
//     return 'Custom permission.';
//   },

//   refreshModuleCheckbox() {
//     const total = $('.permission-checkbox').length;
//     const checked = $('.permission-checkbox:checked').length;
//     $('#moduleSelectAll')
//       .prop('checked', total === checked)
//       .prop('indeterminate', checked > 0 && checked < total);
//     const sidebar = $('.permission-module.active');
//     sidebar.find('small').text(
//       checked + ' / ' + total + ' Assigned'
//     );
//   },

//   setDirty(state = true) {
//     this.dirty = state;

//     $('#permissionDirty')
//       .text(
//         state
//           ? 'Unsaved changes'
//           : 'No changes'
//       );
//   },

//   save() {
//     const permissions = [];

//     $('.permission-checkbox:checked').each(function () {
//       permissions.push($(this).val());
//     });

//     $('#btnSavePermissions')
//       .prop('disabled', true)
//       .html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

//     $.ajax({
//       url: APP.baseUrl + '/roles/permissions/' + this.roleId,

//       type: 'POST',

//       data: {
//         permissions: permissions
//       },

//       success: (response) => {
//         $('#btnSavePermissions')
//           .prop('disabled', false)
//           .html('<i class="bi bi-check2"></i> Save Changes');

//         if (!response.success) {
//           APP.error(response.message);

//           return;
//         }

//         APP.success(response.message);

//         this.assigned = permissions.map(Number);

//         this.setDirty(false);

//         this.renderSidebar();
//       },

//       error: () => {
//         $('#btnSavePermissions')
//           .prop('disabled', false)
//           .html('<i class="bi bi-check2"></i> Save Changes');

//         APP.error('Unable to save permissions.');
//       }
//     });
//   },
// };

// $(function () {
//   PermissionWorkspace.init();
// });



const PermissionWorkspace = {

  roleId: Number(window.roleId),

  /*
   * permissions grouped by module name
   *
   * {
   *   Employees: [...],
   *   Roles: [...],
   *   Departments: [...]
   * }
   */
  modules: {},

  /*
   * Permissions currently stored in database.
   */
  assigned: new Set(),

  /*
   * Permissions currently selected in the UI.
   *
   * This is separate from `assigned` so we can safely
   * navigate between modules before saving.
   */
  working: new Set(),

  currentModule: null,

  dirty: false,

  saving: false,

  init() {

    if (!this.roleId) {

      APP.error(
        'Invalid role.'
      );

      return;
    }

    if (
      !APP.can('role.permission')
    ) {

      $('#permissionWorkspace')
        .html(`
                <div class="text-center py-5">

                    <i class="
                        bi bi-shield-lock
                        display-5
                        text-muted
                    "></i>

                    <h5 class="mt-3">
                        Access Restricted
                    </h5>

                    <p class="text-muted mb-0">
                        You are not authorized to manage role permissions.
                    </p>

                </div>
            `);

      $('#btnSavePermissions')
        .prop(
          'disabled',
          true
        );

      return;
    }

    this.bind();

    this.load();
  },


  /*
   * =========================================================
   * Load
   * =========================================================
   */

  load() {

    $.ajax({

      url:
        `${APP.baseUrl}/roles/permissions-data/${this.roleId}`,

      type: 'GET',

      success: (response) => {

        if (
          !response ||
          response.success !== true
        ) {

          APP.error(
            response?.message ||
            'Unable to load permissions.'
          );

          return;
        }

        this.modules =
          response.data?.permissions || {};

        const assigned =
          response.data?.assigned || [];

        /*
         * Normalize permission IDs.
         */
        this.assigned =
          new Set(
            assigned.map(
              Number
            )
          );

        /*
         * Clone into working state.
         */
        this.working =
          new Set(
            this.assigned
          );

        this.setDirty(false);

        this.renderSidebar();

        const modules =
          Object.keys(
            this.modules
          );

        if (!modules.length) {

          $('#permissionWorkspace').html(`
                        <div class="text-center py-5">
                            <i class="bi bi-shield-x fs-1 text-muted"></i>

                            <h5 class="mt-3">
                                No permissions found
                            </h5>

                            <p class="text-muted mb-0">
                                No permissions are registered yet.
                            </p>
                        </div>
                    `);

          return;
        }

        this.showModule(
          modules[0]
        );
      },

      error: (xhr) => {

        if (
          APP.handleUnauthorized(xhr)
        ) {
          return;
        }

        APP.error(
          xhr.responseJSON?.message ||
          'Unable to load role permissions.'
        );
      }
    });
  },


  /*
   * =========================================================
   * Events
   * =========================================================
   */

  bind() {

    /*
     * Module navigation.
     */
    $(document)
      .off(
        'click.rolePermission',
        '.permission-module'
      )
      .on(
        'click.rolePermission',
        '.permission-module',
        (e) => {

          e.preventDefault();

          const module =
            String(
              $(e.currentTarget)
                .data('module')
            );

          this.showModule(
            module
          );
        }
      );


    /*
     * Individual permission.
     */
    $(document)
      .off(
        'change.rolePermission',
        '.permission-checkbox'
      )
      .on(
        'change.rolePermission',
        '.permission-checkbox',
        (e) => {

          const id =
            Number(
              $(e.currentTarget).val()
            );

          if (!id) {
            return;
          }

          if (
            $(e.currentTarget).is(':checked')
          ) {

            this.working.add(id);

          } else {

            this.working.delete(id);
          }

          this.refreshModuleCheckbox();

          this.setDirty(
            !this.areSetsEqual(
              this.assigned,
              this.working
            )
          );
        }
      );


    /*
     * Current module Select All.
     */
    $(document)
      .off(
        'change.rolePermission',
        '#moduleSelectAll'
      )
      .on(
        'change.rolePermission',
        '#moduleSelectAll',
        (e) => {

          const checked =
            $(e.currentTarget)
              .is(':checked');

          const rows =
            this.modules[
            this.currentModule
            ] || [];

          rows.forEach(
            (permission) => {

              const id =
                Number(
                  permission.id
                );

              if (checked) {

                this.working.add(
                  id
                );

              } else {

                this.working.delete(
                  id
                );
              }
            }
          );

          /*
           * Re-render the current module
           * so all hidden/search state is
           * represented by the working set.
           */
          this.showModule(
            this.currentModule,
            false
          );

          this.setDirty(
            !this.areSetsEqual(
              this.assigned,
              this.working
            )
          );
        }
      );


    /*
     * Module search.
     */
    $('#moduleSearch')
      .off('input.rolePermission')
      .on(
        'input.rolePermission',
        (e) => {

          const keyword =
            String(
              $(e.currentTarget).val()
            )
              .trim()
              .toLowerCase();

          $('.permission-module')
            .each(function () {

              const text =
                $(this)
                  .find(
                    '.permission-module-name'
                  )
                  .text()
                  .trim()
                  .toLowerCase();

              $(this).toggle(
                text.includes(
                  keyword
                )
              );
            });
        }
      );


    /*
     * Permission search.
     */
    $('#permissionSearch')
      .off('input.rolePermission')
      .on(
        'input.rolePermission',
        (e) => {

          const keyword =
            String(
              $(e.currentTarget).val()
            )
              .trim()
              .toLowerCase();

          $('.permission-item')
            .each(function () {

              const text =
                $(this)
                  .text()
                  .toLowerCase();

              $(this).toggle(
                text.includes(
                  keyword
                )
              );
            });

          /*
           * Don't let search affect the
           * module's overall assigned count.
           */
          PermissionWorkspace
            .refreshModuleCheckbox();
        }
      );


    /*
     * Save.
     */
    $('#btnSavePermissions')
      .off('click.rolePermission')
      .on(
        'click.rolePermission',
        () => {

          this.save();
        }
      );


    /*
     * Ctrl + S / Cmd + S.
     */
    $(document)
      .off('keydown.rolePermission')
      .on(
        'keydown.rolePermission',
        (e) => {

          if (
            (e.ctrlKey || e.metaKey) &&
            e.key.toLowerCase() === 's'
          ) {

            e.preventDefault();

            this.save();
          }
        }
      );


    /*
     * Warn about unsaved changes.
     */
    window.addEventListener(
      'beforeunload',
      (e) => {

        if (!this.dirty || this.saving) {
          return;
        }

        e.preventDefault();

        e.returnValue = '';
      }
    );
  },


  /*
   * =========================================================
   * Sidebar
   * =========================================================
   */

  renderSidebar() {

    let html = '';

    Object.keys(
      this.modules
    ).forEach(
      (module) => {

        const permissions =
          this.modules[module] || [];

        const total =
          permissions.length;

        const assigned =
          permissions.filter(
            permission =>
              this.working.has(
                Number(
                  permission.id
                )
              )
          ).length;

        html += `
                    <button
                        type="button"
                        class="permission-module"
                        data-module="${this.escape(
          module
        )}">

                        <div class="permission-module-main">

                            <div
                                class="permission-module-name">

                                ${this.escape(
          module
        )}

                            </div>

                            <small
                                class="text-muted
                                       permission-module-assigned">

                                ${assigned} / ${total} Assigned

                            </small>

                        </div>

                        <span
                            class="permission-module-count">

                            ${total}

                        </span>

                    </button>
                `;
      }
    );

    $('#moduleList')
      .html(
        html
      );

    /*
     * Restore active module.
     */
    if (
      this.currentModule &&
      this.modules[this.currentModule]
    ) {

      this.setActiveModule(
        this.currentModule
      );

    } else {

      $('.permission-module')
        .first()
        .addClass('active');
    }
  },


  setActiveModule(module) {

    $('.permission-module')
      .removeClass('active');

    $(
      '.permission-module'
    )
      .filter(
        `[data-module="${this.escapeSelector(
          module
        )}"]`
      )
      .addClass('active');
  },


  /*
   * =========================================================
   * Workspace
   * =========================================================
   */

  showModule(
    module,
    updateSidebar = true
  ) {

    if (
      !this.modules[module]
    ) {
      return;
    }

    this.currentModule =
      module;

    if (updateSidebar) {

      this.setActiveModule(
        module
      );
    }

    const rows =
      this.modules[module] || [];

    let html = `
            <div class="permission-card">

                <div class="permission-card-header">

                    <div>
                        <div class="permission-card-title">
                            ${this.escape(module)}
                        </div>

                        <div class="text-muted">
                            ${rows.length}
                            ${rows.length === 1
        ? 'permission'
        : 'permissions'}
                        </div>
                    </div>

                    <div>

                        <label
                            class="d-flex
                                   align-items-center
                                   gap-2
                                   mb-0">

                            <input
                                type="checkbox"
                                id="moduleSelectAll">

                            <span>
                                Select All
                            </span>

                        </label>

                    </div>

                </div>

                <div class="permission-grid">
        `;

    if (!rows.length) {

      html += `
                <div class="text-center
                            text-muted
                            py-4">

                    No permissions available.

                </div>
            `;

    } else {

      rows.forEach(
        permission => {

          const checked =
            this.working.has(
              Number(
                permission.id
              )
            );

          html += this.permissionCard(
            permission,
            checked
          );
        }
      );
    }

    html += `
                </div>
            </div>
        `;

    $('#permissionWorkspace')
      .html(
        html
      );

    this.refreshModuleCheckbox();
  },


  permissionCard(
    permission,
    checked
  ) {

    const slug =
      String(
        permission.slug || ''
      );

    let icon =
      'bi-shield';

    if (
      slug.endsWith('.view')
    ) {
      icon = 'bi-eye';

    } else if (
      slug.endsWith('.create')
    ) {
      icon = 'bi-plus-circle';

    } else if (
      slug.endsWith('.edit')
    ) {
      icon = 'bi-pencil-square';

    } else if (
      slug.endsWith('.delete')
    ) {
      icon = 'bi-trash';

    } else if (
      slug.endsWith('.export')
    ) {
      icon = 'bi-download';

    } else if (
      slug.endsWith('.import')
    ) {
      icon = 'bi-upload';
    }

    return `
            <div class="permission-item">

                <div class="permission-item-head">

                    <div class="permission-item-title">

                        <i class="bi ${icon}"></i>

                        ${this.escape(
      permission.name
    )}

                    </div>

                    <span class="permission-system">

                        ${Number(
      permission.is_system
    ) === 1
        ? 'SYSTEM'
        : 'CUSTOM'}

                    </span>

                </div>

                <div
                    class="permission-slug">

                    ${this.escape(
          slug
        )}

                </div>

                <small>
                    ${this.escape(
          this.permissionDescription(
            slug
          )
        )}
                </small>

                <div class="mt-3">

                    <div class="form-check form-switch">

                        <input
                            class="form-check-input permission-checkbox"
                            type="checkbox"
                            value="${Number(
          permission.id
        )}"
                            ${checked
        ? 'checked'
        : ''}>

                    </div>

                </div>

            </div>
        `;
  },


  permissionDescription(slug) {

    if (
      slug.endsWith('.view')
    ) {
      return 'Allows viewing records.';
    }

    if (
      slug.endsWith('.create')
    ) {
      return 'Allows creating new records.';
    }

    if (
      slug.endsWith('.edit')
    ) {
      return 'Allows editing existing records.';
    }

    if (
      slug.endsWith('.delete')
    ) {
      return 'Allows deleting records.';
    }

    if (
      slug.endsWith('.export')
    ) {
      return 'Allows exporting records.';
    }

    if (
      slug.endsWith('.import')
    ) {
      return 'Allows importing records.';
    }

    return 'Custom permission.';
  },


  /*
   * =========================================================
   * Select All / Counts
   * =========================================================
   */

  refreshModuleCheckbox() {

    const rows =
      this.modules[
      this.currentModule
      ] || [];

    if (!rows.length) {

      $('#moduleSelectAll')
        .prop('checked', false)
        .prop('indeterminate', false);

      return;
    }

    const total =
      rows.length;

    const checked =
      rows.filter(
        permission =>
          this.working.has(
            Number(
              permission.id
            )
          )
      ).length;

    $('#moduleSelectAll')
      .prop(
        'checked',
        checked === total
      )
      .prop(
        'indeterminate',
        checked > 0 &&
        checked < total
      );

    /*
     * Sidebar count must represent the
     * working state, not visible search results.
     */
    const $sidebar =
      $('.permission-module')
        .filter(
          `[data-module="${this.escapeSelector(
            this.currentModule
          )}"]`
        );

    $sidebar
      .find(
        '.permission-module-assigned'
      )
      .text(
        `${checked} / ${total} Assigned`
      );
  },


  /*
   * =========================================================
   * Dirty State
   * =========================================================
   */

  setDirty(state = true) {

    this.dirty =
      Boolean(state);

    const $dirty =
      $('#permissionDirty');

    $dirty
      .text(
        this.dirty
          ? 'Unsaved changes'
          : 'No changes'
      )
      .toggleClass(
        'text-muted',
        !this.dirty
      )
      .toggleClass(
        'text-warning',
        this.dirty
      );

    $('#btnSavePermissions')
      .prop(
        'disabled',
        !this.dirty &&
        !this.saving
      );
  },


  /*
   * =========================================================
   * Save
   * =========================================================
   */

  save() {

    if (!APP.can('role.permission')) {

      APP.error(
        'You are not authorized to update role permissions.'
      );

      return;
    }

    if (this.saving) {
      return;
    }

    if (!this.dirty) {

      APP.info(
        'There are no permission changes to save.'
      );

      return;
    }

    const permissions =
      Array.from(
        this.working
      ).map(Number);

    this.saving = true;

    const $button =
      $('#btnSavePermissions');

    $button
      .prop(
        'disabled',
        true
      )
      .html(`
                <span
                    class="spinner-border
                           spinner-border-sm
                           me-2"
                    role="status">
                </span>
                Saving...
            `);

    const requestData = {
      permissions: permissions
    };

    /*
     * Always send CSRF token.
     */
    if (
      APP.csrfName &&
      APP.csrfHash
    ) {

      requestData[
        APP.csrfName
      ] = APP.csrfHash;
    }

    $.ajax({

      url:
        `${APP.baseUrl}/roles/permissions/${this.roleId}`,

      type: 'POST',

      data: requestData,

      success: (response) => {

        if (
          !response ||
          response.success !== true
        ) {

          APP.error(
            response?.message ||
            'Unable to save permissions.'
          );

          return;
        }

        /*
         * Database state is now the working state.
         */
        this.assigned =
          new Set(
            permissions
          );

        this.working =
          new Set(
            permissions
          );

        this.setDirty(false);

        this.renderSidebar();

        this.showModule(
          this.currentModule,
          false
        );

        APP.success(
          response.message ||
          'Permissions updated successfully.'
        );
      },

      error: (xhr) => {

        if (
          APP.handleUnauthorized(xhr)
        ) {
          return;
        }

        if (
          xhr.status === 422 &&
          xhr.responseJSON
        ) {

          APP.error(
            xhr.responseJSON.message ||
            'Invalid permission selection.'
          );

          return;
        }

        APP.error(
          xhr.responseJSON?.message ||
          'Unable to save permissions.'
        );
      },

      complete: () => {

        this.saving = false;

        $button
          .html(`
                        <i class="bi bi-check2"></i>
                        Save Changes
                    `);

        this.setDirty(
          !this.areSetsEqual(
            this.assigned,
            this.working
          )
        );
      }
    });
  },


  /*
   * =========================================================
   * Utilities
   * =========================================================
   */

  areSetsEqual(a, b) {

    if (
      a.size !==
      b.size
    ) {
      return false;
    }

    for (
      const value of a
    ) {

      if (
        !b.has(value)
      ) {
        return false;
      }
    }

    return true;
  },


  escape(value) {

    return String(
      value ?? ''
    )
      .replace(
        /&/g,
        '&amp;'
      )
      .replace(
        /</g,
        '&lt;'
      )
      .replace(
        />/g,
        '&gt;'
      )
      .replace(
        /"/g,
        '&quot;'
      )
      .replace(
        /'/g,
        '&#039;'
      );
  },


  escapeSelector(value) {

    /*
     * Modern browsers support CSS.escape.
     * Fallback keeps this safe for unusual module names.
     */
    if (
      window.CSS &&
      typeof CSS.escape === 'function'
    ) {

      return CSS.escape(
        String(value)
      );
    }

    return String(value)
      .replace(
        /([!"#$%&'()*+,./:;<=>?@[\\\]^`{|}~])/g,
        '\\$1'
      );
  }
};


$(function () {
  PermissionWorkspace.init();
});