<template>
    <div>
        <Button label="Go Back" color="primary" @click="goBackToArtist" />
    </div>
        <div class="my-4 text-xl font-bold">Song Lists</div>

    <TabulatorTable
        ref="songTable"
        table-id="song-index"
        :ajax-url="ApiList.songLists"
        :columns="columns"
        :data-loader-function="loadData"
    />
</template>

<script setup lang="ts">
import { useRouter, useRoute } from "vue-router";
import { useTemplateRef } from "vue";
import Swal from "sweetalert2";

import ApiList from "@/api/apiList";
import TabulatorTable from "@/components/TabulatorTable.vue";
import { CellComponent } from "tabulator-tables";
import { vueActionFormatter } from "@/utils/actionFormatter";
import { GetSongList } from "@/modules/song/api/song";
import { Notify } from "@/utils/notify";
import Button from "@/components/Button.vue";
import SongForm from "@/modules/song/components/SongForm.vue";
import { DeleteSong } from "@/modules/song/api/song";

const router = useRouter();
const route = useRoute();
const songForm = useTemplateRef<InstanceType<any>>("songForm");
const songTable =
    useTemplateRef<InstanceType<typeof TabulatorTable>>("songTable");

function goBackToArtist() {
    router.back();
}
const columns: any[] = [
    {
        title: "S.N",
        field: "sn",
        formatter: "rownum",
        headerSort: false,
        width: 50,
        resizable: false,
    },
    {
        title: "Title",
        field: "title",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 120,
    },
    {
        title: "Album Name",
        field: "album_name",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 120,
    },
    {
        title: "Genre",
        field: "genre",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 150,
    },
    {
        title: "Artist",
        field: "artist_id",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 120,
    },
];

const loadData = async (params: any) => {
    params["artist_id"] = route.params.id;
    const response = await GetSongList(params);
    if (response?.status === "1") {
        return response.response!;
    } else {
        Notify({ type: "error", message: response.message });
    }
    return { total: 0, last_page: 0, data: [] };
};
</script>
