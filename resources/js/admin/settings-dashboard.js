import { createDashboardSortable } from "../shared/sortable";

const ROOT_SELECTOR = "#settings-footer-dashboard";

function getJsonHeaders(csrfToken) {
    return {
        "X-CSRF-TOKEN": csrfToken,
        Accept: "application/json",
        "Content-Type": "application/json",
    };
}

class SettingsDashboardPage {
    constructor(root) {
        this.root = root;
        this.sortableBody = root.querySelector("#settings-footer-sortable");
        this.reorderRoute = root.dataset.reorderRoute || "";
        this.csrfToken = root.dataset.csrfToken || "";
        this.sortable = null;
    }

    init() {
        this.bindEvents();
        this.mountSortable();
    }

    bindEvents() {
        document.addEventListener("change", (event) => {
            const statusToggle = event.target.closest(".js-page-status-toggle");
            if (!statusToggle) {
                return;
            }

            const pageId = Number.parseInt(statusToggle.dataset.pageId || "0", 10);
            if (Number.isInteger(pageId) && pageId > 0) {
                this.togglePageStatus(pageId, statusToggle);
            }
        });
    }

    async togglePageStatus(pageId, checkbox) {
        if (checkbox.disabled) {
            return;
        }

        checkbox.disabled = true;

        try {
            const response = await fetch(`/dashboard/static-pages/${pageId}/toggle-status`, {
                method: "POST",
                headers: getJsonHeaders(this.csrfToken),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || "Failed to update page status.");
            }

            checkbox.checked = Boolean(data.is_active);

            if (window.toast) {
                window.toast.success(data.message || "Page status updated successfully.");
            }
        } catch (error) {
            checkbox.checked = !checkbox.checked;
            if (window.toast) {
                window.toast.error(error?.message || "Failed to update page status.");
            }
        } finally {
            checkbox.disabled = false;
        }
    }

    mountSortable() {
        if (!this.sortableBody || !this.reorderRoute) {
            return;
        }

        if (this.sortable) {
            this.sortable.destroy();
        }

        this.sortable = createDashboardSortable(this.sortableBody, {
            filter: "a,button,input,label,form,.js-confirmable",
            preventOnFilter: false,
            onEnd: async () => {
                const order = this.getOrderedIds();

                if (!order.length) {
                    return;
                }

                await this.persistOrder(order);
                this.refreshSortOrderLabels();
            },
        });
    }

    getOrderedIds() {
        return Array.from(this.sortableBody.querySelectorAll("tr[data-page-id]"))
            .map((row) => Number.parseInt(row.dataset.pageId || "0", 10))
            .filter((id) => Number.isInteger(id) && id > 0);
    }

    refreshSortOrderLabels() {
        const rows = Array.from(this.sortableBody.querySelectorAll("tr[data-page-id]"));

        rows.forEach((row, index) => {
            const orderCell = row.querySelector(".js-sort-order-value");
            if (orderCell) {
                orderCell.textContent = String(index + 1);
            }
        });
    }

    async persistOrder(order) {
        try {
            const response = await fetch(this.reorderRoute, {
                method: "POST",
                headers: getJsonHeaders(this.csrfToken),
                body: JSON.stringify({ order }),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || "Failed to reorder footer pages.");
            }

            if (window.toast) {
                window.toast.success(data.message || "Footer pages reordered successfully.");
            }
        } catch (error) {
            if (window.toast) {
                window.toast.error(error?.message || "Failed to reorder footer pages.");
            }
            window.location.reload();
        }
    }
}

function initSettingsDashboardPage() {
    const root = document.querySelector(ROOT_SELECTOR);
    if (!root) {
        return;
    }

    const page = new SettingsDashboardPage(root);
    page.init();
}

document.addEventListener("DOMContentLoaded", initSettingsDashboardPage);
