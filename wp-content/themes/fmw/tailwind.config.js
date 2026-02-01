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
                primary: '#c75a2a',
                secondary: '#1e293b'
            }
        }
    },
    plugins: []
};
