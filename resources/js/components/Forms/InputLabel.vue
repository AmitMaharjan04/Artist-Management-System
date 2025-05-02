<template>
    <label
        class="mb-2 block text-black dark:text-white"
        :class="labelClass"
        :for="id"
    >
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
    </label>
</template>

<script setup lang="ts">
interface Props {
    label: string;
    id?: string;
    labelClass?: string;
    required?: boolean;
}

withDefaults(defineProps<Props>(), {});
</script>
