import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                likeBounce: {
                    "0%, 100%": { transform: "scale(1)" },
                    "25%": { transform: "scale(0.95)" },
                    "50%": { transform: "scale(1.2)" },
                    "75%": { transform: "scale(0.95)" },
                },
            },
            animation: {
                likeBounce: "likeBounce 0.3s ease-out",
            },
        },
    },

    plugins: [forms],
};
