/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./app/View/Components/**/*.php",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#E8EEF4',
                    100: '#D1DDE9',
                    200: '#A3BBD3',
                    300: '#7599BD',
                    400: '#4777A7',
                    500: '#1B3A5C',
                    600: '#162E4A',
                    700: '#112338',
                    800: '#0B1725',
                    900: '#060C13',
                },
                accent: {
                    400: '#D4A84B',
                    500: '#C8963E',
                    600: '#A07830',
                },
                success: '#27AE60',
                warning: '#F39C12',
                danger: '#C0392B',
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
