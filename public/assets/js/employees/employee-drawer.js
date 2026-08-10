// const EmployeeDrawer = {
//     render(data) {
//         const employeeId =
//             parseInt(data.id, 10);

//         const fullName =
//             data.full_name ||
//             [
//                 data.first_name,
//                 data.last_name
//             ]
//                 .filter(Boolean)
//                 .join(' ') ||
//             'Employee';

//         const initials =
//             this.getInitials(
//                 fullName
//             );

//         const status =
//             data.status === 'active'

//                 ? `
//                     <span class="status-badge status-active">
//                         Active
//                     </span>
//                 `

//                 : `
//                     <span class="status-badge status-inactive">
//                         Inactive
//                     </span>
//                 `;

//         const joiningDate =
//             this.formatDate(
//                 data.joining_date
//             );

//         return `

//             <!-- =====================================================
//                  Employee Summary
//                  ===================================================== -->

//             <div class="employee-drawer-profile">

//                 <div class="employee-drawer-avatar">

//                     ${data.profile_photo
//                 ? `
//                                 <img
//                                     src="${this.escapeAttribute(
//                     this.getPhotoUrl(
//                         data.profile_photo
//                     )
//                 )}"
//                                     alt="${this.escapeAttribute(
//                     fullName
//                 )}"
//                                 >
//                             `
//                 : `
//                                 <span>
//                                     ${this.escapeHtml(
//                     initials
//                 )}
//                                 </span>
//                             `
//             }

//                 </div>

//                 <div class="employee-drawer-profile-info">

//                     <h5>
//                         ${this.escapeHtml(
//                 fullName
//             )}
//                     </h5>

//                     ${data.employee_code
//                 ? `
//                                 <div class="employee-drawer-code">
//                                     ${this.escapeHtml(
//                     data.employee_code
//                 )}
//                                 </div>
//                             `
//                 : ''
//             }

//                     ${data.designation_name
//                 ? `
//                                 <div class="employee-drawer-designation">
//                                     ${this.escapeHtml(
//                     data.designation_name
//                 )}
//                                 </div>
//                             `
//                 : ''
//             }

//                     <div class="mt-2">
//                         ${status}
//                     </div>

//                 </div>

//             </div>

//             <!-- =====================================================
//                  Organization
//                  ===================================================== -->

//             <div class="employee-drawer-section">

//                 <div class="employee-drawer-section-title">

//                     <i class="bi bi-building"></i>

//                     Organization

//                 </div>

//                 <div class="employee-drawer-grid">

//                     ${this.detail(
//                 'Organization',
//                 data.organization_name
//             )}

//                     ${this.detail(
//                 'Branch',
//                 data.branch_name
//             )}

//                     ${this.detail(
//                 'Department',
//                 data.department_name
//             )}

//                     ${this.detail(
//                 'Designation',
//                 data.designation_name
//             )}

//                 </div>

//             </div>

//             <!-- =====================================================
//                  Employment
//                  ===================================================== -->

//             <div class="employee-drawer-section">

//                 <div class="employee-drawer-section-title">

//                     <i class="bi bi-briefcase"></i>

//                     Employment

//                 </div>

//                 <div class="employee-drawer-grid">

//                     ${this.detail(
//                 'Employee Code',
//                 data.employee_code
//             )}

//                     ${this.detail(
//                 'Joining Date',
//                 joiningDate
//             )}

//                     ${this.detail(
//                 'Reporting Manager',
//                 data.reporting_manager_name
//             )}

//                 </div>

//             </div>

//             <!-- =====================================================
//                  Contact
//                  ===================================================== -->

//             <div class="employee-drawer-section">

//                 <div class="employee-drawer-section-title">

//                     <i class="bi bi-person-lines-fill"></i>

//                     Contact

//                 </div>

//                 <div class="employee-drawer-grid">

//                     ${this.detail(
//                 'Email',
//                 data.email
//             )}

//                     ${this.detail(
//                 'Phone',
//                 data.phone
//             )}

//                 </div>

//             </div>

//             <!-- =====================================================
//                  Full Profile
//                  ===================================================== -->

//             <div class="employee-drawer-full-view">

//                 <a
//                     href="${APP.baseUrl} employees/view/${employeeId}"
//                     class="btn employee-drawer-full-view-btn"
//                 >

//                     <span>
//                         <i class="bi bi-person-vcard me-2"></i>
//                         View Full Employee Profile
//                     </span>

//                     <i class="bi bi-arrow-right"></i>

//                 </a>

//             </div>

//         `;
//     },

//     detail(label, value) {
//         if (
//             value === null ||
//             value === undefined ||
//             value === ''
//         ) {
//             value = '-';
//         }

//         return `

//             <div class="employee-drawer-detail">

//                 <span>
//                     ${this.escapeHtml(label)}
//                 </span>

//                 <strong>
//                     ${this.escapeHtml(
//             String(value)
//         )}
//                 </strong>

//             </div>

//         `;
//     },

//     formatDate(value) {
//         if (!value) {
//             return '-';
//         }

//         const date =
//             new Date(value);

//         if (
//             Number.isNaN(
//                 date.getTime()
//             )
//         ) {
//             return value;
//         }

//         return date.toLocaleDateString(
//             'en-GB',
//             {
//                 day: '2-digit',
//                 month: 'short',
//                 year: 'numeric'
//             }
//         );
//     },

//     getInitials(name) {
//         return name
//             .trim()
//             .split(/\s+/)
//             .slice(0, 2)
//             .map(
//                 part =>
//                     part.charAt(0)
//                         .toUpperCase()
//             )
//             .join('');
//     },

//     getPhotoUrl(photo) {

//         if (!photo) {
//             return '';
//         }

