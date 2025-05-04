import type { RouteRecordRaw } from "vue-router";
import { USER_ROLE } from "@/constants/constant";

export const songRoutes: RouteRecordRaw[] = [
    {
        path: "/songs",
        name: "SongPage",
        component: () => import("./pages/SongIndex.vue"),
        meta: {
            title: "Song - Artist Management System",
            requiresAuth: true,
            // authenticateWhen: [USER_ROLE.ARTIST],
        },
    },
    {
        path: "/songs/artist/:id",
        name: "ArtistSongPage",
        component: () => import("./pages/ArtistSongIndex.vue"),
        meta: {
            title: "Artist Song - Artist Management System",
            requiresAuth: true,
            authenticateWhen: [USER_ROLE.SUPER_ADMIN, USER_ROLE.ARTIST_MANAGER],
        },
    },
];
