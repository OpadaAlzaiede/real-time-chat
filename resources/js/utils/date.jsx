import input from "daisyui/components/input";

export const formatMessageDate = (date, useLong = false) => {
    const now = new Date();
    const inputDate = new Date(date);

    if(isToday(inputDate)) {
        return inputDate.toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit",
        });
    } else if(isYesterday(inputDate)) {
        return useLong
                ? "Yesterday " + inputDate.toLocaleTimeString([], {
                    hour: "2-digit",
                    minute: "2-digit",
                })
                : "Yesterday";
    } else if(inputDate.getFullYear() === now.getFullYear()) {
        return inputDate.toLocaleDateString([], {
            day: "2-digit",
            month: "short",
        });
    } else {
        return inputDate.toLocaleDateString();
    }
};

export const isToday = (date) => {
    const now = new Date();
    const inputDate = new Date(date);

    return inputDate.getFullYear() === now.getFullYear() &&
            inputDate.getMonth() === now.getMonth() &&
            inputDate.getDate() === now.getDate();
};

export const isYesterday = (date) => {
    const now = new Date();
    const inputDate = new Date(date);

    return inputDate.getFullYear() === now.getFullYear() &&
            inputDate.getMonth() === now.getMonth() &&
            inputDate.getDate() === now.getDate() - 1;
};
