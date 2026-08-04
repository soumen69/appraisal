document.addEventListener('DOMContentLoaded', function () {

    const toggleButton = document.querySelector('.btn-toggle-sidebar');
    const sidebar = document.querySelector('.app-sidebar');
    const navbar = document.querySelector('.app-navbar');
    const main = document.querySelector('.app-main');
    const sidebarSearch = document.getElementById('sidebarSearch');

    if (!toggleButton) return;

    toggleButton.addEventListener('click', function () {

        sidebar.classList.toggle('collapsed');
        navbar.classList.toggle('expanded');
        main.classList.toggle('expanded');

        localStorage.setItem(
            'sidebar-collapsed',
            sidebar.classList.contains('collapsed')
        );

    });

    if (localStorage.getItem('sidebar-collapsed') === 'true') {

        sidebar.classList.add('collapsed');
        navbar.classList.add('expanded');
        main.classList.add('expanded');

    }

    if (sidebarSearch) {

        sidebarSearch.addEventListener('keyup', function () {

            const keyword = this.value.toLowerCase();

            document.querySelectorAll('.sidebar-menu li').forEach(function (item) {

                if (item.classList.contains('menu-title')) {

                    return;

                }

                item.style.display = item.textContent
                    .toLowerCase()
                    .includes(keyword)
                    ? ''
                    : 'none';

            });

        });

    }

});