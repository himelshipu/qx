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

/**
 * Initialize a searchable combobox.
 *
 * Markup contract:
 *   <div data-influencer-combobox>
 *     <input type="hidden" name="..." data-influencer-value>
 *     <input type="text"      data-influencer-input>
 *     <button                 data-influencer-clear>×</button>
 *     <ul     data-influencer-list>
 *       <li  data-influencer-option data-value=".." data-label="..">..</li>
 *       <li  data-influencer-empty>No matches found</li>
 *     </ul>
 *   </div>
 *
 * @param {HTMLElement} root container element
 * @param {{ onChange: () => void }} hooks onChange fires after a selection (or clear)
 */
const initInfluencerCombobox = (root, hooks = {}) => {
    const container = root.querySelector("[data-influencer-combobox]");
    if (!container) {
        return;
    }

    const hidden = container.querySelector("[data-influencer-value]");
    const input = container.querySelector("[data-influencer-input]");
    const list = container.querySelector("[data-influencer-list]");
    const clearBtn = container.querySelector("[data-influencer-clear]");
    const emptyState = container.querySelector("[data-influencer-empty]");
    const options = list ? Array.from(list.querySelectorAll("[data-influencer-option]")) : [];

    if (!hidden || !input || !list || options.length === 0) {
        return;
    }

    let activeIndex = -1;
    let suppressBlur = false;

    const visibleOptions = () => options.filter((opt) => !opt.classList.contains("hidden"));

    const openList = () => {
        list.classList.remove("hidden");
        input.setAttribute("aria-expanded", "true");
    };

    const closeList = () => {
        list.classList.add("hidden");
        input.setAttribute("aria-expanded", "false");
        activeIndex = -1;
        clearActive();
    };

    const clearActive = () => {
        options.forEach((opt) => opt.setAttribute("aria-selected", "false"));
    };

    const syncClearButton = () => {
        if (!clearBtn) {
            return;
        }
        const hasSelection = hidden.value && hidden.value !== "all";
        clearBtn.classList.toggle("hidden", !hasSelection);
        clearBtn.classList.toggle("flex", hasSelection);
    };

    const syncVisibleFromHidden = () => {
        const current = hidden.value || "all";
        const match = options.find((opt) => opt.dataset.value === current);
        clearActive();
        if (match) {
            input.value = match.dataset.label || "";
            match.setAttribute("aria-selected", "true");
        } else {
            input.value = "";
        }
        syncClearButton();
    };

    const filterOptions = (term) => {
        const needle = term.trim().toLowerCase();
        let visibleCount = 0;
        let firstVisible = null;

        options.forEach((opt) => {
            const label = (opt.dataset.label || "").toLowerCase();
            const matches = needle === "" || label.includes(needle);
            opt.classList.toggle("hidden", !matches);
            if (matches) {
                visibleCount++;
                if (!firstVisible) {
                    firstVisible = opt;
                }
            }
            opt.setAttribute("aria-selected", "false");
        });

        if (emptyState) {
            emptyState.classList.toggle("hidden", visibleCount !== 0);
        }

        activeIndex = firstVisible ? options.indexOf(firstVisible) : -1;
        if (activeIndex >= 0 && options[activeIndex]) {
            options[activeIndex].setAttribute("aria-selected", "true");
            options[activeIndex].scrollIntoView({ block: "nearest" });
        }
    };

    const selectOption = (option, { fireChange = true } = {}) => {
        if (!option) {
            return;
        }
        const value = option.dataset.value || "all";
        hidden.value = value;
        input.value = option.dataset.label || "";
        clearActive();
        option.setAttribute("aria-selected", "true");
        closeList();
        syncClearButton();
        if (fireChange && typeof hooks.onChange === "function") {
            hooks.onChange();
        }
    };

    const clearSelection = () => {
        hidden.value = "all";
        input.value = "";
        filterOptions("");
        syncClearButton();
        closeList();
        if (typeof hooks.onChange === "function") {
            hooks.onChange();
        }
    };

    const moveActive = (delta) => {
        const visible = visibleOptions();
        if (visible.length === 0) {
            return;
        }

        const currentVisibleIndex = activeIndex >= 0
            ? visible.indexOf(options[activeIndex])
            : -1;
        let nextVisibleIndex = currentVisibleIndex + delta;
        if (nextVisibleIndex < 0) {
            nextVisibleIndex = visible.length - 1;
        } else if (nextVisibleIndex >= visible.length) {
            nextVisibleIndex = 0;
        }

        const nextOption = visible[nextVisibleIndex];
        clearActive();
        activeIndex = options.indexOf(nextOption);
        nextOption.setAttribute("aria-selected", "true");
        nextOption.scrollIntoView({ block: "nearest" });
    };

    input.addEventListener("focus", () => {
        openList();
        // re-apply current input value as filter (so opening shows everything if empty,
        // or filters if user is mid-search)
        filterOptions(input.value);
    });

    input.addEventListener("click", () => {
        openList();
        filterOptions(input.value);
    });

    input.addEventListener("input", () => {
        openList();
        filterOptions(input.value);
    });

    input.addEventListener("keydown", (event) => {
        switch (event.key) {
            case "ArrowDown":
                event.preventDefault();
                if (list.classList.contains("hidden")) {
                    openList();
                    filterOptions(input.value);
                }
                moveActive(1);
                break;
            case "ArrowUp":
                event.preventDefault();
                if (list.classList.contains("hidden")) {
                    openList();
                    filterOptions(input.value);
                }
                moveActive(-1);
                break;
            case "Enter":
                if (activeIndex >= 0 && options[activeIndex]) {
                    event.preventDefault();
                    selectOption(options[activeIndex]);
                }
                break;
            case "Escape":
                event.preventDefault();
                // Revert visible text back to selected label, close list
                syncVisibleFromHidden();
                closeList();
                break;
            case "Tab":
                closeList();
                break;
            default:
                break;
        }
    });

    input.addEventListener("blur", () => {
        // Delay so option click registers before we close
        window.setTimeout(() => {
            if (suppressBlur) {
                suppressBlur = false;
                return;
            }
            syncVisibleFromHidden();
            closeList();
        }, 120);
    });

    options.forEach((option) => {
        option.addEventListener("mousedown", (event) => {
            // Prevent input from losing focus before click registers
            event.preventDefault();
        });
        option.addEventListener("click", (event) => {
            event.preventDefault();
            suppressBlur = true;
            selectOption(option);
        });
    });

    clearBtn?.addEventListener("mousedown", (event) => {
        event.preventDefault();
    });
    clearBtn?.addEventListener("click", (event) => {
        event.preventDefault();
        clearSelection();
        input.focus();
    });

    // Initial sync from the hidden value (set by server-side render)
    syncVisibleFromHidden();
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

        if (!params.influencer_id || params.influencer_id === "all") {
            delete params.influencer_id;
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

    // Initialise the influencer combobox; fires loadResults() on selection / clear
    initInfluencerCombobox(root, { onChange: loadResults });

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
        // After form.reset() restores the hidden input to its default "all",
        // re-sync the combobox visible state.
        window.setTimeout(() => {
            const hidden = root.querySelector("[data-influencer-value]");
            if (hidden) {
                hidden.value = hidden.defaultValue || "all";
                const input = root.querySelector("[data-influencer-input]");
                const list = root.querySelector("[data-influencer-list]");
                if (input && list) {
                    list.querySelectorAll("[data-influencer-option]").forEach((opt) => {
                        opt.classList.remove("hidden");
                        opt.setAttribute("aria-selected", opt.dataset.value === hidden.value ? "true" : "false");
                    });
                    const emptyState = list.querySelector("[data-influencer-empty]");
                    if (emptyState) {
                        emptyState.classList.add("hidden");
                    }
                    const match = list.querySelector(`[data-influencer-option][data-value="${hidden.value}"]`);
                    input.value = match ? (match.dataset.label || "") : "";
                    const clearBtn = root.querySelector("[data-influencer-clear]");
                    if (clearBtn) {
                        const hasSelection = hidden.value && hidden.value !== "all";
                        clearBtn.classList.toggle("hidden", !hasSelection);
                        clearBtn.classList.toggle("flex", hasSelection);
                    }
                    list.classList.add("hidden");
                }
            }
            loadResults();
        }, 0);
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
