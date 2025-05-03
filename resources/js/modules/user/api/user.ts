import { api } from "@/composables/useApi";
import ApiList from "@/api/apiList";

export const GetUserList = async (params: Record<string, any>) => {
    return await api().get<{
        data: any[];
        total: number;
        last_page: number;
    }>(ApiList.userLists, params);
};

export const StoreUser = async (payload: Record<string, any>) => {
    return await api().post(ApiList.userStore, payload);
};

export const GetUser = async (id: number) => {
    return await api().get<any>(ApiList.userShow, { id });
};

export const UpdateUser = async (
    id: number,
    payload: Record<string, any>
) => {
    return await api().patch(ApiList.userUpdate, { id, ...payload });
};

export const DeleteUser = async (id: number) => {
    return await api().delete(ApiList.userDelete, { id });
};
