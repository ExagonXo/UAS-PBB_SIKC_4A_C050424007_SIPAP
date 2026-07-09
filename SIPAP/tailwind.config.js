import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                sipalu: {
                    dark:  '#0C4A6E',
                    base:  '#0369A1',
                    mid:   '#0EA5E9',
                    light: '#38BDF8',
                    pale:  '#E0F2FE',
                }
            }
        },
    },

    plugins: [forms],
};
