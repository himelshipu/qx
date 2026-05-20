import collapse from "@alpinejs/collapse";
import { createPopper } from "@popperjs/core";
import Alpine from "alpinejs";
import ApexCharts from "apexcharts";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";
import "./conversation";
import "./stripe/payment";
import "./admin/blog-dashboard";
import "./admin/categories-dashboard";
import "./admin/category-form";
import "./admin/brands-dashboard";
import "./admin/brand-form";
import "./admin/case-studies-dashboard";
import "./admin/case-study-form";
import "./admin/featured-collaborations-dashboard";
import "./admin/featured-collaboration-form";
import "./admin/faq-items-dashboard";
import "./admin/faq-sections-dashboard";
import "./admin/influencers-dashboard";
import "./admin/campaigns-dashboard";
import "./admin/knowledge-base-dashboard";
import "./admin/settings-dashboard";
import "./admin/testimonials-dashboard";
import "./admin/users-dashboard";
import "./admin/user-role-assignment";
import "./admin/permission-assignment";
import "./admin/communication-sidebar-badges";
import "./admin/support-tickets-dashboard";
import "./admin/packages-dashboard";
import "./admin/static-page-form";
import "./admin/static-pages-dashboard";
import "./frontend/campaigns-index";
import "./frontend/campaigns-create";
import "./frontend/campaigns-show";
import "./frontend/campaigns-negotiation-modal";

