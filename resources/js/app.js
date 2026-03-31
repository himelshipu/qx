import { createPopper } from "@popperjs/core";
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";
import ApexCharts from "apexcharts";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import "./stripe/payment";

Alpine.plugin(collapse);

window.Alpine = Alpine;
window.flatpickr = flatpickr;
window.ApexCharts = ApexCharts;
window.createPopper = createPopper;

// Custom Toast System using Alpine.js
Alpine.store("toast", {
    toasts: [],
    add(type, message) {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, type, message });

        // Auto remove after 4 seconds
        setTimeout(() => {
            this.remove(id);
        }, 4000);
    },
    remove(id) {
        this.toasts = this.toasts.filter(t => t.id !== id);
    },
    success(message) {
        this.add("success", message);
    },
    error(message) {
        this.add("error", message);
    },
    info(message) {
        this.add("info", message);
    },
    warning(message) {
        this.add("warning", message);
    },
});

window.toast = {
    success(message) {
        Alpine.store("toast").success(message);
    },
    error(message) {
        Alpine.store("toast").error(message);
    },
    info(message) {
        Alpine.store("toast").info(message);
    },
    warning(message) {
        Alpine.store("toast").warning(message);
    },
};

Alpine.store("confirmModal", {
    isOpen: false,
    title: "Please confirm",
    message: "Are you sure you want to continue?",
    confirmText: "Confirm",
    variant: "danger",
    onConfirm: null,

    open(options = {}) {
        this.title = options.title || "Please confirm";
        this.message = options.message || "Are you sure you want to continue?";
        this.confirmText = options.confirmText || "Confirm";
        this.variant = options.variant || "danger";
        this.onConfirm = typeof options.onConfirm === "function" ? options.onConfirm : null;
        this.isOpen = true;
    },

    close() {
        this.isOpen = false;
        this.onConfirm = null;
    },

    confirm() {
        if (typeof this.onConfirm === "function") {
            this.onConfirm();
        }
        this.close();
    },
});

window.confirmationModal = {
    open(options) {
        Alpine.store("confirmModal").open(options);
    },
};

