<template>
    <p
        v-if="message.length !== 0"
        class="mt-2 text-sm text-red-500 dark:text-red-500"
        :class="errorClass"
    >
        {{ message[0] }}
    </p>
</template>

<script setup lang="ts">
interface Props {
    message: string[];
    errorClass?: string;
}

withDefaults(defineProps<Props>(), {});
</script>
