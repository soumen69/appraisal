document.addEventListener('DOMContentLoaded', function () {

    'use strict';

    const toggleButton = document.querySelector('.btn-toggle-sidebar');

    const sidebar = document.querySelector('.app-sidebar');

    const navbar = document.querySelector('.app-navbar');

    const main = document.querySelector('.app-main');

    const sidebarSearch = document.getElementById('sidebarSearch');

    if (toggleButton && sidebar && navbar && main) {

        toggleButton.addEventListener('click', function () {
            const collapsed = sidebar.classList.toggle('collapsed');
            navbar.classList.toggle('expanded', collapsed);
            main.classList.toggle('expanded', collapsed);
            localStorage.setItem('sidebar-collapsed', collapsed ? 'true' : 'false');
        }
        );

        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            sidebar.classList.add('collapsed');
            navbar.classList.add('expanded');
            main.classList.add('expanded');
        }
    }

    if (sidebarSearch) {

        sidebarSearch.addEventListener('input', function () {
            const keyword = this.value.trim().toLowerCase();
            document.querySelectorAll('.sidebar-menu > li.sidebar-item')
                .forEach(function (item) {

                    const text =
                        item.textContent
                            .trim()
                            .toLowerCase();

                    item.style.display =
                        !keyword ||
                            text.includes(keyword)
                            ? ''
                            : 'none';
                });
        }
        );


        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key !== '/' ||
                    event.ctrlKey ||
                    event.metaKey ||
                    event.altKey
                ) {
                    return;
                }

                const target =
                    event.target;

                if (
                    target instanceof HTMLInputElement ||
                    target instanceof HTMLTextAreaElement ||
                    target instanceof HTMLSelectElement ||
                    target.isContentEditable
                ) {
                    return;
                }

                event.preventDefault();

                sidebarSearch.focus();
            }
        );
    }

});