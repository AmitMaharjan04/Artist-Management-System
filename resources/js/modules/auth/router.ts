import type { RouteRecordRaw } from "vue-router";

export const authRoutes: RouteRecordRaw[] = [
    {
        path: "/login",
        name: "LoginPage",
        component: () => import("./pages/Login.vue"),
        meta: {
            title: "Login - Artist Management System",
        },
    },
    {
        path: "/register",
        name: "RegisterPage",
        component: () => import("./pages/Register.vue"),
        meta: {
            title: "Register - Artist Management System",
        },
    },
];
