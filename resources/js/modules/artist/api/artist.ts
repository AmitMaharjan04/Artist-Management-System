import { api } from "@/composables/useApi";
import ApiList from "@/api/apiList";

export const GetArtistList = async (params: Record<string, any>) => {
    return await api().get<{
        data: any[];
        total: number;
        last_page: number;
    }>(ApiList.artistLists, params);
};

export const StoreArtist = async (payload: Record<string, any>) => {
    return await api().post(ApiList.artistStore, payload);
};

export const GetArtist = async (id: number) => {
    return await api().get<any>(ApiList.artistShow, { id });
};

export const UpdateArtist = async (
    id: number,
    payload: Record<string, any>
) => {
    return await api().patch(ApiList.artistUpdate, { id, ...payload });
};

export const DeleteArtist = async (id: number) => {
    return await api().delete(ApiList.artistDelete, { id });
};

export const ImportArtists = async (data: any) => {
    return await api().post(ApiList.artistImport, data, {
        "content-type": "multipart/form-data",
    });
};