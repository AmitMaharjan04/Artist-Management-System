import { ApiClient } from "@/composables/apiClient";
import { Notify } from "@/utils/notify";
import { useAuthStore } from "@/stores/authStore";

let instance: ApiClient | null = null;

const unauthorized = () => {
    const authStore = useAuthStore();
    authStore.clearSession();
    localStorage.clear();
    window.location.reload();
};

const onServerError = () => {
    Notify({ type: "error", message: "Server Error Occurred" });
};

export const api = (): ApiClient => {
    if (!instance) {
        const authStore = useAuthStore();
        instance = new ApiClient({
            token: authStore.accessToken,
            onUnauthorized: unauthorized,
            onServerError,
        });
    }
    return instance;
};

export const destroyInstance = () => {
    instance = null;
};

export default api;
