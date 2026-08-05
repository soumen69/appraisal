window.APP = window.APP || {};

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