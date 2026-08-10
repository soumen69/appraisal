// const CrudDelete = {

//     bind(crud) {

//         $(document)
//             .off('click', '.btn-delete')
//             .on('click', '.btn-delete', function (e) {

//                 e.preventDefault();

//                 const id = $(this).data('id');

//                 Swal.fire({
//                     title: `Delete ${crud.entity}?`,
//                     text: 'This action cannot be undone.',
//                     icon: 'warning',
//                     showCancelButton: true,
//                     confirmButtonText: 'Delete',
//                     cancelButtonText: 'Cancel',
//                     confirmButtonColor: '#dc3545'
//                 }).then(result => {
//                     if (!result.isConfirmed) {
//                         return;
//                     }

//                     $.ajax({
//                         url: `${crud.endpoint}/delete/${id}`,
//                         type: 'POST',
//                         success(response) {
//                             if (!response.success) {
//                                 APP.error(response.message);
//                                 return;
//                             }
//                             APP.success(response.message);
//                             crud.reload();
//                         },

//                         error(xhr) {
//                             if (xhr.status === 403) {
//                                 APP.error('You are not authorized.');
//                                 return;
//                             }
//                             APP.error('Request failed.');
//                         }
//                     });
//                 });
//             });
//     }
// };

const CrudDelete = {

    bind(crud) {

        $(document)
            .off(
                'click.crudDelete',
                '.btn-delete'
            )
            .on(
                'click.crudDelete',
                '.btn-delete',
                function (e) {

                    e.preventDefault();

                    const id =
                        $(this).data('id');

                    Swal.fire({

                        title:
                            `Delete ${crud.entity}?`,

                        text:
                            'This action cannot be undone.',

                        icon: 'warning',

                        showCancelButton: true,

                        confirmButtonText:
                            'Delete',

                        cancelButtonText:
                            'Cancel',

                        confirmButtonColor:
                            '#dc3545'

                    }).then(result => {

                        if (!result.isConfirmed) {
                            return;
                        }


                        $.ajax({

                            url:
                                `${crud.endpoint}/delete/${id}`,

                            type: 'POST',

                            data: {
                                [APP.csrfName]:
                                    APP.csrfHash
                            },

                            success(response) {

                                if (!response.success) {

                                    APP.error(
                                        response.message
                                    );

                                    return;
                                }

                                APP.success(
                                    response.message
                                );

                                crud.reload();
                            },

                            error(xhr) {

                                if (
                                    xhr.status ===
                                    403
                                ) {

                                    APP.error(
                                        'You are not authorized.'
                                    );

                                    return;
                                }

                                APP.error(
                                    xhr.responseJSON?.message ||
                                    'Request failed.'
                                );
                            }
                        });

                    });
                }
            );
    }
};