import { Notyf } from "notyf";
import "notyf/notyf.min.css"; // ✅ Add this line

const notyf = new Notyf({
    types: [
        {
            type: "success",
            background: "#33C763",
            icon: {
                className: "fa-solid fa-check",
                tagName: "i",
                color: "#fff",
            },
        },
        {
            type: "primary",
            background: "#3C50E0",
            icon: {
                className: "fa-solid fa-check",
                tagName: "i",
                color: "#fff",
            },
        },
        {
            type: "error",
            background: "#FA5252",
            icon: {
                className: "fa-solid fa-x",
                tagName: "span",
                color: "#fff",
            },
            dismissible: false,
        },
    ],
});

export const Notify = ({
    type = "success",
    xPosition = "right",
    yPosition = "top",
    dismissible = true,
    duration = 3000,
    message,
    ripple = true,
}: {
    message: string;
    ripple?: boolean;
    type?: "success" | "error" | "warning" | "primary" | "info";
    xPosition?: "left" | "center" | "right";
    yPosition?: "top" | "bottom" | "center";
    dismissible?: boolean;
    duration?: number;
}) => {
    notyf.open({
        type,
        position: {
            x: xPosition,
            y: yPosition,
        },
        dismissible,
        duration,
        message,
        ripple,
    });
};
