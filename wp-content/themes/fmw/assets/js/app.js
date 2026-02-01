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

    /**
     * Page Transitions
     */
    function initPageTransitions() {
        document.querySelectorAll('a').forEach(function (link) {
            // Skip external links, anchor links, and special links
            if (
                link.hostname !== window.location.hostname ||
                link.getAttribute('href').startsWith('#') ||
                link.getAttribute('target') === '_blank' ||
                link.classList.contains('no-transition') ||
                link.closest('[x-data]') // Skip Alpine-controlled links
            ) {
                return;
            }

            link.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                const main = document.getElementById('main');

                // Don't transition for same page or form submissions
                if (!href || href === window.location.href) return;

                e.preventDefault();

                if (main) {
                    main.classList.remove('page-transition');
                    main.classList.add('page-transition-exit');
                }

                setTimeout(function () {
                    window.location.href = href;
                }, 250);
            });
        });
    }

    /**
     * Custom Cursor with Ring
     */
    function initCustomCursor() {
        // Only on non-touch devices
        if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
            return;
        }

        // Create cursor dot
        const cursor = document.createElement('div');
        cursor.className = 'custom-cursor';
        document.body.appendChild(cursor);

        // Create cursor ring
        const ring = document.createElement('div');
        ring.className = 'cursor-ring';
        document.body.appendChild(ring);

        let mouseX = 0;
        let mouseY = 0;
        let ringX = 0;
        let ringY = 0;

        document.addEventListener('mousemove', function (e) {
            mouseX = e.clientX;
            mouseY = e.clientY;

            // Dot follows instantly
            cursor.style.left = mouseX + 'px';
            cursor.style.top = mouseY + 'px';
        });

        // Hover effect on interactive elements
        document.querySelectorAll('a, button, input, select, textarea, [role="button"]').forEach(function (el) {
            el.addEventListener('mouseenter', function () {
                cursor.classList.add('cursor-hover');
                ring.classList.add('ring-hover');
            });
            el.addEventListener('mouseleave', function () {
                cursor.classList.remove('cursor-hover');
                ring.classList.remove('ring-hover');
            });
        });

        // Ring animation - slightly delayed follow
        function animate() {
            ringX += (mouseX - ringX) * 0.25;
            ringY += (mouseY - ringY) * 0.25;

            ring.style.left = ringX + 'px';
            ring.style.top = ringY + 'px';

            requestAnimationFrame(animate);
        }

        animate();

        // Hide default cursor
        document.body.style.cursor = 'none';
        document.querySelectorAll('a, button, input, select, textarea').forEach(function (el) {
            el.style.cursor = 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initScrollTrigger();
        initSmoothScroll();
        initForms();
        initPageTransitions();
        initCustomCursor();
    });
})();
