<template>
    <div :id="props.tableId" class="w-full"></div>
</template>

<script setup lang="ts">
import {
    type Filter,
    TabulatorFull as Tabulator,
    type ColumnDefinition,
    type Options,
    type TabulatorFull,
} from "tabulator-tables";
import { onMounted, ref } from "vue";

interface TabulatorDataLoaderParams {
    filter?: Record<string, any>;
    page: number;
    size: number;
    sort_field?: string;
    sort_dir?: "asc" | "desc";
}
interface TabulatorDataResponse {
    data: any[];
    total: number;
    last_page: number;
}

interface TabulatorProps {
    tableId: string;
    ajaxUrl?: string;
    data?: any[];
    columns: ColumnDefinition[];
    action?: any[];
    additionalConfig?: Options;
    dataLoaderFunction: (
        params: TabulatorDataLoaderParams
    ) => Promise<TabulatorDataResponse>;
}

const props = withDefaults(defineProps<TabulatorProps>(), {});

const tabulator = ref<TabulatorFull | null>(null);

const mountTabulator = () => {
    const columnsWithActions = [...props.columns];
    if (props.action) {
        columnsWithActions.push(createActionColumn(props.action));
    }

    const tabulatorOptions: Options = {
        columns: columnsWithActions,
        layout: "fitColumns",
        pagination: true,
        placeholder: "No Data Available",
        placeholderHeaderFilter: "No Matching Data Available",
        paginationSize: 25,
        paginationSizeSelector: [25, 50, 100],
        ...props.additionalConfig,
        ...{
            dataReceiveParams: {
                last_page: "last_page",
                last_row: "total",
            },
            ajaxURL: props.ajaxUrl,
            paginationMode: "remote",
            sortMode: "remote",
            filterMode: "remote",
            ajaxRequestFunc: async (url: string, config: any, params: any) => {
                const filters: Filter[] = params.filter || [];
                const processedFilters = {};
                filters.forEach((filter: Filter) => {
                    processedFilters[filter.field] = filter.value;
                });

                const queryParams = {
                    filter: processedFilters,
                    page: params.page,
                    size: params.size,
                    sort_field: params.sort[0]?.field,
                    sort_dir: params.sort[0]?.dir,
                };

                return await props.dataLoaderFunction(queryParams);
            },
        },
    };

    const table = new Tabulator(`#${props.tableId}`, tabulatorOptions);
    tabulator.value = table;
};

onMounted(() => {
    mountTabulator();
});

const refreshTable = (url: string = "") => {
    if (!tabulator.value) return;

    if (url) {
        tabulator.value.setData(url);
    } else {
        if (tabulator.value.getPage() === 1) {
            tabulator.value.setData();
        } else {
            tabulator.value.setPage(Number(tabulator.value.getPage()));
        }
    }
};

function createActionColumn(actions) {
    return {
        title: "Actions",
        field: "actions",
        hozAlign: "center",
        headerSort: false,
        formatter: (cell, formatterParams, onRendered) => {
            const rowData = cell.getRow().getData();
            const container = document.createElement("div");
            container.style.display = "flex";
            container.style.gap = "0.5rem";
            container.style.justifyContent = "center";

            actions.forEach((action) => {
                const button = document.createElement("button");
                button.setAttribute("class", action.className);
                button.setAttribute("type", "button");
                button.style.border = "none";
                button.style.background = "transparent";
                button.style.cursor = "pointer";

                // Handle icon rendering: supports either `iconClass` or `iconSrc`
                if (action.iconClass) {
                    const icon = document.createElement("i");
                    icon.setAttribute("class", action.iconClass);
                    button.appendChild(icon);
                } else if (action.iconSrc) {
                    const img = document.createElement("img");
                    img.setAttribute("src", action.iconSrc);
                    img.setAttribute("alt", action.label || "icon");
                    img.style.width = "16px";
                    img.style.height = "16px";
                    button.appendChild(img);
                } else {
                    button.innerText = action.label;
                }

                button.addEventListener("click", (e) => {
                    e.preventDefault();
                    action.callback(rowData);
                });

                container.appendChild(button);
            });

            return container;
        },
    };
}

defineExpose({
    refreshTable,
    tabulatorInstance: tabulator,
});
</script>