document.addEventListener("DOMContentLoaded", () => {
    const modal = document.querySelector("[data-wishlist-modal]");

    if (!modal) {
        return;
    }

    const modalPanel = modal.querySelector("[data-wishlist-modal-panel]");
    const modalTitle = modal.querySelector("[data-wishlist-modal-title]");
    const defaultView = modal.querySelector("[data-wishlist-modal-default-view]");
    const createView = modal.querySelector("[data-wishlist-modal-create-view]");
    const backdrop = modal.querySelector("[data-wishlist-modal-backdrop]");
    const closeButton = modal.querySelector("[data-wishlist-modal-close]");
    const createListButton = modal.querySelector("[data-wishlist-create-list]");
    const backToListsButton = modal.querySelector("[data-wishlist-back-to-lists]");
    const createInput = modal.querySelector("[data-wishlist-create-input]");
    const createSubmit = modal.querySelector("[data-wishlist-create-submit]");
    const existingListsContainer = modal.querySelector("[data-wishlist-existing-lists]");
    const emptyState = modal.querySelector("[data-wishlist-empty-state]");
    const loadingState = modal.querySelector("[data-wishlist-loading-state]");
    const previewImage = modal.querySelector("[data-wishlist-preview-image]");
    const previewPlaceholder = modal.querySelector("[data-wishlist-preview-placeholder]");

    const isAuthenticated = modal.dataset.wishlistAuthenticated === "true";
    const loginUrl = modal.dataset.wishlistLoginUrl || "/login";
    const statusUrl = modal.dataset.wishlistStatusUrl || "";
    const listsUrl = modal.dataset.wishlistListsUrl || "";
    const baseListsUrl = modal.dataset.wishlistBaseUrl || "";
    const removeInfluencerUrl = "/wishlist/influencers";

    let activeTrigger = null;
    let activeInfluencerId = null;
    let currentWishlistedIds = new Set();
    let currentLists = [];
    let isLoadingLists = false;
    let loginRedirectTimer = null;

    const getInfluencerIdFromTrigger = (trigger) => {
        const card = trigger.closest(".influencer-card");
        const rawId = card?.dataset.influencerId || trigger.dataset.influencerId || "";
        const influencerId = Number(rawId);

        return Number.isFinite(influencerId) && influencerId > 0 ? influencerId : null;
    };

    const setBodyScroll = (lock) => {
        document.body.style.overflow = lock ? "hidden" : "";
    };

    const setTriggerState = (trigger, isActive) => {
        if (!trigger) {
            return;
        }

        trigger.dataset.wishlistActive = isActive ? "true" : "false";
        trigger.setAttribute("aria-pressed", isActive ? "true" : "false");

        const heart = trigger.querySelector(".wishlist-heart-icon");
        if (!heart) {
            return;
        }

        heart.classList.toggle("fill-none", !isActive);
        heart.classList.toggle("fill-red-500", isActive);
        heart.classList.toggle("stroke-red-500", isActive);
    };

    const setPreviewImage = (src) => {
        if (!previewImage || !previewPlaceholder) {
            return;
        }

        if (src) {
            previewImage.src = src;
            previewImage.classList.remove("hidden");
            previewPlaceholder.classList.add("hidden");
            return;
        }

        previewImage.removeAttribute("src");
        previewImage.classList.add("hidden");
        previewPlaceholder.classList.remove("hidden");
    };

    const setMode = (mode) => {
        const createMode = mode === "create";

        defaultView?.classList.toggle("hidden", createMode);
        createView?.classList.toggle("hidden", !createMode);

        if (modalTitle) {
            modalTitle.textContent = createMode ? "Name Your List" : "Add to List";
        }

        if (createMode) {
            window.requestAnimationFrame(() => {
                createInput?.focus();
                createInput?.select?.();
            });
        }
    };

    const fetchJson = async (url, options = {}) => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const response = await fetch(url, {
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                ...(csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {}),
                ...(options.headers || {}),
            },
            ...options,
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || "Something went wrong.");
        }

        return data;
    };

    const requireLogin = () => {
        window.toast?.warning("Only logged in user can add to wishlist");

        if (loginRedirectTimer) {
            window.clearTimeout(loginRedirectTimer);
        }

        loginRedirectTimer = window.setTimeout(() => {
            window.location.href = loginUrl;
        }, 1200);
    };

    const renderLists = () => {
        if (!existingListsContainer || !emptyState || !loadingState) {
            return;
        }

        loadingState.classList.add("hidden");
        existingListsContainer.innerHTML = "";

        if (!currentLists.length) {
            existingListsContainer.classList.add("hidden");
            emptyState.classList.remove("hidden");
            return;
        }

        emptyState.classList.add("hidden");
        existingListsContainer.classList.remove("hidden");

        currentLists.forEach((list) => {
            const isIncluded = Boolean(list.contains_influencer);
            const button = document.createElement("button");
            button.type = "button";
            button.className = "wishlist-list-button w-full flex items-center gap-4 rounded-2xl bg-gray-50 p-4 transition-colors hover:bg-gray-100 dark:bg-gray-800/50 dark:hover:bg-gray-800";
            button.dataset.wishlistListId = String(list.id);
            button.dataset.wishlistContainsInfluencer = isIncluded ? "true" : "false";

            button.innerHTML = `
                <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl bg-gray-200 dark:bg-gray-700">
                    ${list.preview_image ? `<img src="${list.preview_image}" alt="${list.name}" class="h-full w-full object-cover" onerror="this.src='/default.webp'"/>` : `<svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>`}
                </div>
                <div class="flex-1 text-left">
                    <p class="text-lg font-bold text-gray-900 dark:text-white">${list.name}</p>
                    <p class="text-sm text-gray-500">${list.items_count} influencers</p>
                </div>
                <span class="rounded-full px-3 py-1 text-xs font-semibold ${isIncluded ? 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'}">
                    ${isIncluded ? 'Remove' : 'Add'}
                </span>
            `;

            button.addEventListener("click", () => {
                void toggleListMembership(list.id, isIncluded);
            });

            existingListsContainer.appendChild(button);
        });
    };

    const loadListsForInfluencer = async (influencerId) => {
        if (!isAuthenticated || !listsUrl || !influencerId) {
            return;
        }

        if (isLoadingLists) {
            return;
        }

        isLoadingLists = true;
        loadingState?.classList.remove("hidden");

        try {
            const url = new URL(listsUrl, window.location.origin);
            url.searchParams.set("influencer_id", String(influencerId));

            const data = await fetchJson(url.toString(), { method: "GET" });

            currentLists = Array.isArray(data.wishlists) ? data.wishlists : [];
            renderLists();
        } catch (error) {
            console.error("Failed to load wishlist lists:", error);
            window.toast?.error(error.message || "Failed to load your lists");
        } finally {
            isLoadingLists = false;
            loadingState?.classList.add("hidden");
        }
    };

    const refreshWishlistStatus = async () => {
        if (!isAuthenticated || !statusUrl) {
            return;
        }

        try {
            const data = await fetchJson(statusUrl, { method: "GET" });
            currentWishlistedIds = new Set((data.wishlisted_influencer_ids || []).map((value) => Number(value)));

            document.querySelectorAll("[data-wishlist-trigger]").forEach((trigger) => {
                const influencerId = getInfluencerIdFromTrigger(trigger);
                if (!influencerId) {
                    return;
                }

                setTriggerState(trigger, currentWishlistedIds.has(influencerId));
            });
        } catch (error) {
            console.error("Failed to load wishlist status:", error);
        }
    };

    const openModal = (trigger) => {
        activeTrigger = trigger;
        activeInfluencerId = getInfluencerIdFromTrigger(trigger);

        const card = trigger.closest(".influencer-card");
        const image = trigger.dataset.wishlistImage || card?.querySelector("img")?.src || "";

        setPreviewImage(image);
        setMode("default");
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        modal.setAttribute("aria-hidden", "false");

        if (modalPanel) {
            requestAnimationFrame(() => {
                modalPanel.classList.remove("scale-95", "opacity-0");
                modalPanel.classList.add("scale-100", "opacity-100");
            });
        }

        setBodyScroll(true);
        void loadListsForInfluencer(activeInfluencerId);
    };

    const closeModal = () => {
        if (modalPanel) {
            modalPanel.classList.add("scale-95", "opacity-0");
            modalPanel.classList.remove("scale-100", "opacity-100");
        }

        modal.setAttribute("aria-hidden", "true");
        setBodyScroll(false);

        window.setTimeout(() => {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            activeTrigger = null;
            activeInfluencerId = null;
            setPreviewImage("");
            createInput.value = "";
            setMode("default");
        }, 300);
    };

    const toggleListMembership = async (wishlistId, isIncluded) => {
        if (!isAuthenticated) {
            requireLogin();
            return;
        }

        if (!activeInfluencerId || !baseListsUrl) {
            return;
        }

        const endpoint = isIncluded
            ? `${baseListsUrl}/${wishlistId}/items/${activeInfluencerId}`
            : `${baseListsUrl}/${wishlistId}/items`;

        try {
            await fetchJson(endpoint, {
                method: isIncluded ? "DELETE" : "POST",
                body: isIncluded ? null : JSON.stringify({ influencer_id: activeInfluencerId }),
            });

            window.toast?.success(isIncluded ? "Removed from list" : "Added to list");
            await loadListsForInfluencer(activeInfluencerId);
            await refreshWishlistStatus();
            closeModal();
        } catch (error) {
            console.error("Failed to update wishlist:", error);
            window.toast?.error(error.message || "Could not update the list");
        }
    };

    const removeInfluencerFromAllLists = async (trigger, influencerId) => {
        if (!isAuthenticated) {
            requireLogin();
            return;
        }

        try {
            await fetchJson(`${removeInfluencerUrl}/${influencerId}`, {
                method: "DELETE",
            });

            setTriggerState(trigger, false);
            currentWishlistedIds.delete(influencerId);
            window.toast?.success("Removed from wishlist");
            await refreshWishlistStatus();
            closeModal();
        } catch (error) {
            console.error("Failed to remove influencer from wishlist:", error);
            window.toast?.error(error.message || "Could not remove from wishlist");
        }
    };

    const createWishlist = async () => {
        if (!isAuthenticated) {
            requireLogin();
            return;
        }

        const listName = createInput.value.trim();

        if (!listName) {
            createInput.focus();
            return;
        }

        if (!baseListsUrl) {
            return;
        }

        try {
            await fetchJson(baseListsUrl, {
                method: "POST",
                body: JSON.stringify({
                    name: listName,
                    influencer_id: activeInfluencerId,
                }),
            });

            window.toast?.success(`Created ${listName}`);
            createInput.value = "";
            await loadListsForInfluencer(activeInfluencerId);
            await refreshWishlistStatus();
            closeModal();
        } catch (error) {
            console.error("Failed to create wishlist:", error);
            window.toast?.error(error.message || "Failed to create list");
        }
    };

    document.addEventListener("click", (event) => {
        const trigger = event.target.closest("[data-wishlist-trigger]");

        if (trigger) {
            event.preventDefault();
            event.stopPropagation();

            if (!isAuthenticated) {
                requireLogin();
                return;
            }

            const influencerId = getInfluencerIdFromTrigger(trigger);
            if (!influencerId) {
                return;
            }

            if (trigger.dataset.wishlistActive === "true") {
                activeTrigger = trigger;
                activeInfluencerId = influencerId;
                void removeInfluencerFromAllLists(trigger, influencerId);
                return;
            }

            activeTrigger = trigger;
            activeInfluencerId = influencerId;
            openModal(trigger);
        }
    });

    createListButton?.addEventListener("click", () => setMode("create"));
    backToListsButton?.addEventListener("click", () => setMode("default"));
    createSubmit?.addEventListener("click", () => {
        void createWishlist();
    });

    createInput?.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
            event.preventDefault();
            void createWishlist();
        }
    });

    closeButton?.addEventListener("click", closeModal);
    backdrop?.addEventListener("click", closeModal);

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && !modal.classList.contains("hidden")) {
            closeModal();
        }
    });

    void refreshWishlistStatus();
});

