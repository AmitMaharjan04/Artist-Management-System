<template>
    <Modal
        ref="formModal"
        :title="editingId ? 'Edit User' : 'Add User'"
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

import { StoreUser, GetUser, UpdateUser } from "@/modules/user/api/user";
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

const roleOptions = ref<ObjectOption[]>([
    { key: "super_admin", value: "Super Admin" },
    { key: "artist_manager", value: "Artist Manager" },
    { key: "artist", value: "Artist" },
]);

const formFields = computed<FormField[]>(() => [
    {
        id: "first_name",
        label: "First Name",
        placeholder: "First Name",
        defaultValue: "",
        validators: ["required"],
    },
    {
        id: "last_name",
        label: "Last Name",
        placeholder: "Last Name",
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
        id: "role_type",
        label: "Role Type",
        type: "select",
        placeholder: "Select Role",
        options: roleOptions.value,
        validators: ["required"],
    },
    {
        id: "email",
        label: "Email",
        placeholder: "Email Address",
        defaultValue: "",
        validators: ["required", "email"],
    },
    {
        id: "password",
        type: "password",
        label: "Password",
        placeholder: "Password",
        defaultValue: "",
        validators: editingId.value ? [] : ["required", "min:8"],
    },
    {
        id: "phone",
        label: "Phone",
        placeholder: "Phone Number",
        defaultValue: "",
        validators: ["required", "mobile"],
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
        id: "address",
        label: "Address",
        placeholder: "Enter your address",
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
    const response = await GetUser(id);
    fetchLoading.value = false;
    if (response?.status === "1") {
        const data = response.response;
        if (data) {
            formData.value["first_name"] = data.first_name;
            formData.value["last_name"] = data.last_name;

            formData.value["email"] = data.email;
            formData.value["phone"] = data.phone;
            formData.value["dob"] = data.dob;
            formData.value["gender"] = data.gender;
            formData.value["address"] = data.address;
            formData.value["role_type"] = data.role_type;
        }
    }
};
const store = async () => {
    submitLoading.value = true;
    isDisabled.value = true;
    const response = await StoreUser(formData.value);
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
};

const update = async () => {
    submitLoading.value = true;
    isDisabled.value = true;
    const response = await UpdateUser(editingId.value, formData.value);
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
};

defineExpose({ openModal, edit });
</script>
