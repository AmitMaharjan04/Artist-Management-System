<template>
    <Modal
        ref="formModal"
        :title="editingId ? 'Edit Song' : 'Add Song'"
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

import { StoreSong, GetSong, UpdateSong } from "@/modules/song/api/song";
import { type FormField } from "@/utils/type";
import { useForm } from "@/utils/useForm";
import { Notify } from "@/utils/notify";
import Loading from "@/components/Loading.vue";


import { GetAllArtists } from "@/modules/artist/api/artist";


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
const genreOptions = ref<ObjectOption[]>([
    { key: "classic", value: "Classic" },
    { key: "country", value: "Country" },
    { key: "jazz", value: "Jazz" },
    { key: "rnb", value: "RnB" },
    { key: "rock", value: "Rock" },
]);

const artistOptions = ref<ObjectOption[]>([]);

(async () => {
    const response = await GetAllArtists();
    artistOptions.value = response.response.map((artist: any) => ({
        key: artist.id,
        value: artist.name,
    }));
})();
const formFields = computed<FormField[]>(() => [
    {
        id: "artist_id",
        label: "Artist",
        type: "select",
        placeholder: "Select Artist",
        options: artistOptions.value,
        validators: ["required"],
    },
    {
        id: "title",
        label: "Title",
        placeholder: "Enter Title",
        defaultValue: "",
        validators: ["required"],
    },
    {
        id: "album_name",
        label: "Album Name",
        placeholder: "Album Name",
        defaultValue: "",
        validators: ["required"],
    },
    {
        id: "genre",
        label: "Genre",
        type: "select",
        placeholder: "Select Genre",
        options: genreOptions.value,
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

const edit = async (data: any) => {
    openModal();
    editingId.value = data.updated_at;
    fetchLoading.value = true;
    const response = await GetSong(data);
    fetchLoading.value = false;
    if (response?.status === "1") {
        const data = response.response;
        if (data) {
            formData.value["title"] = data.title;
            formData.value["artist_id"] = data.artist_id;
            formData.value["album_name"] = data.album_name;
            formData.value["genre"] = data.genre;
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
    const response = await StoreSong(formData.value);
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
    const response = await UpdateSong(editingId.value, formData.value);
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
