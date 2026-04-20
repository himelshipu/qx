import { createDashboardSortable } from "../shared/sortable";

const ROOT_SELECTOR = "#case-studies-dashboard";

function buildUrlFromTemplate(template, caseStudyId) {
    return template.replace("__ID__", String(caseStudyId));
}

function getJsonHeaders(csrfToken) {
    return {
        "X-CSRF-TOKEN": csrfToken,
        Accept: "application/json",
        "Content-Type": "application/json",
    };
}

class CaseStudiesDashboardPage {
    constructor(root) {
        this.root = root;
        this.form = root.querySelector("#case-studies-filters-form");
        this.resultsId = "case-studies-results";
        this.sortableBody = root.querySelector("#case-studies-sortable");
        this.reorderRoute = root.dataset.reorderRoute || "";
        this.statusToggleTemplate = root.dataset.statusToggleTemplate || "";
        this.csrfToken = root.dataset.csrfToken || "";
        this.sortable = null;
        this.filterDebounceTimer = null;
        this.activeRequestController = null;
    }

    init() {
        this.bindFilters();
        this.bindEvents();
        this.mountSortable();
    }

    bindFilters() {
        if (!this.form) {
            return;
        }

        const searchInput = this.form.querySelector("#q");
        const statusSelect = this.form.querySelector("#status");

        searchInput?.addEventListener("input", () => {
            clearTimeout(this.filterDebounceTimer);
            this.filterDebounceTimer = setTimeout(() => {
                this.applyFilters();
            }, 350);
        });

        statusSelect?.addEventListener("change", () => this.applyFilters());
    }

    bindEvents() {
        document.addEventListener("change", (event) => {
            const statusToggle = event.target.closest(".js-case-study-status-toggle");
            if (!statusToggle) {
                return;
            }

            const caseStudyId = Number.parseInt(statusToggle.dataset.caseStudyId || "0", 10);
            if (Number.isInteger(caseStudyId) && caseStudyId > 0) {
                this.toggleCaseStudyStatus(caseStudyId, statusToggle);
            }
        });

        document.addEventListener("click", (event) => {
            const paginationLink = event.target.closest(`#${this.resultsId} a[href*='page=']`);
            if (!paginationLink) {
                return;
            }

            event.preventDefault();
            const href = paginationLink.getAttribute("href");
            if (href) {
                this.applyFilters(href);
            }
        });
    }

    async toggleCaseStudyStatus(caseStudyId, checkbox) {
        if (checkbox.disabled || !this.statusToggleTemplate) {
            return;
        }

        checkbox.disabled = true;

        try {
            const url = buildUrlFromTemplate(this.statusToggleTemplate, caseStudyId);
            const response = await fetch(url, {
                method: "POST",
                headers: getJsonHeaders(this.csrfToken),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || "Failed to update case study status.");
            }

            checkbox.checked = Boolean(data.is_published);

            if (window.toast) {
                window.toast.success(data.message || "Case study status updated successfully.");
            }

            await this.applyFilters(window.location.href);
        } catch (error) {
            checkbox.checked = !checkbox.checked;
            if (window.toast) {
                window.toast.error(error?.message || "Failed to update case study status.");
            }
        } finally {
            checkbox.disabled = false;
        }
    }

    mountSortable() {
        this.sortableBody = this.root.querySelector("#case-studies-sortable");

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

    buildQueryString() {
        if (!this.form) {
            return "";
        }

        const params = new URLSearchParams(new FormData(this.form));
        if (!params.get("q")) params.delete("q");
        if (!params.get("status") || params.get("status") === "all") params.delete("status");
        return params.toString();
    }

    async applyFilters(explicitUrl = null) {
        if (!this.form) {
            return;
        }

        const query = explicitUrl
            ? new URL(explicitUrl, window.location.origin).searchParams.toString()
            : this.buildQueryString();

        const requestUrl = `${this.form.action}${query ? `?${query}` : ""}`;

        if (this.activeRequestController) {
            this.activeRequestController.abort();
        }

        this.activeRequestController = new AbortController();

        try {
            const response = await fetch(requestUrl, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
                signal: this.activeRequestController.signal,
            });

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");

            const newResults = doc.getElementById(this.resultsId);
            const currentResults = document.getElementById(this.resultsId);

            if (newResults && currentResults) {
                currentResults.outerHTML = newResults.outerHTML;
                this.mountSortable();
                window.history.replaceState({}, "", requestUrl);
            }
        } catch (error) {
            if (error.name !== "AbortError") {
                window.location.href = requestUrl;
            }
        }
    }

    getOrderedIds() {
        return Array.from(this.sortableBody.querySelectorAll("tr[data-case-study-id]"))
            .map((row) => Number.parseInt(row.dataset.caseStudyId || "0", 10))
            .filter((id) => Number.isInteger(id) && id > 0);
    }

    refreshSortOrderLabels() {
        const rows = Array.from(this.sortableBody.querySelectorAll("tr[data-case-study-id]"));

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
                throw new Error(data.message || "Failed to reorder case studies.");
            }

            if (window.toast) {
                window.toast.success(data.message || "Case studies reordered successfully.");
            }
        } catch (error) {
            if (window.toast) {
                window.toast.error(error?.message || "Failed to reorder case studies.");
            }
            window.location.reload();
        }
    }
}

function initCaseStudiesDashboardPage() {
    const root = document.querySelector(ROOT_SELECTOR);
    if (!root) {
        return;
    }

    const page = new CaseStudiesDashboardPage(root);
    page.init();
}

document.addEventListener("DOMContentLoaded", initCaseStudiesDashboardPage);