document.addEventListener("click", async (event) => {
    const removeButton = event.target.closest(".js-wishlist-remove");

    if (!removeButton) {
        return;
    }

    event.preventDefault();

    const removeUrl = removeButton.dataset.removeUrl;
    const card = removeButton.closest("[data-wishlist-item-card]");
    const listSection = removeButton.closest("[data-wishlist-list-section]");

    if (!removeUrl || !card || !listSection) {
        return;
    }

    try {
        const response = await fetch(removeUrl, {
            method: "DELETE",
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                ...(document.querySelector('meta[name="csrf-token"]')?.getAttribute("content")
                    ? {
                          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content"),
                      }
                    : {}),
            },
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || "Failed to remove wishlist item");
        }

        card.remove();

        const countLabel = listSection.querySelector("[data-wishlist-list-count]");
        if (countLabel) {
            const currentCount = Number(countLabel.textContent || 0);
            const nextCount = Number.isFinite(currentCount) ? Math.max(0, currentCount - 1) : 0;
            countLabel.textContent = String(nextCount);
        }

        const remainingCards = listSection.querySelectorAll("[data-wishlist-item-card]");
        if (remainingCards.length === 0) {
            listSection.remove();

            const wishlistPage = document.querySelector("[data-wishlist-page]");
            const sections = wishlistPage?.querySelectorAll("[data-wishlist-list-section]") || [];
            if (sections.length === 0) {
                const emptyState = wishlistPage?.querySelector("[data-wishlist-page-empty]");
                emptyState?.classList.remove("hidden");
            }
        }

        window.toast?.success(data.message || "Removed from wishlist");
    } catch (error) {
        console.error("Failed to remove wishlist item:", error);
        window.toast?.error(error.message || "Could not remove wishlist item");
    }
});

