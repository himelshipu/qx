import { createDashboardSortable } from "../shared/sortable";

const ROOT_SELECTOR = "#featured-collaborations-dashboard";

function buildUrlFromTemplate(template, id) {
    return template.replace("__ID__", String(id));
}

function getJsonHeaders(csrfToken) {
    return {
        "X-CSRF-TOKEN": csrfToken,
        Accept: "application/json",
        "Content-Type": "application/json",
    };
}

class FeaturedCollaborationsDashboardPage {
    constructor(root) {
        this.root = root;
        this.form = root.querySelector("#featured-collaborations-filters-form");
        this.resultsId = "featured-collaborations-results";
        this.filterResultsRoute = root.dataset.filterResultsRoute || "";
        this.reorderRoute = root.dataset.reorderRoute || "";
        this.statusToggleTemplate = root.dataset.statusToggleTemplate || "";
        this.csrfToken = root.dataset.csrfToken || "";
        this.sortable = null;
        this.sortableBody = root.querySelector("#featured-collaborations-sortable");
        this.filterDebounceTimer = null;
        this.activeRequestController = null;
    }

    init() {
        this.bindFilters();
        this.bindEvents();
        this.mountSortable();
    }

    bindFilters() {
        if (!this.form) return;

        const searchInput = this.form.querySelector("#q");
        const statusSelect = this.form.querySelector("#status");
        const typeSelect = this.form.querySelector("#asset_type");

        searchInput?.addEventListener("input", () => {
            clearTimeout(this.filterDebounceTimer);
            this.filterDebounceTimer = setTimeout(() => this.applyFilters(), 350);
        });

        statusSelect?.addEventListener("change", () => this.applyFilters());
        typeSelect?.addEventListener("change", () => this.applyFilters());
    }

    bindEvents() {
        document.addEventListener("change", (event) => {
            const toggle = event.target.closest(".js-featured-collaboration-status-toggle");
            if (!toggle) return;

            const id = Number.parseInt(toggle.dataset.collaborationId || "0", 10);
            if (Number.isInteger(id) && id > 0) {
                this.toggleStatus(id, toggle);
            }
        });

        document.addEventListener("click", (event) => {
            const paginationLink = event.target.closest(`#${this.resultsId} a[href*='page=']`);
            if (!paginationLink) return;

            event.preventDefault();
            const href = paginationLink.getAttribute("href");
            if (href) {
                this.applyFilters(href);
            }
        });
    }

    buildQueryString() {
        const params = new URLSearchParams(new FormData(this.form));
        if (!params.get("q")) params.delete("q");
        if (!params.get("status") || params.get("status") === "all") params.delete("status");
        if (!params.get("asset_type") || params.get("asset_type") === "all") params.delete("asset_type");
        return params.toString();
    }

    async applyFilters(explicitUrl = null) {
        if (!this.form) return;

        const query = explicitUrl
            ? new URL(explicitUrl, window.location.origin).searchParams.toString()
            : this.buildQueryString();

        const requestUrl = `${this.form.action}${query ? `?${query}` : ""}`;
        const resultsUrl = `${this.filterResultsRoute}${query ? `?${query}` : ""}`;

        if (this.activeRequestController) {
            this.activeRequestController.abort();
        }

        this.activeRequestController = new AbortController();

        try {
            const response = await fetch(resultsUrl, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
                signal: this.activeRequestController.signal,
            });

            if (!response.ok) {
                throw new Error("Failed to load featured collaborations.");
            }

            const data = await response.json();
            if (!data.success || typeof data.html !== "string") {
                throw new Error("Invalid featured collaborations payload.");
            }

            const currentResults = document.getElementById(this.resultsId);
            if (currentResults) {
                currentResults.outerHTML = data.html;
                this.mountSortable();
                window.history.replaceState({}, "", requestUrl);
            }
        } catch (error) {
            if (error.name !== "AbortError") {
                window.location.href = requestUrl;
            }
        }
    }

    async toggleStatus(id, checkbox) {
        if (checkbox.disabled || !this.statusToggleTemplate) return;

        checkbox.disabled = true;

        try {
            const url = buildUrlFromTemplate(this.statusToggleTemplate, id);
            const response = await fetch(url, {
                method: "PATCH",
                headers: getJsonHeaders(this.csrfToken),
            });

            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || "Failed to update collaboration status.");
            }

            checkbox.checked = Boolean(data.is_published);
            if (window.toast) {
                window.toast.success(data.message || "Collaboration status updated successfully.");
            }
        } catch (error) {
            checkbox.checked = !checkbox.checked;
            if (window.toast) {
                window.toast.error(error?.message || "Failed to update collaboration status.");
            }
        } finally {
            checkbox.disabled = false;
        }
    }

    mountSortable() {
        this.sortableBody = this.root.querySelector("#featured-collaborations-sortable");

        if (!this.sortableBody || !this.reorderRoute) return;

        if (this.sortable) {
            this.sortable.destroy();
        }

        this.sortable = createDashboardSortable(this.sortableBody, {
            filter: "a,button,input,label,form,.js-confirmable",
            preventOnFilter: false,
            onEnd: async () => {
                const order = this.getOrderedIds();
                if (!order.length) return;

                await this.persistOrder(order);
                this.refreshSortOrderLabels();
            },
        });
    }

    getOrderedIds() {
        return Array.from(this.sortableBody.querySelectorAll("tr[data-collaboration-id]"))
            .map((row) => Number.parseInt(row.dataset.collaborationId || "0", 10))
            .filter((id) => Number.isInteger(id) && id > 0);
    }

    refreshSortOrderLabels() {
        const rows = Array.from(this.sortableBody.querySelectorAll("tr[data-collaboration-id]"));
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
                throw new Error(data.message || "Failed to reorder collaborations.");
            }

            if (window.toast) {
                window.toast.success(data.message || "Collaborations reordered successfully.");
            }
        } catch (error) {
            if (window.toast) {
                window.toast.error(error?.message || "Failed to reorder collaborations.");
            }
            window.location.reload();
        }
    }
}

function initFeaturedCollaborationsDashboardPage() {
    const root = document.querySelector(ROOT_SELECTOR);
    if (!root) return;

    const page = new FeaturedCollaborationsDashboardPage(root);
    page.init();
}

document.addEventListener("DOMContentLoaded", initFeaturedCollaborationsDashboardPage);
