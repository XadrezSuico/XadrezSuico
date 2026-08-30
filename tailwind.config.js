/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/layouts/v2/**/*.blade.php',
        './resources/views/components/v2/**/*.blade.php',
        './resources/views/home/**/*.blade.php',
        './resources/views/home.blade.php',
        './resources/views/auth/login.blade.php',
        './resources/views/layouts/v2/guest.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    DEFAULT: '#6B21A8',
                    light: '#9333EA',
                    dark: '#581C87',
                    surface: '#F5F3FF',
                    sidebar: '#4C1D95',
                },
            },
        },
    },
    plugins: [],
};
