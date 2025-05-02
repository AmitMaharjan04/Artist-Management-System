import { type FormField } from "./type";
import validateData from "./validateForm";
import { type ComputedRef, type Ref, ref } from "vue";

/**
 * Initialize the form by creating empty formData and formErrors according to formInfo.
 * @param formInfo data of the elements in the form.
 * @returns Initial value of form data and error.
 */
export function useForm(
    formInfo: ComputedRef<FormField[]>,
    customFormData?: Ref<Record<string, any>>,
    customFormErrors?: Ref<Record<string, any>>
) {
    const formData = customFormData ?? ref<Record<string, any>>({});
    const formErrors = customFormErrors ?? ref<Record<string, any>>({});

    initialize();

    function initialize() {
        formInfo.value.forEach((info) => {
            formData.value[info.id] = info.defaultValue ?? "";
            formErrors.value[info.id] = [];
        });
    }

    /**
     * Validate the form field/s
     * @param field - current value of the form field.
     * @returns form errors.
     */
    function validate(field?: string) {
        if (field) {
            const selectedInfo =
                formInfo.value.filter((info) => info.id === field)[0] ?? null;

            formErrors.value[field] = validateData(
                formData.value[selectedInfo.id],
                selectedInfo.validators ?? []
            );
        } else {
            formInfo.value.forEach(async (info) => {
                formErrors.value[info.id] = validateData(
                    formData.value[info.id],
                    info.validators ?? []
                );
            });
        }
        return formErrors;
    }

    /**
     * Validate the form by form error
     * @returns true or false boolean.
     */
    function isValid(): boolean {
        let error = true;
        Object.entries(formErrors.value).forEach(([key, errors]) => {
            if (Array.isArray(errors) && errors.length !== 0) {
                error = false;
            }
        });
        return error;
    }

    function reset() {
        initialize();
    }

    return {
        formData,
        formErrors,
        isValid,
        validate,
        reset,
    };
}
