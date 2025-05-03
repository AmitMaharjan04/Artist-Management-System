import colors from "tailwindcss/colors";
import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./resources/**/*.{html,js,vue}",
        "./node_modules/tw-elements/js/**/*.js",
    ],
    theme: {
        screens: {
            ...defaultTheme.screens,
        },
        extend: {
            colors: {

                current: "currentColor",
                transparent: "transparent",
                white: "#FFFFFF",
                "boxdark-2": "#00000000",
                strokedark: "#2E3A47",
                boxdark: "#24303F",
                black: {
                    ...colors.black,
                    DEFAULT: "#1C2434",
                    2: "#010101",
                },
                red: {
                    ...colors.red,
                    DEFAULT: "#FB5454",
                },
                body: "#64748B",
                stroke: "#E2E8F0",
                "form-strokedark": "#3d4d60",
                "form-input": "#1d2a39",
            },
        },
    },
    
    plugins: [require("tw-elements/plugin.cjs")],
};