//         if (
//             photo.startsWith('http://') ||
//             photo.startsWith('https://') ||
//             photo.startsWith('/')
//         ) {
//             return photo;
//         }

//         return `${APP.baseUrl}uploads/employees/${photo}`;
//     },

//     escapeHtml(value) {
//         return String(value ?? '')
//             .replace(
//                 /&/g,
//                 '&amp;'
//             )
//             .replace(
//                 /</g,
//                 '&lt;'
//             )
//             .replace(
//                 />/g,
//                 '&gt;'
//             )
//             .replace(
//                 /"/g,
//                 '&quot;'
//             )
//             .replace(
//                 /'/g,
//                 '&#039;'
//             );
//     },

//     escapeAttribute(value) {
//         return this.escapeHtml(
//             value
//         );
//     }
// };


const EmployeeDrawer = {

    render(data) {

        const employeeId =
            parseInt(data.id, 10);

        const fullName =
            data.full_name ||
            [
                data.first_name,
                data.last_name
            ]
                .filter(Boolean)
                .join(' ') ||
            'Employee';

        const initials =
            this.getInitials(fullName);

        const status =
            data.status === 'active'

                ? `
                    <span class="status-badge status-active">
                        Active
                    </span>
                `

                : `
                    <span class="status-badge status-inactive">
                        Inactive
                    </span>
                `;


        return `

            <div class="employee-drawer-profile">

                <div class="employee-drawer-avatar">

                    ${data.profile_photo

                ? `
                                <img
                                    src="${this.escapeAttribute(
                    this.getPhotoUrl(
                        data.profile_photo
                    )
                )}"
                                    alt="${this.escapeAttribute(
                    fullName
                )}">
                            `

                : `
                                <span>
                                    ${this.escapeHtml(
                    initials
                )}
                                </span>
                            `
            }

                </div>


                <div class="employee-drawer-profile-info">

                    <h5>
                        ${this.escapeHtml(fullName)}
                    </h5>


                    ${data.employee_code

                ? `
                                <div class="employee-drawer-code">
                                    ${this.escapeHtml(
                    data.employee_code
                )}
                                </div>
                            `

                : ''
            }


                    ${data.designation_name

                ? `
                                <div class="employee-drawer-designation">
                                    ${this.escapeHtml(
                    data.designation_name
                )}
                                </div>
                            `

                : ''
            }


                    <div class="mt-2">
                        ${status}
                    </div>

                </div>

            </div>


            <div class="employee-drawer-section">

                <div class="employee-drawer-section-title">

                    <i class="bi bi-building"></i>

                    Organization

                </div>


                <div class="employee-drawer-grid">

                    ${this.detail(
                'Organization',
                data.organization_name
            )}

                    ${this.detail(
                'Branch',
                data.branch_name
            )}

                    ${this.detail(
                'Department',
                data.department_name
            )}

                    ${this.detail(
                'Designation',
                data.designation_name
            )}

                </div>

            </div>


            <div class="employee-drawer-section">

                <div class="employee-drawer-section-title">

                    <i class="bi bi-briefcase"></i>

                    Employment

                </div>


                <div class="employee-drawer-grid">

                    ${this.detail(
                'Employee Code',
                data.employee_code
            )}

                    ${this.detail(
                'Joining Date',
                this.formatDate(
                    data.joining_date
                )
            )}

                    ${this.detail(
                'Reporting Manager',
                data.reporting_manager_name
            )}

                </div>

            </div>


            <div class="employee-drawer-section">

                <div class="employee-drawer-section-title">

                    <i class="bi bi-person-lines-fill"></i>

                    Contact

                </div>


                <div class="employee-drawer-grid">

                    ${this.detail(
                'Email',
                data.email
            )}

                    ${this.detail(
                'Phone',
                data.phone
            )}

                </div>

            </div>


            <div class="employee-drawer-full-view">

                <a
                    href="${APP.baseUrl}employees/view/${employeeId}"
                    class="btn employee-drawer-full-view-btn">

                    <span>

                        <i class="bi bi-person-vcard me-2"></i>

                        View Full Employee Profile

                    </span>


                    <i class="bi bi-arrow-right"></i>

                </a>

            </div>

        `;
    },


    detail(label, value) {

        if (
            value === null ||
            value === undefined ||
            value === ''
        ) {
            value = '-';
        }


        return `

            <div class="employee-drawer-detail">

                <span>
                    ${this.escapeHtml(label)}
                </span>

                <strong>
                    ${this.escapeHtml(
            String(value)
        )}
                </strong>

            </div>

        `;
    },


    formatDate(value) {

        if (!value) {
            return '-';
        }


        const date =
            new Date(value);


        if (
            Number.isNaN(
                date.getTime()
            )
        ) {
            return this.escapeHtml(
                String(value)
            );
        }


        return date.toLocaleDateString(
            'en-GB',
            {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }
        );
    },


    getInitials(name) {

        const parts =
            String(name)
                .trim()
                .split(/\s+/)
                .filter(Boolean);


        if (!parts.length) {
            return 'E';
        }


        if (parts.length === 1) {

            return parts[0]
                .substring(0, 2)
                .toUpperCase();
        }


        return (
            parts[0].charAt(0) +
            parts[parts.length - 1].charAt(0)
        ).toUpperCase();
    },


    getPhotoUrl(photo) {

        if (!photo) {
            return '';
        }


        if (
            photo.startsWith('http://') ||
            photo.startsWith('https://') ||
            photo.startsWith('/')
        ) {
            return photo;
        }


        return `${APP.baseUrl}uploads/employees/${photo}`;
    },


    escapeHtml(value) {

        return String(value ?? '')
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


    escapeAttribute(value) {

        return this.escapeHtml(value);
    }

};