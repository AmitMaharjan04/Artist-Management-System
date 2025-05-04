import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        accessToken: "",
        superAdmin: false,
        user: {},
        activeTab: "",
    }),
    actions: {
        setToken(token: string) {
            this.accessToken = token;
        },

        setSuperAdmin(value: boolean) {
            this.superAdmin = value;
        },
        setUser(user: Record<string, any>) {
            this.user = user;
        },

        setActiveTab(tab: string) {
            this.activeTab = tab;
        },

        clearSession() {
            this.accessToken = "";
            this.superAdmin = false;
            this.user = {},
            this.activeTab = "";
        },
    },
    persist: true,
});
