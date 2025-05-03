<script setup lang="ts">
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/authStore";

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
  try {
    await authStore.clearSession();
    router.push("/login");
  } catch (error) {
    console.error("Logout failed:", error);
  }
};
</script>

<template>
  <header
    class="bg-gray-800 sticky top-0 z-[99] flex w-full h-16 drop-shadow-1 dark:bg-boxdark dark:drop-shadow-none border-b-2"
  >
    <div class="flex flex-grow items-center justify-between px-4 py-4 md:px-6 2xl:px-8">
      <!-- Left Side: Logo and Title -->
      <div class="flex items-center gap-2">
        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary">
          <span class="text-xl font-bold text-white">A</span>
        </div>
        <h1 class="hidden text-xl font-bold text-white dark:text-white sm:block">
          Dashboard
        </h1>
      </div>

      <div class="flex items-center gap-3">
        <button
          @click="handleLogout"
          class="flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white transition hover:bg-opacity-90"
        >
          <svg
            class="fill-current"
            width="16"
            height="16"
            viewBox="0 0 16 16"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M8.5 7.5V1.5H1.5V14.5H8.5V8.5H10.5V10.5L14.5 7.5L10.5 4.5V6.5H8.5V7.5Z"
            ></path>
          </svg>
          Logout
        </button>
      </div>
    </div>
  </header>
</template>