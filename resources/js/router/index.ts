import { createRouter, createWebHistory } from "vue-router";
import routes from "./routes";
import { useAuthStore } from "@/stores/authStore";
import { Notify } from "@/utils/notify";
import Swal from "sweetalert2";

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const defaultTitle = 'Artist Management System';
    document.title = (to.meta.title as string) || defaultTitle;
    next();
  });

router.beforeEach((to, from, next) => {
    const userAuth = useAuthStore();
    if (
        to.name == "LoginPage" &&
        userAuth.accessToken
    ) {
        return next({ name: "Dashboard" });
    }
    if (to.meta.requiresAuth && !userAuth.accessToken) {
        Notify({ type: "error", message: "Unauthenticated" });
        return next({ name: "LoginPage" });
    }
    if(to.meta.requiresAuth && to.meta.authenticateWhen) {
        const authStore = useAuthStore();
        if (Array.isArray(to.meta.authenticateWhen) && !to.meta.authenticateWhen.includes(authStore.user?.role)) {
            Swal.fire({
                title: "Access Denied",
                text: "You do not have permission to access this page.",
                icon: "error",
            });
            return next({ name: "Dashboard" });
        }
    }
    return next();
});

export default router;
