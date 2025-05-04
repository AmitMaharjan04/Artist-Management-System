<template>
    <section
        class="bg-gray-200 min-h-screen w-full flex items-center justify-center"
    >
        <div
            class="bg-white rounded-xl w-[400px] md:w-[600px] sm:my-12 md:my-15 lg:w-[1000px] p-6"
        >
            <div class="flex items-center">
                <h4
                    class="text-2xl font-semibold text-gray-900 dark:text-white"
                >
                    {{ "Artist Management System" }}
                </h4>
            </div>
            <div class="flex gap-8 flex-col lg:flex-row">
                <div
                    class="lg:w-1/2 w-full flex items-center justify-center border-b-2 lg:border-r-2 lg:border-b-0"
                >
                    <img
                        src="@assets/images/login.svg"
                        class="w-3/5 lg:w-4/5"
                    />
                </div>
                <div class="lg:w-1/2 w-full space-y-6">
                    <div>
                        <h4
                            class="text-3xl font-semibold text-gray-900 dark:text-white"
                        >
                            Create Account
                        </h4>
                        <p
                            class="text-gray-600 dark:text-gray-400 mt-2 flex align-top justify-start"
                        >
                            Join us to start managing artists
                            <svg
                                class="inline-block w-5 h-5 ml-1"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </p>
                    </div>
                    <form class="space-y-6" @submit.prevent="handleSubmit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="form in formInfo" :key="form.id">
                                <Select
                                    v-if="form.type === 'select'"
                                    v-model="formData[form.id]"
                                    :options="form.options! as ObjectOption[]"
                                    :label="form.label"
                                    :error="formErrors[form.id]"
                                    :placeholder="form.placeholder ?? ''"
                                    :required="
                                        form.validators?.includes('required')
                                    "
                                    @change="validate(form.id)"
                                />
                                <InputField
                                    v-else
                                    :id="form.id"
                                    v-model="formData[form.id]"
                                    :type="form.type"
                                    :placeholder="form.placeholder"
                                    :label="form.label"
                                    :required="
                                        form.validators?.includes('required')
                                    "
                                    :error="formErrors[form.id]"
                                    @input="validate(form.id)"
                                />
                            </div>
                        </div>
                        <div class="flex w-1/2 gap-4 justify-center">
                            <Button
                                label="Register"
                                type="submit"
                                class="w-full"
                                :loading="submitLoading"
                                :disabled="disabledButton"
                            />
                        </div>

                        <p class="text-center text-gray-600 dark:text-gray-400">
                            Already have an account?
                            <router-link
                                :to="{ name: 'LoginPage' }"
                                class="text-primary font-medium hover:underline"
                            >
                                Sign In
                            </router-link>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import InputField from "@/components/Forms/InputField.vue";
import Select from "@/components/Forms/Select.vue";
import { type FormField } from "@/utils/type";
import { useForm } from "@/utils/useForm";
import { useRouter } from "vue-router";
import { RegisterUser } from "../api/auth";
import { Notify } from "@/utils/notify";
import Button from "@/components/Button.vue";

const router = useRouter();
const submitLoading = ref(false);
const disabledButton = ref(false);

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

const formInfo = computed<FormField[]>(() => [
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
        id: "email",
        label: "Email",
        placeholder: "Email Address",
        defaultValue: "",
        validators: ["required", "email"],
    },
    {
        id: "phone",
        label: "Phone",
        placeholder: "Phone Number",
        defaultValue: "",
        validators: ["required", "mobile"],
    },
    {
        id: "password",
        type: "password",
        label: "Password",
        placeholder: "Password",
        defaultValue: "",
        validators: ["required", "min:8"],
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
        id: "role_type",
        label: "Role Type",
        type: "select",
        placeholder: "Select Role",
        options: roleOptions.value,
        validators: ["required"],
    },
]);

const { formData, formErrors, isValid, validate } = useForm(formInfo);

async function handleSubmit(_e: Event) {
    validate();
    if (!isValid())
        return;

    submitLoading.value = true;
    disabledButton.value = true;
    const response = await RegisterUser(formData.value);

    submitLoading.value = false;
    disabledButton.value = false;
    if (response.status === "1") {
        Notify({
            type: "success",
            message: response.message,
        });
        router.push({ name: "LoginPage" });
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
}
</script>
