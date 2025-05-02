<template>
    <div :class="fieldClass">
        <InputLabel
            v-if="label"
            :label="label"
            class="fw-500"
            :required="required"
        />
        <div
            class="input"
            :class="[
                `input-${size}`,
                error && error.length !== 0 ? 'error' : '',
            ]"
        >
            <input
                :id="id"
                :type="type"
                :value="modelValue"
                :required="required"
                :class="[focus]"
                :placeholder="placeholder"
                @input="updateValue($event)"
            />
            <template v-if="type === 'password'">
                <div
                    class="-mr-2 select-none cursor-pointer"
                    tabindex="0"
                    @click="togglePassword"
                >
                </div>
            </template>
        </div>
        <InputError v-if="error" :message="error" />
    </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import type { InputTypeHTMLAttribute } from "vue";

import InputLabel from "./InputLabel.vue";
import InputError from "./InputError.vue";
interface Props {
    modelValue: string | number;
    id: string;
    type?: InputTypeHTMLAttribute;
    label?: string;
    placeholder?: string;
    fieldClass?: string;
    error?: string[];
    required?: boolean;
    size?: "xs" | "sm" | "md" | "lg";
}

const props = withDefaults(defineProps<Props>(), {
    type: "text",
    size: "md",
});

const showEyeIcon = ref<boolean>(true);
const id = ref<string>(props.id);

const emit = defineEmits(["update:modelValue"]);

const updateValue = (event) => {
    emit("update:modelValue", event.target?.value);
};

const focus = ref();

function togglePassword() {
    const password = document.getElementById(id.value);
    let type = password?.getAttribute("type");
    password?.setAttribute("type", type === "password" ? "text" : "password");
    showEyeIcon.value = !showEyeIcon.value;
}
</script>
