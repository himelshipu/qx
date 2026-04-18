const rootSelector = "[data-packages-dashboard]";

const buildUrl = (baseUrl, params) => {
    const url = new URL(baseUrl, window.location.origin);

    Object.entries(params).forEach(([key, value]) => {
        if (value !== undefined && value !== null && String(value).trim() !== "") {
            url.searchParams.set(key, value);
        }
    });

    return url;
};

const initPackagesDashboard = () => {
    const root = document.querySelector(rootSelector);
    if (!root) {
        return;
    }

    const form = root.querySelector("[data-packages-filter-form]");
    const results = root.querySelector("[data-packages-results]");
    const tableUrl = root.dataset.tableUrl;
    const resetButton = root.querySelector("[data-packages-reset]");
    const searchInput = form?.querySelector("input[name='q']");
    const selects = form ? Array.from(form.querySelectorAll("select")) : [];
    const csrfToken = document.querySelector("meta[name='csrf-token']")?.getAttribute("content") || "";
    let searchTimer = null;

    const syncUrl = (params) => {
        const nextUrl = buildUrl(window.location.pathname, params);
        window.history.replaceState({}, "", `${nextUrl.pathname}${nextUrl.search}`);
    };

    const getNormalizedParams = () => {
        if (!form) {
            return {};
        }

        const params = Object.fromEntries(new FormData(form).entries());

        if (!params.q) {
            delete params.q;
        }

        if (!params.status || params.status === "all") {
            delete params.status;
        }

        if (!params.platform || params.platform === "all") {
            delete params.platform;
        }

        return params;
    };

    const loadFromUrl = async (urlString) => {
        if (!results) {
            return;
        }

        results.classList.add("opacity-60");

        try {
            const response = await fetch(urlString, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "text/html",
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            results.innerHTML = await response.text();
            bindPagination();
        } catch (error) {
            console.error("Failed to load packages table:", error);
        } finally {
            results.classList.remove("opacity-60");
        }
    };

    const loadResults = async () => {
        if (!tableUrl) {
            return;
        }

        const params = getNormalizedParams();
        const url = buildUrl(tableUrl, params);

        await loadFromUrl(url.toString());
        syncUrl(params);
    };

    const bindPagination = () => {
        results?.querySelectorAll("nav[role='navigation'] a[href]")?.forEach((link) => {
            link.addEventListener("click", (event) => {
                event.preventDefault();

                const href = link.getAttribute("href") || "";
                if (!href) {
                    return;
                }

                const url = new URL(href, window.location.origin);
                const currentParams = getNormalizedParams();
                Object.entries(currentParams).forEach(([key, value]) => {
                    url.searchParams.set(key, value);
                });

                loadFromUrl(url.toString());
                window.history.replaceState({}, "", `${url.pathname}${url.search}`);
            });
        });
    };

    const togglePackageStatus = async (checkbox) => {
        if (!(checkbox instanceof HTMLInputElement)) {
            return;
        }

        const url = checkbox.dataset.toggleUrl || "";
        if (!url) {
            return;
        }

        checkbox.disabled = true;

        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || "Failed to update package status.");
            }

            if (typeof data.is_active !== "undefined") {
                checkbox.checked = Boolean(data.is_active);
            }

            if (window.toast) {
                window.toast.success(data.message || "Package status updated successfully.");
            }
        } catch (error) {
            console.error(error);
            checkbox.checked = !checkbox.checked;
            if (window.toast) {
                window.toast.error(error?.message || "Unable to update package status right now.");
            }
        } finally {
            checkbox.disabled = false;
        }
    };

    form?.addEventListener("submit", (event) => {
        event.preventDefault();
        loadResults();
    });

    searchInput?.addEventListener("input", () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(loadResults, 250);
    });

    selects.forEach((select) => {
        select.addEventListener("change", loadResults);
    });

    resetButton?.addEventListener("click", () => {
        if (!form) {
            return;
        }

        form.reset();
        loadResults();
    });

    root.addEventListener("change", (event) => {
        const target = event.target;
        if (target instanceof HTMLInputElement && target.matches("[data-package-toggle-status]")) {
            togglePackageStatus(target);
        }
    });

    bindPagination();
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initPackagesDashboard);
} else {
    initPackagesDashboard();
}
