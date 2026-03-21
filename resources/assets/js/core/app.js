(function () {
    if (window.HidrantesApp) {
        return;
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
}());
