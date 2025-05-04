import { api } from "@/composables/useApi";
import ApiList from "@/api/apiList";

export const GetSongList = async (params: Record<string, any>) => {
    return await api().get<{
        data: any[];
        total: number;
        last_page: number;
    }>(ApiList.songLists, params);
};

export const StoreSong = async (payload: Record<string, any>) => {
    return await api().post(ApiList.songStore, payload);
};

export const GetSong = async (data: any) => {
    return await api().get<any>(ApiList.songShow, { ...data });
};

export const UpdateSong = async (
    id: number,
    payload: Record<string, any>
) => {
    return await api().patch(ApiList.songUpdate, { id, ...payload });
};

export const DeleteSong = async (data: any) => {
    return await api().delete(ApiList.songDelete, { ...data });
};
