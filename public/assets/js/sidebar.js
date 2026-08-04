document.addEventListener('DOMContentLoaded', function () {

    const toggleButton = document.querySelector('.btn-toggle-sidebar');
    const sidebar = document.querySelector('.app-sidebar');
    const navbar = document.querySelector('.app-navbar');
    const main = document.querySelector('.app-main');

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

});