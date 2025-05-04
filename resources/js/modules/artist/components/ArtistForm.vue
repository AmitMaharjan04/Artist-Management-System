<template>
    <Modal
        ref="formModal"
        :title="editingId ? 'Edit Artist' : 'Add Artist'"
        size="3xl"
        :scrollable="true"
        @close="modalClosed"
    >
        <div v-if="fetchLoading" class="h-22 flex items-center justify-center">
            <Loading class="size-16" />
        </div>
        <form v-else @submit.prevent="save">
            <section class="grid sm:grid-cols-2 gap-4">
                <div v-for="form in formFields" :key="form.id">
                    <Select
                        v-if="form.type === 'select'"
                        v-model="formData[form.id]"
                        :options="form.options! as ObjectOption[]"
                        :label="form.label"
                        :error="formErrors[form.id]"
                        :placeholder="form.placeholder ?? ''"
                        :required="form.validators?.includes('required')"
                        @change="validate(form.id)"
                    />
                    <InputField
                        v-else
                        :id="form.id"
                        v-model="formData[form.id]"
                        :type="form.type"
                        :placeholder="form.placeholder"
                        :label="form.label"
                        :required="form.validators?.includes('required')"
                        :error="formErrors[form.id]"
                        @input="validate(form.id)"
                    />
                </div>
            </section>
            <div class="flex mt-5 justify-end">
                <Button
                    label="Save"
                    type="submit"
                    color="success"
                    :disabled="isDisabled"
                    :loading="submitLoading"
                />
            </div>
        </form>
    </Modal>
</template>

<script setup lang="ts">
import { computed, ref, useTemplateRef } from "vue";
import Button from "@/components/Button.vue";
import InputField from "@/components/Forms/InputField.vue";
import Modal from "@/components/Modal.vue";
import Select from "@/components/Forms/Select.vue";

import { StoreArtist, GetArtist, UpdateArtist } from "@/modules/artist/api/artist";
import { type FormField } from "@/utils/type";
import { useForm } from "@/utils/useForm";
import { Notify } from "@/utils/notify";
import Loading from "@/components/Loading.vue";

const emits = defineEmits<{
    (event: "saved"): void;
}>();

const formModal = useTemplateRef<InstanceType<typeof Modal>>("formModal");
const editingId = ref(0);
const fetchLoading = ref(false);
const isDisabled = ref(false);
const submitLoading = ref(false);
const openModal = () => {
    formModal.value?.openModal();
};

const closeModal = () => {
    formModal.value?.closeModal();
};

const modalClosed = () => {
    editingId.value = 0;
    formReset();
};

type ObjectOption = { [key: string]: any };
const genderOptions = ref<ObjectOption[]>([
    { key: "m", value: "Male" },
    { key: "f", value: "Female" },
    { key: "o", value: "Others" },
]);

const formFields = computed<FormField[]>(() => [
    {
        id: "name",
        label: "Name",
        placeholder: "Enter Name",
        defaultValue: "",
        validators: ["required"],
    },
    {
        id: "dob",
        type: "date",
        label: "Date of Birth",
        placeholder: "Date of Birth",
        defaultValue: "",
        validators: ["required"],
    },
    {
        id: "gender",
        label: "Gender",
        type: "select",
        placeholder: "Select Gender",
        options: genderOptions.value,
        validators: ["required"],
    },
    {
        id: "address",
        label: "Address",
        placeholder: "Enter your address",
        defaultValue: "",
        validators: ["required"],
    },
    {
        id: "first_release_year",
        label: "First Released Year",
        placeholder: "",
        validators: ["required"],
    },
    {
        id: "no_of_albums_released",
        label: "Albums Released",
        placeholder: "",
        defaultValue: "",
        validators: ["required"],
    },
]);
const {
    isValid,
    formErrors,
    formData,
    validate,
    reset: formReset,
} = useForm(formFields);

const save = () => {
    validate();
    if (!isValid()) return;
    if (editingId.value) {
        update();
    } else {
        store();
    }
};

const edit = async (id: number) => {
    openModal();
    editingId.value = id;
    fetchLoading.value = true;
    const response = await GetArtist(id);
    fetchLoading.value = false;
    if (response?.status === "1") {
        const data = response.response;
        if (data) {
            formData.value["name"] = data.name;
            formData.value["dob"] = data.dob;
            formData.value["gender"] = data.gender;
            formData.value["address"] = data.address;
            formData.value["first_release_year"] = data.first_release_year;
            formData.value["no_of_albums_released"] = data.no_of_albums_released;
        }
    } else {
        Notify({
            type: "error",
            message: response.message,
        });
    }
};
const store = async () => {
    submitLoading.value = true;
    isDisabled.value = true;
    const response = await StoreArtist(formData.value);
    submitLoading.value = false;
    isDisabled.value = false;
    if (response.status === "1") {
        Notify({ message: response.message });
        emits("saved");
        closeModal();
        return;
    }

    if (
        response?.status == "0" &&
        response.statusCode === 400 &&
        response.message
    ) {
        for (const [key, message] of Object.entries(response.message)) {
            formErrors.value[key] = message;
        }
        return;
    }
    Notify({
        message: response.message,
        ripple: true,
        type: "error",
    });
};

const update = async () => {
    submitLoading.value = true;
    isDisabled.value = true;
    const response = await UpdateArtist(editingId.value, formData.value);
    submitLoading.value = false;
    isDisabled.value = false;
    if (response.status === "1") {
        Notify({ message: response.message });
        emits("saved");
        closeModal();
        return;
    }

    if (
        response?.status == "0" &&
        response.statusCode === 400 &&
        response.message
    ) {
        for (const [key, message] of Object.entries(response.message)) {
            formErrors.value[key] = message;
        }
        return;
    }
    Notify({
        message: response.message,
        ripple: true,
        type: "error",
    });
};

defineExpose({ openModal, edit });
</script>
