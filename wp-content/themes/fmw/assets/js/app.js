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
     * Custom Cursor with Trail
     */
    function initCustomCursor() {
        // Only on non-touch devices
        if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
            return;
        }

        // Create cursor elements
        const cursor = document.createElement('div');
        cursor.className = 'custom-cursor';
        document.body.appendChild(cursor);

        // Create trail elements
        const trailCount = 5;
        const trails = [];
        for (let i = 0; i < trailCount; i++) {
            const trail = document.createElement('div');
            trail.className = 'cursor-trail';
            trail.style.opacity = (1 - (i / trailCount)) * 0.3;
            document.body.appendChild(trail);
            trails.push({ el: trail, x: 0, y: 0 });
        }

        let mouseX = 0;
        let mouseY = 0;
        let cursorX = 0;
        let cursorY = 0;
        let isHovering = false;

        document.addEventListener('mousemove', function (e) {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });

        // Hover effect on interactive elements
        document.querySelectorAll('a, button, input, select, textarea, [role="button"]').forEach(function (el) {
            el.addEventListener('mouseenter', function () {
                isHovering = true;
                cursor.classList.add('cursor-hover');
            });
            el.addEventListener('mouseleave', function () {
                isHovering = false;
                cursor.classList.remove('cursor-hover');
            });
        });

        // Animation loop
        function animate() {
            // Smooth cursor follow
            cursorX += (mouseX - cursorX) * 0.15;
            cursorY += (mouseY - cursorY) * 0.15;

            cursor.style.left = cursorX + 'px';
            cursor.style.top = cursorY + 'px';

            // Trail animation
            let prevX = cursorX;
            let prevY = cursorY;

            trails.forEach(function (trail, index) {
                const speed = 0.15 - (index * 0.02);
                trail.x += (prevX - trail.x) * speed;
                trail.y += (prevY - trail.y) * speed;
                trail.el.style.left = trail.x + 'px';
                trail.el.style.top = trail.y + 'px';
                prevX = trail.x;
                prevY = trail.y;
            });

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
