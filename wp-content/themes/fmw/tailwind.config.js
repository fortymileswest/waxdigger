/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './*.php',
        './inc/**/*.php',
        './partials/**/*.php',
        './components/**/*.php',
        './woocommerce/**/*.php',
        './assets/js/**/*.js'
    ],
    theme: {
        container: {
            center: true,
            padding: '1rem'
        },
        extend: {
            colors: {
                dark: '#0d0d0d',
                cream: '#f0ece4',
                accent: '#25ddb3',
                'teal-dark': '#0a8c6a',
                'card-dark': '#1a1a1a',
            },
            fontFamily: {
                mono: ['JetBrains Mono', 'ui-monospace', 'SFMono-Regular', 'monospace'],
                display: ['Space Grotesk', 'system-ui', 'sans-serif'],
            },
            maxWidth: {
                site: '1440px',
            },
            letterSpacing: {
                'wider-2': '2px',
                'wider-3': '3px',
                'wider-5': '5px',
            },
            lineHeight: {
                'tight-09': '0.9',
                'tight-085': '0.85',
            },
        }
    },
    plugins: []
};
