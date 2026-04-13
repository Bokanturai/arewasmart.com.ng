/**
 * BVN UX Enhancements
 * Handles global form validation, loading states, and input masking.
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        // 1. Bootstrap 5 Form Validation handler
        const forms = document.querySelectorAll('.needs-validation');

        Array.from(forms).forEach(form => {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    form.classList.add('was-validated');
                } else {
                    // Form is valid, show loading state
                    handleFormSubmission(form);
                }
            }, false);
        });

        // 2. Numeric-only input enforcement for specific classes
        const numericInputs = document.querySelectorAll('.numeric-only');
        numericInputs.forEach(input => {
            input.addEventListener('input', function (e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    });

    /**
     * Handles the UI changes when a form is being submitted
     * @param {HTMLFormElement} form 
     */
    function handleFormSubmission(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (!submitBtn) return;

        // Save original content
        const originalContent = submitBtn.innerHTML;
        const loadingText = submitBtn.getAttribute('data-loading-text') || 'Processing...';

        // Set loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            ${loadingText}
        `;

        // Safety timeout to re-enable button if something goes wrong (e.g., page doesn't refresh)
        // This is useful for development but also good as a fallback.
        setTimeout(() => {
            if (submitBtn.disabled) {
                // Check if we are still on the same page and form hasn't triggered a real refresh
                // (Though usually for traditional POST, the page will reload)
            }
        }, 30000); 
    }
})();
