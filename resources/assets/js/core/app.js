(function () {
    if (window.HidrantesApp) {
        return;
    }

    function createSidebarController() {
        const sidebar = document.getElementById('app-sidebar');
        const toggleButton = document.querySelector('[data-sidebar-toggle]');
        const closeButtons = document.querySelectorAll('[data-sidebar-close]');
        const backdrop = document.querySelector('.sidebar-backdrop');
        const mobileQuery = window.matchMedia('(max-width: 960px)');

        if (!sidebar || !toggleButton || closeButtons.length === 0 || !backdrop) {
            return null;
        }

        const closeSidebar = function () {
            document.body.classList.remove('has-sidebar-open');
            toggleButton.setAttribute('aria-expanded', 'false');
            backdrop.hidden = true;
        };

        const openSidebar = function () {
            if (!mobileQuery.matches) {
                return;
            }

            document.body.classList.add('has-sidebar-open');
            toggleButton.setAttribute('aria-expanded', 'true');
            backdrop.hidden = false;
        };

        const toggleSidebar = function () {
            if (document.body.classList.contains('has-sidebar-open')) {
                closeSidebar();
                return;
            }

            openSidebar();
        };

        toggleButton.addEventListener('click', toggleSidebar);

        closeButtons.forEach((button) => {
            button.addEventListener('click', closeSidebar);
        });

        sidebar.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', function () {
                if (mobileQuery.matches) {
                    closeSidebar();
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        const handleViewportChange = function (event) {
            if (!event.matches) {
                closeSidebar();
            }
        };

        if (typeof mobileQuery.addEventListener === 'function') {
            mobileQuery.addEventListener('change', handleViewportChange);
        } else if (typeof mobileQuery.addListener === 'function') {
            mobileQuery.addListener(handleViewportChange);
        }

        closeSidebar();

        return {
            open: openSidebar,
            close: closeSidebar,
        };
    }

    window.HidrantesApp = {
        onReady(callback) {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', callback, { once: true });
                return;
            }

            callback();
        },
    };

    window.HidrantesApp.onReady(function () {
        createSidebarController();
    });
}());
