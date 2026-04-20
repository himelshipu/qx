const ROOT_SELECTOR = "#static-pages-dashboard";
const FILTER_FORM_SELECTOR = "#static-pages-filters-form";
const RESULTS_SELECTOR = "#static-pages-results";

const debounce = (fn, wait = 300) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn(...args), wait);
    };
};

const updateUrl = (params) => {
    const url = new URL(window.location.href);
    ["q", "status", "page"].forEach((key) => {
        const value = params.get(key);
        if (value && value !== "all") {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
    });
    window.history.replaceState({}, "", url);
};

const initStaticPagesDashboard = () => {
    const root = document.querySelector(ROOT_SELECTOR);
    if (!root) {
        return;
    }

    const filterForm = root.querySelector(FILTER_FORM_SELECTOR);
    const resultsContainer = root.querySelector(RESULTS_SELECTOR);
    if (!filterForm || !resultsContainer) {
        return;
    }

    const csrfToken = root.dataset.csrfToken || "";
    const filterRoute = root.dataset.filterResultsRoute || "";
    const statusTemplate = root.dataset.statusToggleTemplate || "";

    const bindPagination = () => {
        root.querySelectorAll("[data-pagination-container] a").forEach((link) => {
            link.addEventListener("click", (event) => {
                event.preventDefault();
                const href = link.getAttribute("href");
                if (!href) {
                    return;
                }
                const url = new URL(href, window.location.origin);
                refreshResults(url.searchParams);
            });
        });
    };

    const bindStatusToggles = () => {
        root.querySelectorAll("[data-status-toggle]").forEach((toggle) => {
            toggle.addEventListener("change", async () => {
                if (!statusTemplate) {
                    return;
                }

                const pageId = toggle.dataset.id;
                const previous = !toggle.checked;

                try {
                    const response = await fetch(statusTemplate.replace("__ID__", String(pageId)), {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": csrfToken,
                            "X-Requested-With": "XMLHttpRequest",
                            Accept: "application/json",
                        },
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || "Unable to update page status");
                    }

                    if (window.toast) {
                        window.toast.success(data.message || "Page status updated successfully.");
                    }
                } catch (error) {
                    toggle.checked = previous;
                    if (window.toast) {
                        window.toast.error(error?.message || "Failed to update page status.");
                    }
                }
            });
        });
    };

    const rebind = () => {
        bindStatusToggles();
        bindPagination();
    };

    const refreshResults = async (params = new URLSearchParams(new FormData(filterForm))) => {
        if (!filterRoute) {
            return;
        }

        updateUrl(params);

        const response = await fetch(`${filterRoute}?${params.toString()}`, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "text/html",
            },
        });

        if (!response.ok) {
            return;
        }

        const html = await response.text();
        resultsContainer.outerHTML = html;

        const nextResults = root.querySelector(RESULTS_SELECTOR);
        if (nextResults) {
            rebind();
        }
    };

    const debouncedRefresh = debounce(() => {
        const params = new URLSearchParams(new FormData(filterForm));
        params.delete("page");
        refreshResults(params);
    }, Number.parseInt(root.dataset.searchDebounce || "350", 10));

    filterForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const params = new URLSearchParams(new FormData(filterForm));
        params.delete("page");
        refreshResults(params);
    });

    const searchInput = filterForm.querySelector("[name=\"q\"]");
    if (searchInput) {
        searchInput.addEventListener("input", debouncedRefresh);
    }

    const statusInput = filterForm.querySelector("[name=\"status\"]");
    if (statusInput) {
        statusInput.addEventListener("change", debouncedRefresh);
    }

    rebind();
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initStaticPagesDashboard);
} else {
    initStaticPagesDashboard();
}
