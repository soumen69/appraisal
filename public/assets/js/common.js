$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
    $('[data-bs-toggle="popover"]').popover();

    $('.select2').select2({
        width: '100%',
        theme: 'bootstrap-5'
    });

    $('.datepicker').flatpickr({
        dateFormat: "d M Y"
    });
});