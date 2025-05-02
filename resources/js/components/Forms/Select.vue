<template>
    <div :id="id" class="w-full">
        <InputLabel v-if="label" :label="label" :required="required" />

        <div class="relative">
            <div
                class="select cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 flex justify-between items-center"
                :class="[
                    error && error.length !== 0 ? 'error' : '',
                    `select-${size}`,
                    `input-${size}`,
                ]"
                tabindex="0"
                @click="toggleDropdown"
                @keydown.space.prevent="toggleDropdown"
            >
                {{ displayValue }}
                <i
                    class="fa fa-angle-down"
                    :class="[isOpen ? 'rotate-180' : 'rotate-default']"
                    ></i>
            </div>

            <div v-if="isOpen">
                <ul class="select-dropdown max-h-60 overflow-auto select-none">
                    <li
                        v-for="option in dropdownOptions"
                        :key="option.key"
                        class="cursor-pointer px-4 py-2 border-gray-200 hover:bg-gray-100  mx-[5%] border-b last:border-0"
                        @click="selectOption(option)"
                    >
                        <div class="flex items-center">
                            <slot :option="option">
                                {{ option.value }}
                            </slot>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <InputError v-if="error && error.length !== 0" :message="error" />
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import InputLabel from "./InputLabel.vue";
import InputError from "./InputError.vue";

type ObjectOption = { [key: string]: any };

interface Props {
    id?: string;
    modelValue: any;
    options: ObjectOption[];
    label?: string;
    error?: string[];
    placeholder?: string;
    required?: boolean;
    size?: "xs" | "sm" | "md" | "lg";
}

const props = withDefaults(defineProps<Props>(), {
    size: "md",
    required: false,
});

const dropdownOptions = ref<any>(props.options);

const emit = defineEmits<{
    "update:modelValue": [value: any];
    change: [value: any];
}>();

const isOpen = ref(false);

const displayValue = computed(() => {
    if (!props.modelValue) return props.placeholder || "Select an option...";

    const selectedOption = dropdownOptions.value.find(
        (option) => option.key == props.modelValue
    );
    return selectedOption ? selectedOption.value : props.placeholder;
});

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const selectOption = (option: ObjectOption) => {
    const value = option.key;
    emit("update:modelValue", value);
    emit("change", value);
    isOpen.value = false;
};
</script>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
    transition: transform 0.2s ease-in-out;
}

.rotate-default {
    transition: transform 0.2s ease-in-out;
}
</style>
