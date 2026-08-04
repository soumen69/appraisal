window.APP = {
    success(message) {
        toastr.success(message);
    },

    error(message) {
        toastr.error(message);
    },

    warning(message) {
        toastr.warning(message);
    },

    info(message) {
        toastr.info(message);
    },

    confirm(title, text, callback) {
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
    }
};