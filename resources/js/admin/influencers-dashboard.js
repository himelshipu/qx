import { createDashboardSortable } from "../shared/sortable";

const ROOT_SELECTOR = "#influencers-dashboard";

function buildUrlFromTemplate(template, influencerId) {
    return template.replace("__ID__", String(influencerId));
}

function getJsonHeaders(csrfToken) {
    return {
        "X-CSRF-TOKEN": csrfToken,
        Accept: "application/json",
        "Content-Type": "application/json",
    };
}

class InfluencersDashboardPage {
    constructor(root) {
        this.root = root;
        this.form = root.querySelector("#influencer-filters-form");
        this.resultsId = "influencers-results";
        this.modal = document.getElementById("feature-position-modal");
        this.featuredList = document.getElementById("featured-list");
        this.featuredCount = document.getElementById("featured-count");
        this.searchResults = document.getElementById("search-results");
        this.featuredSearchInput = document.getElementById("featured-search");
        this.modalLoading = document.getElementById("modal-loading");

        this.filterDebounceTimer = null;
        this.featuredSearchDebounceTimer = null;
        this.activeRequestController = null;
        this.featuredListSortable = null;

        this.csrfToken = root.dataset.csrfToken || "";
        this.maxFeatured = Number.parseInt(root.dataset.maxFeatured || "20", 10);
        this.routes = {
            filterResults: root.dataset.filterResultsRoute || "",
            statusToggleTemplate: root.dataset.statusToggleTemplate || "",
            featuredAddTemplate: root.dataset.featuredAddTemplate || "",
            featuredRemoveTemplate: root.dataset.featuredRemoveTemplate || "",
            featuredList: root.dataset.featuredListRoute || "",
            featuredSearch: root.dataset.featuredSearchRoute || "",
            featuredReorder: root.dataset.featuredReorderRoute || "",
        };
    }

    init() {
        if (!this.form) {
            return;
        }

        this.bindFilters();
        this.bindEvents();
    }

    bindFilters() {
        const searchInput = this.form.querySelector("#q");
        const statusSelect = this.form.querySelector("#status");
        const featuredSelect = this.form.querySelector("#featured");

        searchInput?.addEventListener("input", () => {
            clearTimeout(this.filterDebounceTimer);
            this.filterDebounceTimer = setTimeout(() => {
                this.applyFilters();
            }, 350);
        });

        statusSelect?.addEventListener("change", () => this.applyFilters());
        featuredSelect?.addEventListener("change", () => this.applyFilters());
    }

    bindEvents() {
        document.addEventListener("click", (event) => {
            const featurePositionBtn = event.target.closest("#feature-position-btn");
            if (featurePositionBtn) {
                event.preventDefault();
                this.openFeaturedModal();
                return;
            }

            const paginationLink = event.target.closest(`#${this.resultsId} a[href*='page=']`);
            if (paginationLink) {
                event.preventDefault();
                const href = paginationLink.getAttribute("href");
                if (href) {
                    this.applyFilters(href);
                }
                return;
            }

            const closeModalBtn = event.target.closest(".js-close-featured-modal");
            if (closeModalBtn) {
                event.preventDefault();
                this.closeFeaturedModal();
                return;
            }

            const addFeaturedBtn = event.target.closest(".js-add-featured");
            if (addFeaturedBtn) {
                event.preventDefault();
                if (addFeaturedBtn.disabled) {
                    return;
                }

                const influencerId = Number(addFeaturedBtn.dataset.influencerId);
                if (Number.isInteger(influencerId) && influencerId > 0) {
                    this.addFeaturedInfluencer(influencerId);
                }
                return;
            }

            const removeFeaturedBtn = event.target.closest(".js-remove-featured");
            if (removeFeaturedBtn) {
                event.preventDefault();
                const influencerId = Number(removeFeaturedBtn.dataset.influencerId);
                if (Number.isInteger(influencerId) && influencerId > 0) {
                    this.removeFeaturedInfluencer(influencerId);
                }
                return;
            }

            if (this.modal && event.target === this.modal) {
                this.closeFeaturedModal();
            }
        });

        document.addEventListener("change", (event) => {
            const statusToggle = event.target.closest(".js-influencer-status-toggle");
            if (statusToggle) {
                const influencerId = Number(statusToggle.dataset.influencerId);
                if (Number.isInteger(influencerId) && influencerId > 0) {
                    this.toggleInfluencerStatus(influencerId, statusToggle);
                }

                return;
            }

            const featuredToggle = event.target.closest(".js-influencer-featured-toggle");
            if (featuredToggle) {
                const influencerId = Number(featuredToggle.dataset.influencerId);
                if (Number.isInteger(influencerId) && influencerId > 0) {
                    this.toggleInfluencerFeatured(influencerId, featuredToggle);
                }
            }
        });

        if (this.featuredSearchInput && !this.featuredSearchInput.dataset.bound) {
            this.featuredSearchInput.dataset.bound = "1";
            this.featuredSearchInput.addEventListener("input", (e) => {
                clearTimeout(this.featuredSearchDebounceTimer);
                const query = String(e.target.value || "").trim();

                if (query.length < 2) {
                    this.searchResults.innerHTML = "";
                    this.searchResults.classList.add("hidden");
                    return;
                }

                this.featuredSearchDebounceTimer = setTimeout(() => {
                    this.searchFeaturedInfluencers(query);
                }, 300);
            });
        }
    }

