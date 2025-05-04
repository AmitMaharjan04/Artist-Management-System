<template>
    <div class="flex gap-6 justify-start items-center mb-3">
        <div class="text-xl font-bold">Users</div>
        <div>
            <Button
                color="primary"
                label="Add User"
                @click="userForm?.openModal"
            />
        </div>
    </div>
    <TabulatorTable
        ref="userTable"
        table-id="user-index"
        :ajax-url="ApiList.userLists"
        :columns="columns"
        :data-loader-function="loadData"
    />
    <UserForm ref="userForm" @saved="userTable?.refreshTable" />
</template>

<script setup lang="ts">
import { useRouter } from "vue-router";
import { useTemplateRef } from "vue";
import Swal from "sweetalert2";

import ApiList from "@/api/apiList";
import TabulatorTable from "@/components/TabulatorTable.vue";
import { CellComponent } from "tabulator-tables";
import { vueActionFormatter } from "@/utils/actionFormatter";
import { GetUserList } from "@/modules/user/api/user";
import { Notify } from "@/utils/notify";
import Button from "@/components/Button.vue";
import UserForm from "@/modules/user/components/UserForm.vue";
import { DeleteUser } from "@/modules/user/api/user";
import { dateFilter, selectFilter } from "@/utils/tabulatorHeader";

const router = useRouter();
const genderOptions = [
    { key: "m", value: "Male" },
    { key: "f", value: "Female" },
    { key: "o", value: "Others" },
];

const roleOptions = [
    { key: "super_admin", value: "Super Admin" },
    { key: "artist_manager", value: "Artist Manager" },
    { key: "artist", value: "Artist" },
];

const userForm = useTemplateRef<InstanceType<any>>("userForm");
const userTable =
    useTemplateRef<InstanceType<typeof TabulatorTable>>("userTable");

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
        title: "Name",
        field: "name",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 120,
    },
    {
        title: "Email",
        field: "email",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 120,
    },
    {
        title: "Phone Number",
        field: "phone",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 150,
    },
    {
        title: "Date of Birth",
        field: "dob",
        headerFilter: dateFilter,
        headerFilterFunc: "=",
        headerFilterLiveFilter: false,
        minWidth: 150,
        formatter: function (cell) {
            const value = cell.getValue();
            if (!value) return "";

            try {
                const date = new Date(value);
                if (isNaN(date.getTime())) return "(invalid date)";
                const day = date.getDate().toString().padStart(2, "0");
                const month = (date.getMonth() + 1).toString().padStart(2, "0");
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            } catch {
                return "(invalid date)";
            }
        },
    },

    {
        title: "Gender",
        field: "gender",
        headerFilter: selectFilter(genderOptions),
        headerFilterFunc: "=",
        minWidth: 150,
        formatter: genderFormatter,
    },
    {
        title: "Address",
        field: "address",
        headerFilter: "input",
        headerFilterLiveFilter: false,
        minWidth: 150,
    },
    {
        title: "Role Type",
        field: "role_type",
        headerFilter: selectFilter(roleOptions),
        headerFilterFunc: "=",
        headerFilterLiveFilter: false,
        minWidth: 150,
    },
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
    userForm.value?.edit(data.id);
};

const loadData = async (params: any) => {
    const response = await GetUserList(params);
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
            const response = await DeleteUser(data.id);
            if (response?.status === "1") {
                Notify({ message: response.message });
                userTable.value?.refreshTable();
            }
        }
    });
};
</script>
