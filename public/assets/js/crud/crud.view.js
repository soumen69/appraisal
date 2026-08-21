
// const CrudView = {

//     bind(crud) {

//         $(document)
//             .off('click', '.btn-view')
//             .on(
//                 'click',
//                 '.btn-view',
//                 function (e) {

//                     e.preventDefault();

//                     const id =
//                         $(this).data('id');
//                     const url =
//                         typeof crud.viewEndpoint === 'function'
//                             ? crud.viewEndpoint(
//                                 id,
//                                 crud
//                             )
//                             : crud.viewEndpoint
//                                 ? `${crud.viewEndpoint}/${id}`
//                                 : `${crud.endpoint}/edit/${id}`;


//                     $.get(
//                         url,
//                         function (response) {

//                             if (!response.success) {

//                                 APP.error(
//                                     response.message
//                                 );

//                                 return;
//                             }


//                             const title =
//                                 crud.entity
//                                     ? `${crud.entity} Details`
//                                     : 'Details';


//                             CrudDrawer.show(
//                                 title,
//                                 response.data,
//                                 crud
//                             );
//                         }
//                     );
//                 }
//             );
//     }
// };


const CrudView = {

    bind(crud) {

        $(document)
            .off(
                'click.crudView',
                '.btn-view'
            )
            .on(
                'click.crudView',
                '.btn-view',
                function (e) {

                    e.preventDefault();

                    if (!crud.can('view')) {

                        APP.error(
                            'You are not authorized to view this record.'
                        );

                        return;
                    }

                    const id =
                        $(this).data('id');

                    const url =
                        typeof crud.viewEndpoint === 'function'
                            ? crud.viewEndpoint(id, crud)
                            : crud.viewEndpoint
                                ? `${crud.viewEndpoint}/${id}`
                                : `${crud.endpoint}/edit/${id}`;


                    $.get(
                        url,
                        function (response) {

                            if (!response.success) {

                                APP.error(
                                    response.message
                                );

                                return;
                            }

                            const title =
                                crud.entity
                                    ? `${crud.entity} Details`
                                    : 'Details';

                            CrudDrawer.show(
                                title,
                                response.data,
                                crud
                            );
                        }
                    ).fail(function (xhr) {

                        if (
                            APP.handleUnauthorized(xhr)
                        ) {
                            return;
                        }

                        APP.error(
                            xhr.responseJSON?.message ||
                            'Unable to load record.'
                        );
                    });
                }
            );
    }
};