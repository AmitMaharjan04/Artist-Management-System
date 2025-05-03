import type { RouteRecordRaw } from "vue-router";

export const authRoutes: RouteRecordRaw[] = [
    {
        path: "/user",
        name: "UserPage",
        component: () => import("./pages/UserIndex.vue"),
        meta: {
            title: "User - Artist Management System",
        },
    },
];
