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
                scrim: 'rgba(115, 115, 115, 0.56)',
                error: '#ba1a1a',
            },
            borderRadius: {
                sm: '16px',
                DEFAULT: '1rem',
                md: '24px',
                lg: '32px',
                xl: '48px',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                display: ['80px', { lineHeight: '80px', fontWeight: '600' }],
                'display-mobile': ['48px', { lineHeight: '52px', fontWeight: '600' }],
                'headline-lg': ['56px', { lineHeight: '56px', fontWeight: '600' }],
                'headline-md': ['44px', { lineHeight: '50px', fontWeight: '600' }],
                'headline-sm': ['32px', { lineHeight: '36px', fontWeight: '600' }],
                'title-lg': ['24px', { lineHeight: '30px', fontWeight: '600' }],
                'title-md': ['20px', { lineHeight: '26px', fontWeight: '600' }],
                'body-lead': ['20px', { lineHeight: '28px', fontWeight: '300' }],
                'body-md': ['16px', { lineHeight: '22px', fontWeight: '400' }],
                'body-sm': ['14px', { lineHeight: '20px', fontWeight: '400' }],
                action: ['16px', { lineHeight: '22px', fontWeight: '600' }],
                'badge-label': ['12px', { lineHeight: '16px', fontWeight: '600' }],
                caption: ['12px', { lineHeight: '16px', fontWeight: '400' }],
            },
            spacing: {
                'space-xs': '0.25rem',
                'space-sm': '0.5rem',
                'space-md': '1rem',
                'space-lg': '1.5rem',
                'space-xl': '2rem',
                gutter: '1rem',
                'gutter-md': '1.5rem',
                'gutter-lg': '2rem',
                margin: '1rem',
                'margin-md': '2rem',
                'margin-lg': '3rem',
            },
        },
    },

    plugins: [forms],
};