Alpine.plugin(collapse);

window.Alpine = Alpine;
window.flatpickr = flatpickr;
window.ApexCharts = ApexCharts;
window.createPopper = createPopper;

// Custom Toast System using Alpine.js
Alpine.store("toast", {
    toasts: [],
    add(type, message) {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, type, message });

        // Auto remove after 4 seconds
        setTimeout(() => {
            this.remove(id);
        }, 4000);
    },
    remove(id) {
        this.toasts = this.toasts.filter((t) => t.id !== id);
    },
    success(message) {
        this.add("success", message);
    },
    error(message) {
        this.add("error", message);
    },
    info(message) {
        this.add("info", message);
    },
    warning(message) {
        this.add("warning", message);
    },
});

window.toast = {
    success(message) {
        Alpine.store("toast").success(message);
    },
    error(message) {
        Alpine.store("toast").error(message);
    },
    info(message) {
        Alpine.store("toast").info(message);
    },
    warning(message) {
        Alpine.store("toast").warning(message);
    },
};

Alpine.store("confirmModal", {
    isOpen: false,
    title: "Please confirm",
    message: "Are you sure you want to continue?",
    confirmText: "Confirm",
    variant: "danger",
    onConfirm: null,

    open(options = {}) {
        this.title = options.title || "Please confirm";
        this.message = options.message || "Are you sure you want to continue?";
        this.confirmText = options.confirmText || "Confirm";
        this.variant = options.variant || "danger";
        this.onConfirm =
            typeof options.onConfirm === "function" ? options.onConfirm : null;
        this.isOpen = true;
    },

    close() {
        this.isOpen = false;
        this.onConfirm = null;
    },

    confirm() {
        if (typeof this.onConfirm === "function") {
            this.onConfirm();
        }
        this.close();
    },
});

