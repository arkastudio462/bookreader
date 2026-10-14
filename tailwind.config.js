import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                ink: '#141414',
                'ink-soft': '#262626',
                canvas: '#ffffff',
                soft: '#f3f3f3',
                field: '#f0f0f0',
                'hairline-soft': '#f0f0f0',
                hair: '#e0e0e0',
                muted: '#707070',
                faint: '#adadad',
                accent: '#0066ff',
            },
            borderRadius: {
                sm: '16px',
                md: '24px',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
