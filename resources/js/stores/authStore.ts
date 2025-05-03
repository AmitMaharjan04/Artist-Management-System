import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        accessToken: "",
        superAdmin: false,
    }),
    actions: {
        setToken(token: string) {
            this.accessToken = token;
        },

        setSuperAdmin(value: boolean) {
            this.superAdmin = value;
        },

        clearSession() {
            this.accessToken = "";
            this.superAdmin = false;
        },
    },
    persist: true,
});
