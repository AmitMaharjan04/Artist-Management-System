<template>
    <button
        :type="type"
        :class="[
             $attrs.class,
            'button',
            `button-${color}`,
            `button-${size}`,
        ]"
        :disabled="disabled"
        :aria-disabled="disabled ? 'true' : undefined"
        @click="$emit('click', $event)"
    >
        <span class="flex gap-2 items-center justify-center">
            <span>{{ label }}</span>
            <i class="fa fa-spinner fa-spin" v-if="loading"></i>
        </span>
    </button>
</template>

<script setup lang="ts">
interface ButtonProps {
    label: string;
    disabled?: boolean;
    loading?: boolean;
    color?:
        | "primary"
        | "danger"
        | "success";
    size?: "xs" | "sm" | "md" | "lg" | "xl";
    type?: "button" | "submit";
}

const _props = withDefaults(defineProps<ButtonProps>(), {
    disabled: false,
    loading: false,
    color: "primary",
    size: "md",
    type: "button",
});

const _emit = defineEmits(["click"]);
</script>
