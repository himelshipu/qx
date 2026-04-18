const rootSelector = "[data-support-tickets-dashboard]";

const buildUrl = (baseUrl, params) => {
    const url = new URL(baseUrl, window.location.origin);
    Object.entries(params).forEach(([key, value]) => {
        if (value !== undefined && value !== null && String(value).trim() !== "") {
            url.searchParams.set(key, value);
        }
    });
    return url;
};

const initSupportTicketsDashboard = () => {
    const root = document.querySelector(rootSelector);
    if (!root) {
        return;
    }

    const form = root.querySelector("[data-support-tickets-filter-form]");
    const results = root.querySelector("[data-support-tickets-results]");
    const tableUrl = root.dataset.tableUrl;
    const resetButton = root.querySelector("[data-support-tickets-reset]");
    const searchInput = form?.querySelector("input[name='search']");
    const selects = form ? Array.from(form.querySelectorAll("select")) : [];
    let searchTimer = null;

    const syncUrl = (params) => {
        const nextUrl = buildUrl(window.location.pathname, params);
        window.history.replaceState({}, "", `${nextUrl.pathname}${nextUrl.search}`);
    };

    const loadResults = async () => {
        if (!form) return;

        if (!results || !tableUrl) return;

        const formData = new FormData(form);
        const params = Object.fromEntries(formData.entries());
        const url = buildUrl(tableUrl, params);

        results.classList.add("opacity-60");

        try {
            const response = await fetch(url.toString(), {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "text/html",
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            results.innerHTML = await response.text();
            syncUrl(params);
            bindPagination();
        } catch (error) {
            console.error("Failed to load support tickets table:", error);
        } finally {
            results.classList.remove("opacity-60");
        }
    };

    form?.addEventListener("submit", (event) => {
        event.preventDefault();
        loadResults();
    });

    const bindPagination = () => {
        results?.querySelectorAll("nav[role='navigation'] a[href]")?.forEach((link) => {
            const href = link.getAttribute("href") || "";
            if (!href.includes("support-tickets")) {
                return;
            }

            link.addEventListener("click", (event) => {
                event.preventDefault();
                const url = new URL(href, window.location.origin);
                window.history.replaceState({}, "", `${url.pathname}${url.search}`);
                fetch(url.toString(), {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "text/html",
                    },
                })
                    .then((response) => response.text())
                    .then((html) => {
                        if (results) {
                            results.innerHTML = html;
                            bindPagination();
                        }
                    })
                    .catch((error) => console.error("Pagination load failed:", error));
            });
        });
    };

    searchInput?.addEventListener("input", () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(loadResults, 250);
    });

    selects.forEach((select) => {
        select.addEventListener("change", loadResults);
    });

    resetButton?.addEventListener("click", () => {
        if (!form) return;

        form.reset();
        loadResults();
    });

    bindPagination();
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initSupportTicketsDashboard);
} else {
    initSupportTicketsDashboard();
}
