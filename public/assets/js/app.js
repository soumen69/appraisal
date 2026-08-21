// window.APP = window.APP || {};

// APP.success = function (message) {
//     toastr.success(message);
// };

// APP.error = function (message) {
//     toastr.error(message);
// };

// APP.warning = function (message) {
//     toastr.warning(message);
// };

// APP.info = function (message) {
//     toastr.info(message);
// };

// APP.confirm = function (title, text, callback) {
//     Swal.fire({
//         title: title,
//         text: text,
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonText: 'Yes',
//         cancelButtonText: 'Cancel',
//         reverseButtons: true
//     }).then((result) => {
//         if (result.isConfirmed) {
//             callback();
//         }
//     });
// };


window.APP = window.APP || {};

/*
|--------------------------------------------------------------------------
| Notifications
|--------------------------------------------------------------------------
*/

APP.success = function (message) {
    toastr.success(message);
};

APP.error = function (message) {
    toastr.error(message);
};

APP.warning = function (message) {
    toastr.warning(message);
};

APP.info = function (message) {
    toastr.info(message);
};


/*
|--------------------------------------------------------------------------
| Confirmation
|--------------------------------------------------------------------------
*/

APP.confirm = function (title, text, callback) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
};


/*
|--------------------------------------------------------------------------
| Permission helpers
|--------------------------------------------------------------------------
*/

APP.permissions = Array.isArray(APP.permissions)
    ? APP.permissions.map(String)
    : [];

APP.isSuper = APP.isSuper === true;


/**
 * Check one permission.
 *
 * Super administrators always pass.
 */
APP.can = function (permission) {

    if (APP.isSuper) {
        return true;
    }

    if (!permission) {
        return false;
    }

    return APP.permissions.includes(String(permission));
};


/**
 * Check any permission.
 */
APP.canAny = function (permissions) {

    if (APP.isSuper) {
        return true;
    }

    if (!Array.isArray(permissions)) {
        return false;
    }

    return permissions.some(permission =>
        APP.can(permission)
    );
};


/**
 * Check all permissions.
 */
APP.canAll = function (permissions) {

    if (APP.isSuper) {
        return true;
    }

    if (!Array.isArray(permissions)) {
        return false;
    }

    return permissions.every(permission =>
        APP.can(permission)
    );
};


/**
 * Handle an unauthorized AJAX response.
 */
APP.handleUnauthorized = function (xhr) {

    if (!xhr || xhr.status !== 403) {
        return false;
    }

    const message =
        xhr.responseJSON?.message ||
        'You are not authorized to perform this action.';

    APP.error(message);

    return true;
};