/**
 * Forty Miles West Theme - Main JavaScript
 *
 * @package FMW
 */

(function () {
    'use strict';

    /**
     * Initialise GSAP ScrollTrigger
     */
    function initScrollTrigger() {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
            return;
        }

        gsap.registerPlugin(ScrollTrigger);

        window.addEventListener('load', function () {
            ScrollTrigger.refresh();
        });
    }

    /**
     * Smooth scroll for anchor links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
            anchor.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    }

    /**
     * Handle AJAX form submissions
     */
    function initForms() {
        document.querySelectorAll('[data-ajax-form]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(form);
                const action = form.dataset.action || 'fmw_contact_form';
                const submitButton = form.querySelector('[type="submit"]');
                const messageContainer = form.querySelector('[data-form-message]');

                formData.append('action', action);
                formData.append('nonce', fmw.nonce);

                if (submitButton) submitButton.disabled = true;
                if (messageContainer) {
                    messageContainer.textContent = '';
                    messageContainer.className = '';
                }

                fetch(fmw.ajaxUrl, { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (messageContainer) {
                            messageContainer.textContent = data.data.message;
                            messageContainer.className = data.success ? 'form-success' : 'form-error';
                        }
                        if (data.success) form.reset();
                    })
                    .catch(() => {
                        if (messageContainer) {
                            messageContainer.textContent = 'An error occurred. Please try again.';
                            messageContainer.className = 'form-error';
                        }
                    })
                    .finally(() => {
                        if (submitButton) submitButton.disabled = false;
                    });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initScrollTrigger();
        initSmoothScroll();
        initForms();
    });
})();
