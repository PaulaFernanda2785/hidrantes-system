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

    function createSingleSubmitController() {
        const forms = Array.from(document.querySelectorAll('form')).filter((form) => {
            const method = (form.getAttribute('method') || 'GET').toUpperCase();
            const mode = (form.dataset.singleSubmit || '').toLowerCase();

            return method === 'POST' && mode !== 'false';
        });

        if (forms.length === 0) {
            return null;
        }

        const enhanceSubmitter = function (form, submitter) {
            if (!submitter || !('disabled' in submitter)) {
                return;
            }

            const processingText = form.dataset.submitProcessingText
                || submitter.dataset.processingText
                || 'Processando...';

            submitter.disabled = true;
            submitter.classList.add('is-submitting');
            submitter.setAttribute('aria-busy', 'true');

            if (submitter.tagName === 'BUTTON') {
                if (!submitter.dataset.originalHtml) {
                    submitter.dataset.originalHtml = submitter.innerHTML;
                }

                submitter.textContent = processingText;

                const spinner = document.createElement('span');
                spinner.className = 'submit-loading-indicator';
                spinner.setAttribute('aria-hidden', 'true');
                submitter.appendChild(spinner);
                return;
            }

            if (submitter.tagName === 'INPUT') {
                if (!submitter.dataset.originalValue) {
                    submitter.dataset.originalValue = submitter.value;
                }

                submitter.value = processingText;
            }
        };

        forms.forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.isSubmitting === '1') {
                    event.preventDefault();
                    return;
                }

                const submitter = event.submitter
                    || form.querySelector('button[type="submit"], input[type="submit"]');

                form.dataset.isSubmitting = '1';
                enhanceSubmitter(form, submitter);
            });
        });

        return {
            formsCount: forms.length,
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
        createSingleSubmitController();
    });
}());
