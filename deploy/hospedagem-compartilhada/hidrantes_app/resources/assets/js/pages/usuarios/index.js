window.HidrantesApp.onReady(() => {
    const confirmForms = document.querySelectorAll('[data-confirm-submit]');

    if (confirmForms.length === 0) {
        return;
    }

    confirmForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.confirmMessage || 'Deseja continuar com esta operação?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
