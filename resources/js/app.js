import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

document.addEventListener('DOMContentLoaded', () => {

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    const openButton = document.getElementById('open-sidebar');
    const closeButton = document.getElementById('close-sidebar');

    const sidebarLinks = document.querySelectorAll('.sidebar-link');


    function openSidebar() {

        sidebar.classList.remove('-translate-x-full');

        overlay.classList.remove('hidden');

    }


    function closeSidebar() {

        sidebar.classList.add('-translate-x-full');

        overlay.classList.add('hidden');

    }


    if (openButton) {

        openButton.addEventListener('click', openSidebar);

    }


    if (closeButton) {

        closeButton.addEventListener('click', closeSidebar);

    }


    if (overlay) {

        overlay.addEventListener('click', closeSidebar);

    }


    sidebarLinks.forEach((link) => {

        link.addEventListener('click', () => {

            if (window.innerWidth < 1024) {

                closeSidebar();

            }

        });

    });


    window.addEventListener('resize', () => {

        if (window.innerWidth >= 1024) {

            sidebar.classList.remove('-translate-x-full');

            overlay.classList.add('hidden');

        } else {

            sidebar.classList.add('-translate-x-full');

        }

    });

});