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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                headline: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                body: ['Manrope', ...defaultTheme.fontFamily.sans],
                label: ['Manrope', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#8F0000',
                    container: '#FFDAD4',
                },
                'on-primary': {
                    DEFAULT: '#FFFFFF',
                    container: '#410000',
                },
                surface: {
                    DEFAULT: '#FFFBFF',
                    'container-lowest': '#FFFFFF',
                    'container-low': '#F7F2F7',
                    'container': '#F1ECF1',
                    'container-high': '#EBE6EB',
                    'container-highest': '#E5E1E6',
                },
                'on-surface': {
                    DEFAULT: '#201A19',
                    variant: '#534341',
                },
                outline: {
                    DEFAULT: '#857370',
                    variant: '#D8C2BE',
                },
                error: {
                    DEFAULT: '#BA1A1A',
                }
            }
        },
    },

    plugins: [forms],
};
