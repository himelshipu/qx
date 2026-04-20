const ROOT_SELECTOR = "#campaigns-dashboard";

class CampaignsDashboardPage {
    constructor(root) {
        this.root = root;
        this.form = root.querySelector("#campaign-filters-form");
        this.resultsId = "campaigns-results";
        this.filterDebounceTimer = null;
        this.activeRequestController = null;
        this.routes = {
            filterResults: root.dataset.filterResultsRoute || "",
        };
    }

    init() {
        if (!this.form || !this.routes.filterResults) {
            return;
        }

        this.bindFilters();
        this.bindEvents();
    }

    bindFilters() {
        const searchInput = this.form.querySelector("#q");
        const statusSelect = this.form.querySelector("#status");
        const typeSelect = this.form.querySelector("#type");

        searchInput?.addEventListener("input", () => {
            window.clearTimeout(this.filterDebounceTimer);
            this.filterDebounceTimer = window.setTimeout(() => {
                this.applyFilters();
            }, 350);
        });

        statusSelect?.addEventListener("change", () => this.applyFilters());
        typeSelect?.addEventListener("change", () => this.applyFilters());
    }

    bindEvents() {
        this.form?.addEventListener("submit", (event) => {
            event.preventDefault();
            this.applyFilters();
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
        const params = new URLSearchParams(new FormData(this.form));

        if (!params.get("q")) {
            params.delete("q");
        }

        if (!params.get("status") || params.get("status") === "all") {
            params.delete("status");
        }

        if (!params.get("type") || params.get("type") === "all") {
            params.delete("type");
        }

        return params.toString();
    }

    async applyFilters(explicitUrl = null) {
        const query = explicitUrl
            ? new URL(explicitUrl, window.location.origin).searchParams.toString()
            : this.buildQueryString();

        const requestUrl = `${this.form.action}${query ? `?${query}` : ""}`;
        const resultsUrl = `${this.routes.filterResults}${query ? `?${query}` : ""}`;

        if (this.activeRequestController) {
            this.activeRequestController.abort();
        }

        this.activeRequestController = new AbortController();

        const currentResults = document.getElementById(this.resultsId);
        currentResults?.classList.add("opacity-60");

        try {
            const response = await fetch(resultsUrl, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
                signal: this.activeRequestController.signal,
            });

            if (!response.ok) {
                throw new Error("Failed to load campaign table");
            }

            const data = await response.json();
            if (!data.success || typeof data.html !== "string") {
                throw new Error("Invalid campaign table payload");
            }

            const nextResults = document.getElementById(this.resultsId);
            if (nextResults) {
                nextResults.outerHTML = data.html;
                window.history.replaceState({}, "", requestUrl);
            }
        } catch (error) {
            if (error.name !== "AbortError") {
                window.location.href = requestUrl;
            }
        } finally {
            document.getElementById(this.resultsId)?.classList.remove("opacity-60");
        }
    }
}

const initCampaignsDashboard = () => {
    const root = document.querySelector(ROOT_SELECTOR);
    if (!root) {
        return;
    }

    const page = new CampaignsDashboardPage(root);
    page.init();
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initCampaignsDashboard);
} else {
    initCampaignsDashboard();
}