    buildQueryString() {
        const params = new URLSearchParams(new FormData(this.form));
        if (!params.get("q")) params.delete("q");
        if (!params.get("status") || params.get("status") === "all") params.delete("status");
        if (!params.get("featured") || params.get("featured") === "all") params.delete("featured");
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

        try {
            const response = await fetch(resultsUrl, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
                signal: this.activeRequestController.signal,
            });

            if (!response.ok) {
                throw new Error("Failed to load influencer table");
            }

            const data = await response.json();
            if (!data.success || typeof data.html !== "string") {
                throw new Error("Invalid influencer table payload");
            }

            const currentResults = document.getElementById(this.resultsId);
            if (currentResults) {
                currentResults.outerHTML = data.html;
                window.history.replaceState({}, "", requestUrl);
            }
        } catch (error) {
            if (error.name !== "AbortError") {
                window.location.href = requestUrl;
            }
        }
    }

    async toggleInfluencerStatus(influencerId, checkbox) {
        if (checkbox.disabled) {
            return;
        }

        checkbox.disabled = true;

        try {
            const url = buildUrlFromTemplate(this.routes.statusToggleTemplate, influencerId);
            const response = await fetch(url, {
                method: "POST",
                headers: getJsonHeaders(this.csrfToken),
            });

            if (!response.ok) {
                throw new Error("Failed to update status");
            }

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || "Failed to update influencer status.");
            }

            checkbox.checked = Boolean(data.is_active);
            if (window.toast) {
                window.toast.success(data.message || "Influencer status updated successfully.");
            }
        } catch (error) {
            console.error(error);
            if (window.toast) {
                window.toast.error(error?.message || "Unable to update influencer status right now.");
            }
            checkbox.checked = !checkbox.checked;
        } finally {
            checkbox.disabled = false;
        }
    }

    async toggleInfluencerFeatured(influencerId, checkbox) {
        if (checkbox.disabled) {
            return;
        }

        checkbox.disabled = true;

        try {
            const template = checkbox.checked
                ? this.routes.featuredAddTemplate
                : this.routes.featuredRemoveTemplate;

            const url = buildUrlFromTemplate(template, influencerId);
            const response = await fetch(url, {
                method: "POST",
                headers: getJsonHeaders(this.csrfToken),
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || "Failed to update featured status.");
            }

            if (window.toast) {
                window.toast.success(data.message || "Influencer featured status updated successfully.");
            }
        } catch (error) {
            console.error(error);
            if (window.toast) {
                window.toast.error(error?.message || "Unable to update influencer featured status right now.");
            }
            checkbox.checked = !checkbox.checked;
        } finally {
            checkbox.disabled = false;
        }
    }

    async openFeaturedModal() {
        if (!this.modal || !this.featuredList || !this.featuredCount || !this.searchResults || !this.featuredSearchInput || !this.modalLoading) {
            if (window.toast) {
                window.toast.error("Featured modal is not available on this page.");
            }
            return;
        }

        this.modal.classList.remove("hidden");
        document.body.classList.add("overflow-hidden");
        await this.loadFeaturedInfluencers();
    }

    closeFeaturedModal() {
        if (!this.modal || !this.searchResults || !this.featuredSearchInput) {
            return;
        }

        this.modal.classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
        this.searchResults.innerHTML = "";
        this.featuredSearchInput.value = "";
    }

    async loadFeaturedInfluencers() {
        this.modalLoading.classList.remove("hidden");

        try {
            const response = await fetch(this.routes.featuredList, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            });

            const data = await response.json();
            this.modalLoading.classList.add("hidden");

            if (!data.success) {
                throw new Error(data.message || "Failed to load featured influencers");
            }

            if (Number.isInteger(data.data?.maxAllowed)) {
                this.maxFeatured = data.data.maxAllowed;
            }

            this.renderFeaturedList(data.data.featured, data.data.count);
        } catch (error) {
            console.error(error);
            this.modalLoading.classList.add("hidden");
            if (window.toast) {
                window.toast.error(error.message || "Failed to load featured influencers");
            }
        }
    }

    renderFeaturedList(featured, count) {
        this.featuredCount.textContent = count;
        this.featuredList.innerHTML = "";

        if (!featured.length) {
            this.featuredList.innerHTML = `
                <li class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    No featured influencers yet. Search and add influencers to get started.
                </li>
            `;
            return;
        }

        featured.forEach((influencer) => {
            const li = document.createElement("li");
            li.className =
                "flex cursor-move items-center gap-3 p-2.5 transition hover:bg-gray-100 dark:hover:bg-gray-700/50";
            li.dataset.influencerId = influencer.id;

            li.innerHTML = `
                <div class="shrink-0 text-gray-400" title="Drag to reorder">
                    <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8 5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM8 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM8 19a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM14 5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM14 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM14 19a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                    </svg>
                </div>

                <div class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-[10px] font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                    ${influencer.priority}
                </div>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-gray-900 dark:text-white">${influencer.display_name}</p>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <button type="button" class="js-remove-featured text-gray-400 transition hover:text-red-600 dark:hover:text-red-400" data-influencer-id="${influencer.id}" title="Remove from featured">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;

            this.featuredList.appendChild(li);
        });

        if (this.featuredListSortable) {
            this.featuredListSortable.destroy();
        }

        this.featuredListSortable = createDashboardSortable(this.featuredList, {
            onEnd: async () => {
                const rawIds = Array.from(
                    this.featuredList.querySelectorAll("[data-influencer-id]"),
                ).map((el) => Number.parseInt(el.dataset.influencerId || "0", 10));

                const influencerIds = Array.from(
                    new Set(rawIds.filter((id) => Number.isInteger(id) && id > 0)),
                );

                if (!influencerIds.length) {
                    return;
                }

                await this.updateFeaturedOrder(influencerIds);
            },
        });
    }

    async searchFeaturedInfluencers(query) {
        try {
            const response = await fetch(
                `${this.routes.featuredSearch}?q=${encodeURIComponent(query)}`,
                {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                },
            );

            const data = await response.json();

            if (!data.results || !data.results.length) {
                this.searchResults.innerHTML =
                    '<div class="p-3 text-center text-sm text-gray-500">No influencers found</div>';
                this.searchResults.classList.remove("hidden");
                return;
            }

            this.searchResults.innerHTML = data.results
                .map(
                    (influencer) => `
                    <div class="flex items-center gap-3 border-b border-gray-100 p-3 last:border-b-0 dark:border-gray-700">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${influencer.display_name}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${influencer.is_featured ? "Already featured" : "Not featured"}</p>
                        </div>
                        <button
                            type="button"
                            class="js-add-featured rounded bg-indigo-600 px-3 py-1 text-xs font-medium text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                            data-influencer-id="${influencer.id}"
                            ${influencer.is_featured ? "disabled" : ""}
                        >
                            ${influencer.is_featured ? "Featured" : "Add"}
                        </button>
                    </div>
                `,
                )
                .join("");

            this.searchResults.classList.remove("hidden");
        } catch (error) {
            console.error(error);
            if (window.toast) {
                window.toast.error("Failed to search influencers");
            }
        }
    }

    async addFeaturedInfluencer(influencerId) {
        try {
            const url = buildUrlFromTemplate(this.routes.featuredAddTemplate, influencerId);
            const response = await fetch(url, {
                method: "POST",
                headers: getJsonHeaders(this.csrfToken),
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || "Failed to add influencer");
            }

            if (window.toast) {
                window.toast.success(data.message);
            }

            if (data.removedInfluencer && window.toast) {
                window.toast.info(
                    `Removed "${data.removedInfluencer.display_name}" (max ${this.maxFeatured} featured influencers)`,
                );
            }

            this.renderFeaturedList(data.featured, data.featured.length);
            this.featuredSearchInput.value = "";
            this.searchResults.innerHTML = "";
            this.searchResults.classList.add("hidden");
        } catch (error) {
            console.error(error);
            if (window.toast) {
                window.toast.error(error.message || "Failed to add influencer to featured");
            }
        }
    }

    async removeFeaturedInfluencer(influencerId) {
        try {
            const url = buildUrlFromTemplate(this.routes.featuredRemoveTemplate, influencerId);
            const response = await fetch(url, {
                method: "POST",
                headers: getJsonHeaders(this.csrfToken),
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || "Failed to remove influencer");
            }

            if (window.toast) {
                window.toast.success(data.message);
            }

            this.renderFeaturedList(data.featured, data.featured.length);
        } catch (error) {
            console.error(error);
            if (window.toast) {
                window.toast.error(error.message || "Failed to remove influencer from featured");
            }
        }
    }

    async updateFeaturedOrder(influencerIds) {
        try {
            const response = await fetch(this.routes.featuredReorder, {
                method: "POST",
                headers: getJsonHeaders(this.csrfToken),
                body: JSON.stringify({ order: influencerIds }),
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || "Failed to update order");
            }

            this.renderFeaturedList(data.featured, data.featured.length);
            if (window.toast) {
                window.toast.success(data.message || "Featured influencer positions updated successfully.");
            }
        } catch (error) {
            console.error(error);
            if (window.toast) {
                window.toast.error(error.message || "Failed to update featured order");
            }

            this.loadFeaturedInfluencers();
        }
    }
}

function initInfluencersDashboard() {
    const root = document.querySelector(ROOT_SELECTOR);
    if (!root) {
        return;
    }

    const page = new InfluencersDashboardPage(root);
    page.init();
}

document.addEventListener("DOMContentLoaded", initInfluencersDashboard);
