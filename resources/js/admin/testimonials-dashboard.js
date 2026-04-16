import { createDashboardSortable } from "../shared/sortable";

const ROOT_SELECTOR = "#testimonials-dashboard";

function buildUrlFromTemplate(template, testimonialId) {
    return template.replace("__ID__", String(testimonialId));
}

function getJsonHeaders(csrfToken) {
    return {
        "X-CSRF-TOKEN": csrfToken,
        Accept: "application/json",
        "Content-Type": "application/json",
    };
}

class TestimonialsDashboardPage {
    constructor(root) {
        this.root = root;
        this.form = root.querySelector("#testimonials-filters-form");
        this.resultsId = "testimonials-results";
        this.sortableBody = root.querySelector("#testimonials-sortable");
        this.filterResultsRoute = root.dataset.filterResultsRoute || "";
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
            const statusToggle = event.target.closest(".js-testimonial-status-toggle");
            if (!statusToggle) {
                return;
            }

            const testimonialId = Number.parseInt(statusToggle.dataset.testimonialId || "0", 10);
            if (Number.isInteger(testimonialId) && testimonialId > 0) {
                this.toggleStatus(testimonialId, statusToggle);
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
                throw new Error("Failed to load testimonial table");
            }

            const data = await response.json();
            if (!data.success || typeof data.html !== "string") {
                throw new Error("Invalid testimonial table payload");
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

    async toggleStatus(testimonialId, checkbox) {
        if (checkbox.disabled || !this.statusToggleTemplate) {
            return;
        }

        checkbox.disabled = true;

        try {
            const url = buildUrlFromTemplate(this.statusToggleTemplate, testimonialId);
            const response = await fetch(url, {
                method: "POST",
                headers: getJsonHeaders(this.csrfToken),
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || "Failed to update testimonial status.");
            }

            checkbox.checked = Boolean(data.is_published);

            if (window.toast) {
                window.toast.success(data.message || "Testimonial status updated successfully.");
            }
        } catch (error) {
            checkbox.checked = !checkbox.checked;
            if (window.toast) {
                window.toast.error(error?.message || "Failed to update testimonial status.");
            }
        } finally {
            checkbox.disabled = false;
        }
    }

    mountSortable() {
        this.sortableBody = this.root.querySelector("#testimonials-sortable");

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
        return Array.from(this.sortableBody.querySelectorAll("tr[data-testimonial-id]"))
            .map((row) => Number.parseInt(row.dataset.testimonialId || "0", 10))
            .filter((id) => Number.isInteger(id) && id > 0);
    }

    refreshSortOrderLabels() {
        const rows = Array.from(this.sortableBody.querySelectorAll("tr[data-testimonial-id]"));

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
                throw new Error(data.message || "Failed to reorder testimonials.");
            }

            if (window.toast) {
                window.toast.success(data.message || "Testimonials reordered successfully.");
            }
        } catch (error) {
            if (window.toast) {
                window.toast.error(error?.message || "Failed to reorder testimonials.");
            }
            window.location.reload();
        }
    }
}

function initTestimonialsDashboardPage() {
    const root = document.querySelector(ROOT_SELECTOR);
    if (!root) {
        return;
    }

    const page = new TestimonialsDashboardPage(root);
    page.init();
}

document.addEventListener("DOMContentLoaded", initTestimonialsDashboardPage);
