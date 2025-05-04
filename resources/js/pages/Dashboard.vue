<template>
    <SongIndex v-if="role === 'artist'" />
    <Tabs v-else
        :tabs="tabs.flat()"
        :tab-length="tabs.length"
        :show-id="activeTab"
        @tabChanged="changeTab"
    />
</template>

<script setup lang="ts">
import { computed, defineAsyncComponent, onMounted, ref } from "vue";
import ApiList from "@/api/apiList";
import { useAuthStore } from "@/stores/authStore";
import Tabs from "@/components/Tabs.vue";
import SongIndex from "@/modules/song/pages/SongIndex.vue";
import { USER_ROLE } from "../constants/constant";
const authStore = useAuthStore();

const role = authStore.user?.role;
let activeTab = authStore.activeTab ?? undefined;
if(role != USER_ROLE.ARTIST) {
    activeTab = (activeTab === '')? (role === USER_ROLE.SUPER_ADMIN ? "user" : "artist") : activeTab;
    authStore.activeTab = activeTab;
}
const tabs = computed(() => [
    ...(role === USER_ROLE.SUPER_ADMIN
        ? [
              {
                  id: "user",
                  title: "User",
                  content: defineAsyncComponent(
                      () => import("@/modules/user/pages/UserIndex.vue")
                  ),
              },
          ]
        : []),
    [
        {
            id: "artist",
            title: "Artist",
            content: defineAsyncComponent(
                () => import("@/modules/artist/pages/ArtistIndex.vue")
            ),
        },
    ],
]);

const changeTab = (tabId: string | number) => {
    authStore.activeTab = tabId as "user" | "artist";
    activeTab = authStore.activeTab;
};

</script>
