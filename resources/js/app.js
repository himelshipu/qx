import { createPopper } from "@popperjs/core";
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";
import ApexCharts from "apexcharts";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

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
        initializeCharts();
        initializeCalendar();
    });
} else {
    Alpine.start();
    renderFlashToasts();
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
