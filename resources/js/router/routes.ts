import { USER_ROLE } from "@/constants/constant";
import { authRoutes } from "@/modules/auth/router";
import { songRoutes } from "@/modules/song/router";
import type { RouteRecordRaw } from "vue-router";

const notLayoutRoutes: RouteRecordRaw[] = [...authRoutes];

const layoutRoutes: RouteRecordRaw[] = [...songRoutes];
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
            },
            {
                path: "/songs/artist/:id",
                name: "ArtistSongPage",
                component: () =>
                    import("@/modules/song/pages/ArtistSongIndex.vue"),
                meta: {
                    title: "Artist Song - Artist Management System",
                    requiresAuth: true,
                    authenticateWhen: [
                        USER_ROLE.SUPER_ADMIN,
                        USER_ROLE.ARTIST_MANAGER,
                    ],
                },
            },
        ],
    },
    ...notLayoutRoutes,
];

export default routes;
