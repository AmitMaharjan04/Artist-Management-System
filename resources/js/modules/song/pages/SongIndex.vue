<template>
    <div v-if="route.name !== 'Dashboard'" class="mb-2">
        <Button label="Go Back" color="primary" @click="goBackToArtist" />
    </div>
    <div class="flex gap-6 justify-start items-center mb-3">
        <div class="text-xl font-bold">Songs</div>
        <div v-if="authStore.user!.role === USER_ROLE.ARTIST">
            <Button
                color="primary"
                label="Add Song"
                @click="songForm?.openModal"
            />
        </div>
    </div>
    <TabulatorTable
        ref="songTable"
        table-id="song-index"
        :ajax-url="ApiList.songLists"
        :columns="columns"
        :data-loader-function="loadData"
    />
    <SongForm ref="songForm" @saved="songTable?.refreshTable" />
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
import { USER_ROLE } from "@/constants/constant";
import { useAuthStore } from "@/stores/authStore";

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const songForm = useTemplateRef<InstanceType<any>>("songForm");
const songTable =
    useTemplateRef<InstanceType<typeof TabulatorTable>>("songTable");

interface Props {
    artist_id?: number | null;
}

function goBackToArtist() {
    router.back();
}

const props = withDefaults(defineProps<Props>(), {
    artist_id: null,
});

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
        field: "artist_name",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 150,
    },

    ...(authStore.user!.role === USER_ROLE.ARTIST
        ? [
              {
                  title: "Actions",
                  field: "actions",
                  minWidth: 120,
                  headerSort: false,
                  formatter: vueActionFormatter,
                  formatterParams: (cell: CellComponent): any => ({
                      router,
                      type: "flex",
                      buttonType: "normal",
                      onlyIcon: true,
                      options: [
                          {
                              type: "action",
                              label: "Edit",
                              color: "text-blue-600 text-lg",
                              icon: "fa-pen",
                              class: "mx-2",
                              handler: editData,
                          },
                          {
                              type: "action",
                              label: "Delete",
                              icon: "fa-trash",
                              color: "text-red-600 text-lg",
                              class: "mx-2",
                              handler: deleteData,
                          },
                      ],
                  }),
              },
          ]
        : []),
];

function genderFormatter(cell, formatterParams, onRendered) {
    if (cell.getValue() == "m") {
        return "Male";
    } else if (cell.getValue() == "f") {
        return "Female";
    } else {
        return "Others";
    }
}

const editData = (data: Record<string, any>) => {
    songForm.value?.edit(data);
};

const loadData = async (params: any) => {
    // if(props.artist_id)
    //     params["artist_id"] = props.artist_id;
    const response = await GetSongList(params);
    if (response?.status === "1") {
        return response.response!;
    } else {
        Notify({ type: "error", message: response.message });
    }
    return { total: 0, last_page: 0, data: [] };
};

const deleteData = (data: Record<string, any>) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
    }).then(async (result) => {
        if (result.isConfirmed) {
            const response = await DeleteSong(data);
            if (response?.status === "1") {
                Notify({ message: response.message });
                songTable.value?.refreshTable();
            }
        }
    });
};
</script>