window.confirmationModal = {
    open(options) {
        Alpine.store("confirmModal").open(options);
    },
};

// Role Form Modal Store
Alpine.store("roleFormModal", {
    isOpen: false,
    editingId: null,
    loading: false,
    errors: {},
    formData: {
        name: "",
        description: "",
        is_active: true,
    },

    openCreateModal() {
        this.editingId = null;
        this.formData = {
            name: "",
            description: "",
            is_active: true,
        };
        this.errors = {};
        this.isOpen = true;
    },

    async openEditModal(roleId) {
        this.editingId = roleId;
        await this.loadRoleData(roleId);
        this.isOpen = true;
    },

    closeModal() {
        this.isOpen = false;
        this.editingId = null;
        this.formData = {
            name: "",
            description: "",
            is_active: true,
        };
        this.errors = {};
    },

    async loadRoleData(roleId) {
        try {
            const response = await fetch(`/dashboard/roles/${roleId}/data`);
            const data = await response.json();
            if (data.success) {
                this.formData = {
                    name: data.role.name,
                    description: data.role.description || "",
                    is_active: data.role.is_active,
                };
            }
        } catch (error) {
            console.error("Error loading role:", error);
            window.toast.error("Failed to load role data");
        }
    },

    async submitForm() {
        this.loading = true;
        this.errors = {};

        try {
            const url = this.editingId
                ? `/dashboard/roles/${this.editingId}`
                : "/dashboard/roles";
            const method = this.editingId ? "PUT" : "POST";

            const response = await fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                },
                body: JSON.stringify(this.formData),
            });

            const data = await response.json();

            if (data.success) {
                window.toast.success(data.message);
                this.closeModal();
                setTimeout(() => window.location.reload(), 500);
            } else {
                if (response.status === 422 && data.errors) {
                    this.errors = data.errors;
                    window.toast.error("Please check the form for errors");
                } else {
                    window.toast.error(data.message);
                }
            }
        } catch (error) {
            console.error("Error:", error);
            window.toast.error("An error occurred. Please try again.");
        } finally {
            this.loading = false;
        }
    },
});

// Create a direct reference to the store to avoid re-initialization issues
const roleFormModalStore = Alpine.store("roleFormModal");

window.roleFormModal = {
    openCreate() {
        Alpine.store("roleFormModal").openCreateModal();
    },
    openEdit(roleId) {
        Alpine.store("roleFormModal").openEditModal(roleId);
    },
};