function extractConfirmMessage(raw) {
    if (!raw || typeof raw !== "string") {
        return null;
    }

    const match = raw.match(/confirm\((['"`])([\s\S]*?)\1\)/i);
    return match ? match[2] : null;
}

function parseClickConfirm(raw) {
    if (!raw || typeof raw !== "string") {
        return null;
    }

    const simpleMatch = raw.match(/^\s*return\s+confirm\((['"`])([\s\S]*?)\1\)\s*;?\s*$/i);
    if (simpleMatch) {
        return {
            message: simpleMatch[2],
            script: null,
        };
    }

    const conditionalMatch = raw.match(/^\s*if\s*\(\s*confirm\((['"`])([\s\S]*?)\1\)\s*\)\s*\{([\s\S]*)\}\s*;?\s*$/i);
    if (conditionalMatch) {
        return {
            message: conditionalMatch[2],
            script: conditionalMatch[3]?.trim() || null,
        };
    }

    return null;
}

function normalizeLegacyConfirmAttributes() {
    document.querySelectorAll("form[onsubmit*='confirm(']").forEach((form) => {
        const onsubmitValue = form.getAttribute("onsubmit");
        const isSimpleConfirm = /^\s*return\s+confirm\(/i.test(onsubmitValue || "");
        if (!isSimpleConfirm) {
            return;
        }

        const message = extractConfirmMessage(onsubmitValue);
        if (message) {
            form.dataset.confirmMessage = message;
            form.dataset.confirmVariant = form.dataset.confirmVariant || "danger";
            form.classList.add("js-confirmable");
            form.removeAttribute("onsubmit");
        }
    });

    document.querySelectorAll("button[onclick*='confirm('], a[onclick*='confirm(']").forEach((element) => {
        const parsed = parseClickConfirm(element.getAttribute("onclick"));
        if (parsed?.message) {
            element.dataset.confirmMessage = parsed.message;
            element.dataset.confirmVariant = element.dataset.confirmVariant || "danger";
            if (parsed.script) {
                element.dataset.confirmScript = parsed.script;
            }
            element.classList.add("js-confirmable");
            element.removeAttribute("onclick");
        }
    });
}

function openElementConfirmation(element, onConfirm) {
    const fallbackTitle = element.dataset.confirmVariant === "danger" ? "Confirm deletion" : "Please confirm";

    window.confirmationModal.open({
        title: element.dataset.confirmTitle || fallbackTitle,
        message: element.dataset.confirmMessage || "Are you sure you want to continue?",
        confirmText: element.dataset.confirmButton || "Confirm",
        variant: element.dataset.confirmVariant || "danger",
        onConfirm,
    });
}

function initializeConfirmationHandlers() {
    normalizeLegacyConfirmAttributes();

    document.addEventListener(
        "submit",
        (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            if (!form.dataset.confirmMessage) {
                return;
            }

            if (form.dataset.confirmArmed === "1") {
                form.dataset.confirmArmed = "0";
                return;
            }

            event.preventDefault();
            openElementConfirmation(form, () => {
                form.dataset.confirmArmed = "1";
                form.requestSubmit();
            });
        },
        true,
    );

    document.addEventListener("click", (event) => {
        const trigger = event.target.closest(".js-confirmable[data-confirm-message]");
        if (!trigger) {
            return;
        }

        if (trigger instanceof HTMLFormElement) {
            return;
        }

        if (trigger.dataset.confirmArmed === "1") {
            trigger.dataset.confirmArmed = "0";
            return;
        }

        const owningForm = trigger.closest("form");

        event.preventDefault();
        openElementConfirmation(trigger, () => {
            if (trigger.dataset.confirmScript) {
                try {
                    // eslint-disable-next-line no-new-func
                    const callback = new Function(trigger.dataset.confirmScript);
                    callback.call(trigger);
                } catch (scriptError) {
                    console.error("Confirmation action failed:", scriptError);
                }
                return;
            }

            if (owningForm instanceof HTMLFormElement) {
                owningForm.dataset.confirmArmed = "1";
                owningForm.requestSubmit();
                return;
            }

            trigger.dataset.confirmArmed = "1";
            trigger.click();
        });
    });

    window.addEventListener("open-confirmation-modal", (event) => {
        const detail = event.detail || {};
        openElementConfirmation(
            {
                dataset: {
                    confirmTitle: detail.title,
                    confirmMessage: detail.message,
                    confirmButton: detail.confirmText,
                    confirmVariant: detail.variant,
                },
            },
            typeof detail.onConfirm === "function" ? detail.onConfirm : () => {},
        );
    });
}

function renderFlashToasts() {
    const flash = window.__toastrFlash || {};
    const allowedLevels = ["success", "error", "info", "warning"];

    allowedLevels.forEach((level) => {
        const message = flash[level];
        if (typeof message === "string" && message.trim() !== "") {
            window.toast[level](message);
        }
    });
}

Alpine.store("theme", {
    init() {
        const savedTheme = localStorage.getItem("theme");
        const systemTheme = window.matchMedia("(prefers-color-scheme: dark)")
            .matches
            ? "dark"
            : "light";
        this.theme = savedTheme || systemTheme;
        this.applyTheme();
    },

    theme: "light",

    toggle() {
        this.theme = this.theme === "light" ? "dark" : "light";
        localStorage.setItem("theme", this.theme);
        this.applyTheme();
    },

    applyTheme() {
        const html = document.documentElement;
        const isDark = this.theme === "dark";

        html.classList.toggle("dark", isDark);
        html.style.colorScheme = isDark ? "dark" : "light";
        html.style.backgroundColor = isDark ? "#111827" : "#ffffff";
    },
});

// Sidebar Store - Admin only (Click only, no hover)
Alpine.store("sidebar", {
    init() {
        this.isExpanded = window.innerWidth >= 1280;
    },

    isExpanded: true,
    isMobileOpen: false,

    toggleExpanded() {
        this.isExpanded = !this.isExpanded;
        this.isMobileOpen = false;
    },

    toggleMobileOpen() {
        this.isMobileOpen = !this.isMobileOpen;
    },

    setMobileOpen(val) {
        this.isMobileOpen = val;
    },
});

// Start Alpine - IMPORTANT: Do this after defining stores
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        Alpine.start();
        renderFlashToasts();
        initializeConfirmationHandlers();
        initializeCharts();
        initializeCalendar();
    });
} else {
    Alpine.start();
    renderFlashToasts();
    initializeConfirmationHandlers();
    initializeCharts();
    initializeCalendar();
}

// Initialize charts for admin pages
function initializeCharts() {
    // Chart 1
    if (document.querySelector("#chartOne")) {
        import("./components/admin/chart/chart-1")
            .then((module) => module.initChartOne())
            .catch(() => {});
    }
    // Chart 2
    if (document.querySelector("#chartTwo")) {
        import("./components/admin/chart/chart-2")
            .then((module) => module.initChartTwo())
            .catch(() => {});
    }
    // Chart 3
    if (document.querySelector("#chartThree")) {
        import("./components/admin/chart/chart-3")
            .then((module) => module.initChartThree())
            .catch(() => {});
    }
    // Chart 6
    if (document.querySelector("#chartSix")) {
        import("./components/admin/chart/chart-6")
            .then((module) => module.initChartSix())
            .catch(() => {});
    }
    // Chart 8
    if (document.querySelector("#chartEight")) {
        import("./components/admin/chart/chart-8")
            .then((module) => module.initChartEight())
            .catch(() => {});
    }
    // Chart 13
    if (document.querySelector("#chartThirteen")) {
        import("./components/admin/chart/chart-13")
            .then((module) => module.initChartThirteen())
            .catch(() => {});
    }
}

// Initialize calendar for admin pages
function initializeCalendar() {
    if (document.querySelector("#calendar")) {
        import("./components/admin/calendar-init")
            .then((module) => module.calendarInit())
            .catch(() => {});
    }
}
