import daisyui from "daisyui";
import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    plugins: [daisyui],
    daisyui: {
        oklch: false,
    },
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.jsx",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Roboto Condensed", ...defaultTheme.fontFamily.sans],
                rammetto: ["Rammetto One", "sans-serif"],
            },
            colors: {
                TheGreen: "#3DF400",
                TheRed: "#FC0303",
                TheBlue: "#0047BA",
            },
            animation: {
                'fade-in-down': 'fade-in-down 0.3s ease-in-out',
                'fade-out-up': 'fade-out-up 0.3s ease-in-out',
            },
            keyframes: {
                'fade-in-down': {
                    '0%': {
                        opacity: '0',
                        transform: 'translateY(-10px)'
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateY(0)'
                    },
                },
                'fade-out-up': {
                    '0%': {
                        opacity: '1',
                        transform: 'translateY(0)'
                    },
                    '100%': {
                        opacity: '0',
                        transform: 'translateY(-10px)'
                    },
                }
            },
            backgroundImage: {
                "gradient-to-r-base-200":
                    "linear-gradient(to right, #0D1335, #26389B)", // define a gradient background for base-200
                "gradient-to-r-base-300":
                    "linear-gradient(to right, #0D1335, #162059)", // define a gradient background for base-200
            },
            // Add custom widths here
            width: {
                'custom-width-1': '43vw', // Example custom width
                'custom-width-2': '41vw', // Additional custom width if needed
            },
            screens: {
                'screen-1089': '1089px', // Custom screen size for 1089px
            },
        },
    },
    plugins: [daisyui],
    daisyui: {
        themes: [
            {
                dark: {
                    ...require("daisyui/src/theming/themes")["dark"],
                    "base-100": "#303030",
                    "base-200": "#0D1335", //dark blue
                    "base-content": "#FFFFFF",
                    primary: "#dc2626",
                },
            },
        ],
    },
};
