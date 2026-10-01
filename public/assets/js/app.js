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


APP.permissions = Array.isArray(APP.permissions)
    ? APP.permissions.map(String)
    : [];

APP.isSuper = APP.isSuper === true;

APP.can = function (permission) {
    if (APP.isSuper) {
        return true;
    }

    if (!permission) {
        return false;
    }

    return APP.permissions.includes('*') ||
        APP.permissions.includes(String(permission));
};

APP.canAny = function (permissions) {
    if (APP.isSuper) {
        return true;
    }

    if (!Array.isArray(permissions)) {
        return false;
    }

    return permissions.some(permission => APP.can(permission));
};

APP.canAll = function (permissions) {
    if (APP.isSuper) {
        return true;
    }

    if (!Array.isArray(permissions)) {
        return false;
    }

    return permissions.every(permission => APP.can(permission));
};

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