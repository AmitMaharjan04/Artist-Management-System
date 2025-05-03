import type { CellComponent } from "tabulator-tables";

export function vueActionFormatter(cell: CellComponent, formatterParams: any) {
    const rowData = cell.getRow().getData();
    const container = document.createElement("div");
    container.classList.add("flex", "items-center", "justify-start", "gap-2");

    const { options = [] } = formatterParams;

    options.forEach((option) => {
        const button = document.createElement("button");
        button.setAttribute("type", "button");
        button.classList.add(option.class ?? "", "hover:opacity-80");

        if (option.icon) {
            const icon = document.createElement("i");
            icon.className = `fas ${option.icon} ${option.color}`;
            button.appendChild(icon);
        } else {
            button.innerText = option.label || "Action";
        }

        button.addEventListener("click", (e) => {
            e.preventDefault();
            option.handler && option.handler(rowData);
        });

        container.appendChild(button);
    });

    return container;
}
