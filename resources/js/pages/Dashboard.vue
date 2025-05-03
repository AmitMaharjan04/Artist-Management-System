<template>
    <Tabs
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

const authStore = useAuthStore();

const initialLoading = ref(false);
const submitLoading = ref(false);
const activeTab = ref<"user" | "artist">(
    authStore.superAdmin ? "user" : "artist"
);
const tabs = computed(() => [
    ...(authStore.superAdmin
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
    activeTab.value = tabId as "user" | "artist";
};

</script>
