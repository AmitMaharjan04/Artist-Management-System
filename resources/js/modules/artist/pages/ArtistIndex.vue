<template>
    <div class="flex justify-between items-center mb-3">
        <div class="flex gap-6 items-center">
            <div class="text-xl font-bold">Artists</div>
            <div v-if="authStore.user!.role === USER_ROLE.ARTIST_MANAGER">
                <Button
                    color="primary"
                    label="Add Artist"
                    @click="artistForm?.openModal"
                />
            </div>
        </div>
        <div
            v-if="authStore.user!.role === USER_ROLE.ARTIST_MANAGER"
            class="flex gap-3 items-center"
        >
            <div class="relative ml-2 cursor-help group">
                <div
                    class="flex items-center justify-center w-5 h-5 rounded-full bg-gray-200 text-gray-700 text-xs font-bold"
                >
                    ?
                </div>
                <div
                    class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-64 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-opacity z-10"
                >
                    Import CSV format must match export CSV format.
                </div>
            </div>
            <div>
                <input
                    type="file"
                    id="csvFileInput"
                    accept=".csv"
                    style="display: none"
                    @change="handleFileUpload"
                    ref="fileInput"
                />
                <Button
                    label="Import CSV"
                    :disabled="importDisabled"
                    @click="() => ($refs.fileInput as HTMLInputElement)?.click()"
                />
            </div>
            <Button
                label="Export CSV"
                color="success"
                :disabled="exportDisabled"
                @click="exportCSV"
            />
        </div>
    </div>
    <div v-if="importing" class="p-4 bg-yellow-100 rounded mb-3">
        <p>Processing import... Please wait.</p>
    </div>
    <TabulatorTable
        ref="artistTable"
        table-id="artist-index"
        :ajax-url="ApiList.artistLists"
        :columns="columns"
        :data-loader-function="loadData"
    />
    <ArtistForm ref="artistForm" @saved="artistTable?.refreshTable" />
</template>

<script setup lang="ts">
import { useRouter } from "vue-router";
import { useTemplateRef, ref } from "vue";
import Swal from "sweetalert2";

import ApiList from "@/api/apiList";
import TabulatorTable from "@/components/TabulatorTable.vue";
import { CellComponent } from "tabulator-tables";
import { vueActionFormatter } from "@/utils/actionFormatter";
import { GetArtistList, ImportArtists } from "@/modules/artist/api/artist";
import { Notify } from "@/utils/notify";
import Button from "@/components/Button.vue";
import ArtistForm from "../components/ArtistForm.vue";
import { DeleteArtist } from "@/modules/artist/api/artist";
import { USER_ROLE } from "@/constants/constant";
import { useAuthStore } from "@/stores/authStore";

const authStore = useAuthStore();
const fileInput = ref(null);
const importing = ref(false);
const exportDisabled = ref(false);
const importDisabled = ref(false);
// Handle file upload - simplified to just send the file directly
const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Check if the file is a CSV
    if (file.type !== "text/csv" && !file.name.endsWith(".csv")) {
        Notify({
            type: "error",
            message: "Please select a valid CSV file.",
        });
        return;
    }

    // Create form data to send the file
    const formData = new FormData();
    formData.append("csv_file", file);

    importing.value = true;
    importDisabled.value = true;
    try {
        const response = await ImportArtists(formData);

        if (response?.status === "1") {
            Notify({
                type: "success",
                message: response.message || "Artists imported successfully.",
            });
            artistTable.value?.refreshTable();
        } else {
            // If there were validation errors, display them
            if (Array.isArray(response.message)) {
                // Format error messages from backend
                Swal.fire({
                    title: "Import Failed",
                    html: `<ul class="text-left">${response.message
                        .map((msg) => `<li>${msg}</li>`)
                        .join("")}</ul>`,
                    icon: "error",
                });
            } else {
                Swal.fire({
                    title: "Import Failed",
                    html: `<ul class="text-left"><li>${response.message}</li></ul>`,
                    icon: "error",
                });
            }
        }
    } catch (error) {
        Notify({
            type: "error",
            message: "An error occurred during import.",
        });
        console.error("Import error:", error);
    } finally {
        importing.value = false;
        importDisabled.value = false;
        // Reset file input so the same file can be selected again
        event.target.value = "";
    }
};

const router = useRouter();

const artistForm = useTemplateRef<InstanceType<any>>("artistForm");
const artistTable =
    useTemplateRef<InstanceType<typeof TabulatorTable>>("artistTable");

const exportCSV = () => {
    const tabulator = artistTable.value?.tabulatorInstance;
    if (tabulator) {
        exportDisabled.value = true;
        tabulator.download("csv", "artists.csv");
        exportDisabled.value = false;
    }
};

const columns: any[] = [
    {
        title: "S.N",
        field: "sn",
        formatter: "rownum",
        headerSort: false,
        width: 50,
        resizable: false,
        download: false,
    },
    {
        title: "Name",
        field: "name",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 120,
    },
    {
        title: "Date of Birth",
        field: "dob",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 150,
    },
    {
        title: "Gender",
        field: "gender",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 150,
        formatter: genderFormatter,
    },
    {
        title: "Address",
        field: "address",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 120,
    },
    {
        title: "First Released Year",
        field: "first_release_year",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 150,
    },
    {
        title: "Albums Released",
        field: "no_of_albums_released",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 150,
    },
    {
        title: "Actions",
        field: "actions",
        minWidth: 120,
        headerSort: false,
        download: false,
        formatter: vueActionFormatter,
        formatterParams: (cell: CellComponent): any => ({
            router,
            type: "flex",
            buttonType: "normal",
            onlyIcon: false,
            options: [
                ...(authStore.user!.role === USER_ROLE.ARTIST_MANAGER
                    ? [
                          {
                              type: "action",
                              label: "Edit",
                              color: "text-blue-600 text-lg",
                              icon: "fa-pen",
                              class: "mx-2",
                              handler: editData,
                          },
                      ]
                    : []),

                ...(authStore.user!.role === USER_ROLE.ARTIST_MANAGER
                    ? [
                          {
                              type: "action",
                              label: "Delete",
                              color: "text-red-600 text-lg",
                              icon: "fa-trash",
                              class: "mx-2",
                              handler: deleteData,
                          },
                      ]
                    : []),
                ...(authStore.user!.role === USER_ROLE.ARTIST_MANAGER ||
                authStore.user!.role === USER_ROLE.SUPER_ADMIN
                    ? [
                          {
                              type: "action",
                              label: "Songs",
                              icon: "fa-music",
                              color: "text-green-600 text-lg",
                              class: "mx-2",
                              handler: () => {
                                  const artistId = cell.getData().id;
                                  router.push(`/songs/artist/${artistId}`);
                              },
                          },
                      ]
                    : []),
            ],
        }),
    },
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
    artistForm.value?.edit(data.id);
};

const loadData = async (params: any) => {
    const response = await GetArtistList(params);
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
            const response = await DeleteArtist(data.id);
            if (response?.status === "1") {
                Notify({ message: response.message });
                artistTable.value?.refreshTable();
            } else {
                Notify({
                    type: "error",
                    message: response.message,
                });
            }
        }
    });
};
</script>