function extractConfirmMessage(raw) {
    if (!raw || typeof raw !== "string") {
        return null;
    }

    const match = raw.match(/confirm\((['"`])([\s\S]*?)\1\)/i);
    return match ? match[2] : null;
}

function parseClickConfirm(raw) {
    if (!raw || typeof raw !== "string") {
        return null;
    }

    const simpleMatch = raw.match(
        /^\s*return\s+confirm\((['"`])([\s\S]*?)\1\)\s*;?\s*$/i,
    );
    if (simpleMatch) {
        return {
            message: simpleMatch[2],
            script: null,
        };
    }

    const conditionalMatch = raw.match(
        /^\s*if\s*\(\s*confirm\((['"`])([\s\S]*?)\1\)\s*\)\s*\{([\s\S]*)\}\s*;?\s*$/i,
    );
    if (conditionalMatch) {
        return {
            message: conditionalMatch[2],
            script: conditionalMatch[3]?.trim() || null,
        };
    }

    return null;
}

function normalizeLegacyConfirmAttributes() {
    document.querySelectorAll("form[onsubmit*='confirm(']").forEach((form) => {
        const onsubmitValue = form.getAttribute("onsubmit");
        const isSimpleConfirm = /^\s*return\s+confirm\(/i.test(
            onsubmitValue || "",
        );
        if (!isSimpleConfirm) {
            return;
        }

        const message = extractConfirmMessage(onsubmitValue);
        if (message) {
            form.dataset.confirmMessage = message;
            form.dataset.confirmVariant =
                form.dataset.confirmVariant || "danger";
            form.classList.add("js-confirmable");
            form.removeAttribute("onsubmit");
        }
    });

    document
        .querySelectorAll("button[onclick*='confirm('], a[onclick*='confirm(']")
        .forEach((element) => {
            const parsed = parseClickConfirm(element.getAttribute("onclick"));
            if (parsed?.message) {
                element.dataset.confirmMessage = parsed.message;
                element.dataset.confirmVariant =
                    element.dataset.confirmVariant || "danger";
                if (parsed.script) {
                    element.dataset.confirmScript = parsed.script;
                }
                element.classList.add("js-confirmable");
                element.removeAttribute("onclick");
            }
        });
}

function openElementConfirmation(element, onConfirm) {
    const fallbackTitle =
        element.dataset.confirmVariant === "danger"
            ? "Confirm deletion"
            : "Please confirm";

    window.confirmationModal.open({
        title: element.dataset.confirmTitle || fallbackTitle,
        message:
            element.dataset.confirmMessage ||
            "Are you sure you want to continue?",
        confirmText: element.dataset.confirmButton || "Confirm",
        variant: element.dataset.confirmVariant || "danger",
        onConfirm,
    });
}

function initializeConfirmationHandlers() {
    if (window.__rockiesConfirmHandlersInitialized) {
        return;
    }

    window.__rockiesConfirmHandlersInitialized = true;
    normalizeLegacyConfirmAttributes();

    document.addEventListener(
        "submit",
        (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            if (!form.dataset.confirmMessage) {
                return;
            }

            if (form.dataset.confirmArmed === "1") {
                form.dataset.confirmArmed = "0";
                return;
            }

            event.preventDefault();
            openElementConfirmation(form, () => {
                form.dataset.confirmArmed = "1";
                form.requestSubmit();
            });
        },
        true,
    );

    document.addEventListener("click", (event) => {
        const trigger = event.target.closest(
            ".js-confirmable[data-confirm-message]",
        );
        if (!trigger) {
            return;
        }

        if (trigger instanceof HTMLFormElement) {
            return;
        }

        if (trigger.dataset.confirmArmed === "1") {
            trigger.dataset.confirmArmed = "0";
            return;
        }

        const owningForm = trigger.closest("form");

        event.preventDefault();
        openElementConfirmation(trigger, () => {
            if (trigger.dataset.confirmScript) {
                try {
                    // eslint-disable-next-line no-new-func
                    const callback = new Function(
                        trigger.dataset.confirmScript,
                    );
                    callback.call(trigger);
                } catch (scriptError) {
                    console.error("Confirmation action failed:", scriptError);
                }
                return;
            }

            if (owningForm instanceof HTMLFormElement) {
                owningForm.dataset.confirmArmed = "1";
                owningForm.requestSubmit();
                return;
            }

            trigger.dataset.confirmArmed = "1";
            trigger.click();
        });
    });

    window.addEventListener("open-confirmation-modal", (event) => {
        const detail = event.detail || {};
        openElementConfirmation(
            {
                dataset: {
                    confirmTitle: detail.title,
                    confirmMessage: detail.message,
                    confirmButton: detail.confirmText,
                    confirmVariant: detail.variant,
                },
            },
            typeof detail.onConfirm === "function"
                ? detail.onConfirm
                : () => {},
        );
    });
}

function renderFlashToasts() {
    const flash = window.__toastrFlash || {};
    const allowedLevels = ["success", "error", "info", "warning"];

    allowedLevels.forEach((level) => {
        const message = flash[level];
        if (typeof message === "string" && message.trim() !== "") {
            window.toast[level](message);
        }
    });
}

Alpine.store("theme", {
    init() {
        const savedTheme = localStorage.getItem("theme");
        const systemTheme = window.matchMedia("(prefers-color-scheme: dark)")
            .matches
            ? "dark"
            : "light";
        this.theme = savedTheme || systemTheme;
        this.applyTheme();
    },

    theme: "light",

    toggle() {
        this.theme = this.theme === "light" ? "dark" : "light";
        localStorage.setItem("theme", this.theme);
        this.applyTheme();
    },

    applyTheme() {
        const html = document.documentElement;
        const isDark = this.theme === "dark";

        html.classList.toggle("dark", isDark);
        html.style.colorScheme = isDark ? "dark" : "light";
        html.style.backgroundColor = isDark ? "#111827" : "#ffffff";
    },
});

// Sidebar Store - Admin only (Click only, no hover)
Alpine.store("sidebar", {
    init() {
        this.isExpanded = window.innerWidth >= 1280;
    },

    isExpanded: true,
    isMobileOpen: false,

    toggleExpanded() {
        this.isExpanded = !this.isExpanded;
        this.isMobileOpen = false;
    },

    toggleMobileOpen() {
        this.isMobileOpen = !this.isMobileOpen;
    },

    setMobileOpen(val) {
        this.isMobileOpen = val;
    },
});

function bootRockiesApp() {
    if (window.__rockiesAppBooted === true) {
        return;
    }

    window.__rockiesAppBooted = true;

    if (!window.__rockiesAlpineStarted) {
        Alpine.start();
        window.__rockiesAlpineStarted = true;
    }

    renderFlashToasts();
    initializeConfirmationHandlers();
    initializeCharts();
    initializeCalendar();
}

// Start Alpine exactly once, even if this bundle is evaluated more than once.
if (document.readyState === "loading") {
    if (!window.__rockiesBootListenerRegistered) {
        window.__rockiesBootListenerRegistered = true;
        document.addEventListener("DOMContentLoaded", bootRockiesApp, {
            once: true,
        });
    }
} else {
    bootRockiesApp();
}

// Initialize charts for admin pages
function initializeCharts() {
    // Chart 1
    if (document.querySelector("#chartOne")) {
        import("./components/admin/chart/chart-1")
            .then((module) => module.initChartOne())
            .catch(() => {});
    }
    // Chart 2
    if (document.querySelector("#chartTwo")) {
        import("./components/admin/chart/chart-2")
            .then((module) => module.initChartTwo())
            .catch(() => {});
    }
    // Chart 3
    if (document.querySelector("#chartThree")) {
        import("./components/admin/chart/chart-3")
            .then((module) => module.initChartThree())
            .catch(() => {});
    }
    // Chart 6
    if (document.querySelector("#chartSix")) {
        import("./components/admin/chart/chart-6")
            .then((module) => module.initChartSix())
            .catch(() => {});
    }
    // Chart 8
    if (document.querySelector("#chartEight")) {
        import("./components/admin/chart/chart-8")
            .then((module) => module.initChartEight())
            .catch(() => {});
    }
    // Chart 13
    if (document.querySelector("#chartThirteen")) {
        import("./components/admin/chart/chart-13")
            .then((module) => module.initChartThirteen())
            .catch(() => {});
    }
}

// Initialize calendar for admin pages
function initializeCalendar() {
    if (document.querySelector("#calendar")) {
        import("./components/admin/calendar-init")
            .then((module) => module.calendarInit())
            .catch(() => {});
    }
}
