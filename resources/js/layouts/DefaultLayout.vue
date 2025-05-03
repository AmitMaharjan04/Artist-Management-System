<template>
    <div>
        <div
            class="flex h-screen overflow-hidden"
        >   <!-- <Menu /> -->
            <div
                class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden"
            >
                <!-- <HeaderArea /> -->
                <HeaderArea />
                <main>
                    <div class="mx-auto p-4 md:p-6 2xl:p-8">
                        <router-view />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import HeaderArea from "@/components/HeaderArea.vue";
</script>
