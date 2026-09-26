/* =========================================================
   Todo List — minimal vanilla JS enhancements.
   Every feature still works with JS disabled; this only adds
   confirmation prompts and auto-dismissing flash messages.
   ========================================================= */

(function () {
    'use strict';

    /**
     * Ask the user to confirm before destructive forms submit.
     */
    function wireConfirmations() {
        var forms = document.querySelectorAll('form[data-confirm]');

        Array.prototype.forEach.call(forms, function (form) {
            if (form.dataset.confirmWired === '1') return;
            form.dataset.confirmWired = '1';

            form.addEventListener('submit', function (event) {
                if (event.defaultPrevented) return;
                if (!window.confirm(form.dataset.confirm)) {
                    event.preventDefault();
                }
            });
        });
    }

    /**
     * Fade out flash messages after a few seconds.
     */
    function wireFlashDismissal() {
        var buttons = document.querySelectorAll('[data-dismiss-flash]');

        Array.prototype.forEach.call(buttons, function (button) {
            button.addEventListener('click', function () {
                var flash = button.closest('.flash');
                if (flash) flash.remove();
            });
        });

        Array.prototype.forEach.call(document.querySelectorAll('.flash'), function (flash) {
            window.setTimeout(function () {
                flash.style.transition = 'opacity .35s';
                flash.style.opacity = '0';
                window.setTimeout(function () {
                    if (flash.parentNode) flash.remove();
                }, 380);
            }, 6000);
        });
    }

    function init() {
        wireConfirmations();
        wireFlashDismissal();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
