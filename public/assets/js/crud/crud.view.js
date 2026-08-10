// const CrudView = {

//     bind(crud) {
//         $(document)
//             .off('click', '.btn-view')
//             .on('click', '.btn-view', function (e) {
//                 e.preventDefault();
//                 const id = $(this).data('id');

//                 $.get(
//                     `${crud.endpoint}/edit/${id}`,
//                     function (response) {
//                         if (!response.success) {
//                             APP.error(response.message);
//                             return;
//                         }

//                         const title = crud.entity
//                             ? `${crud.entity} Details`
//                             : 'Details';

//                         CrudDrawer.show(
//                             title,
//                             response.data,
//                             crud
//                         );
//                     }
//                 );
//             });
//     }
// };

const CrudView = {

    bind(crud) {

        $(document)
            .off('click', '.btn-view')
            .on(
                'click',
                '.btn-view',
                function (e) {

                    e.preventDefault();

                    const id =
                        $(this).data('id');

                    /*
                     * By default the reusable CRUD
                     * continues using /edit/{id}.
                     *
                     * Specific modules can provide
                     * a custom viewEndpoint.
                     */
                    const url =
                        typeof crud.viewEndpoint === 'function'
                            ? crud.viewEndpoint(
                                id,
                                crud
                            )
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
                    );
                }
            );
    }
};