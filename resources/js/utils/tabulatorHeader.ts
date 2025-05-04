import type {
    CellComponent,
    EmptyCallback,
    ValueBooleanCallback,
} from "tabulator-tables";

export function dateFilter(
    _cell: CellComponent,
    _onRendered: EmptyCallback,
    success: ValueBooleanCallback
): HTMLInputElement {
    const inputField = document.createElement("input");
    inputField.type = "date";

    inputField.addEventListener("click", function () {
        try {
            this.showPicker();
        } catch (err) {
            console.error(err);
        }
    });

    inputField.addEventListener("focus", function () {
        try {
            this.showPicker();
        } catch (err) {
            console.error(err);
        }
    });

    inputField.addEventListener("change", function () {
        this.blur(); // optionally blur on select
    });

    document.addEventListener("click", function (e: any) {
        if (!inputField.contains(e.target)) {
            inputField.blur();
        }
    });

    function buildValue() {
        success(inputField.value);
    }

    inputField.addEventListener("change", buildValue);

    return inputField;
}

export function selectFilter(
    options: { key: string; value: string }[]
) {
    return function (
        _cell: CellComponent,
        _onRendered: EmptyCallback,
        success: ValueBooleanCallback
    ): HTMLSelectElement {
        const select = document.createElement("select");

        // Add "All" option
        const allOption = document.createElement("option");
        allOption.value = "";
        allOption.textContent = "All";
        select.appendChild(allOption);

        // Append provided options
        for (const { key, value } of options) {
            const option = document.createElement("option");
            option.value = key;
            option.textContent = value;
            select.appendChild(option);
        }

        // Trigger filter on change
        select.addEventListener("change", () => {
            success(select.value);
        });

        return select;
    };
}