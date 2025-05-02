import { api } from "@/composables/useApi";
import ApiList from "@/api/apiList";

export const LoginUser = async (
    payload: {
        password: string;
    } & ({ username: string } | { email: string })
) => {
    return await api().post<any>(ApiList.login, payload);
};

export const RegisterUser = async (payload: Record<string, any>) => {
    return await api().post<any>(ApiList.register, payload);
};