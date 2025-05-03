import type { RouteRecordRaw } from "vue-router";

export const authRoutes: RouteRecordRaw[] = [
    {
        path: "/artist",
        name: "ArtistPage",
        component: () => import("./pages/ArtistIndex.vue"),
        meta: {
            title: "Artist - Artist Management System",
        },
    },
];
