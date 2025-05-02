import { authRoutes } from "@/modules/auth/router";
import type { RouteRecordRaw } from "vue-router";

const notLayoutRoutes: RouteRecordRaw[] = [...authRoutes];

const routes: RouteRecordRaw[] = [
    {
        path: "/:pathMatch(.*)*",
        name: "ErrorPage",
        component: () => import("@/pages/NotFound.vue"),
        meta: {
            title: "404",
            requiresAuth: true,
        },
    },
    {
        path: "/",
        name: "DefaultLayout",
        component: () => import("@/layouts/DefaultLayout.vue"),
        redirect: { name: "Dashboard" },
        meta: {
            title: "Artist Management System",
            requiresAuth: true,
        },
        children: [
            {
                path: "dashboard",
                name: "Dashboard",
                component: () => import("@/pages/Dashboard.vue"),
                meta: {
                    title: "Dashboard - Artist Management System",
                },
            }
        ],
    },
    ...notLayoutRoutes,
];

export default routes;
