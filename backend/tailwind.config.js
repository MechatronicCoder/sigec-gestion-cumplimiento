import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        'bg-green-500', 'bg-lime-500', 'bg-amber-500', 'bg-red-500', 'bg-gray-500',
        'text-green-600', 'text-lime-600', 'text-amber-600', 'text-red-600', 'text-gray-600',
        'bg-green-100', 'bg-lime-100', 'bg-amber-100', 'bg-red-100', 'bg-gray-100',
        'text-green-700', 'text-lime-700', 'text-amber-700', 'text-red-700', 'text-gray-700',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
