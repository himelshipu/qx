/**
 * Campaign Negotiation Modal
 * Handles the modal dialog for negotiating campaign applications
 * Fixes pricing logic flaws and proper state management
 */

document.addEventListener("DOMContentLoaded", function () {
    // Setup event delegation for negotiate buttons
    document.addEventListener("click", function (e) {
        if (e.target.closest(".js-negotiate-btn")) {
            const btn = e.target.closest(".js-negotiate-btn");
            const appId = btn.dataset.appId;
            const campaignId = btn.dataset.campaignId;
            const actionUrl = btn.dataset.actionUrl;
            const status = btn.dataset.status;
            const influencerName = btn.dataset.influencerName;
            const influencerOffer = btn.dataset.influencerOffer;
            const brandOffer = btn.dataset.brandOffer;
            const lastCounterBy = btn.dataset.lastCounterBy;

            window.negotiationModalState.openModal(
                appId,
                campaignId,
                actionUrl,
                status,
                influencerName,
                influencerOffer,
                brandOffer,
                lastCounterBy,
            );
        }
    });
});

// Global state for negotiation modal
window.negotiationModalState = {
    open: false,
    appId: null,
    campaignId: null,
    actionUrl: "",
    status: "",
    influencerName: "",
    influencerOffer: "",
    brandOffer: "",
    lastCounterBy: "",
    newCounterPrice: "",
    isSubmitting: false,

    openModal(
        appId,
        campaignId,
        actionUrl,
        status,
        influencerName,
        influencerOffer,
        brandOffer,
        lastCounterBy,
    ) {
        this.appId = appId;
        this.campaignId = campaignId;
        this.actionUrl = actionUrl;
        this.status = status;
        this.influencerName = influencerName;
        this.influencerOffer = influencerOffer
            ? parseFloat(influencerOffer)
            : "";
        this.brandOffer = brandOffer ? parseFloat(brandOffer) : "";
        this.lastCounterBy = lastCounterBy;
        this.newCounterPrice = "";
        this.open = true;
        document.body.style.overflow = "hidden";

        // Dispatch custom event to trigger Alpine
        window.dispatchEvent(
            new CustomEvent("negotiation-modal-open", {
                detail: { ...this },
            }),
        );
    },

    closeModal() {
        this.open = false;
        this.resetForm();
        document.body.style.overflow = "";
    },

    resetForm() {
        this.newCounterPrice = "";
        this.isSubmitting = false;
    },

    async acceptApplication() {
        await this.submitAction("accept");
    },

    async counterApplication() {
        if (!this.newCounterPrice || this.newCounterPrice <= 0) {
            window.toast?.error("Please enter a valid counter offer");
            return;
        }
        await this.submitAction("counter", this.newCounterPrice);
    },

    async rejectApplication() {
        await this.submitAction("decline");
    },

    async submitAction(action, brandOffer = null) {
        this.isSubmitting = true;

        try {
            const formData = new FormData();
            formData.append("action", action);

            if (brandOffer !== null) {
                formData.append("brand_offer", brandOffer);
            }

            // Get CSRF token
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");
            if (csrfToken) {
                formData.append("_token", csrfToken);
            }

            const response = await fetch(this.actionUrl, {
                method: "POST",
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                window.toast?.success(
                    data.message || `Application ${action} successfully`,
                );
                this.closeModal();

                // Reload the page to reflect changes
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                window.toast?.error(data.message || "An error occurred");
                this.isSubmitting = false;
            }
        } catch (error) {
            console.error("Error:", error);
            window.toast?.error(
                "Failed to update application. Please try again.",
            );
            this.isSubmitting = false;
        }
    },
};

// Alpine.js component function for the modal
window.negotiationModal = function () {
    return {
        open: false,
        status: "",
        influencerName: "",
        influencerOffer: "",
        brandOffer: "",
        lastCounterBy: "",
        newCounterPrice: "",
        isSubmitting: false,

        init() {
            // Listen for modal open event
            window.addEventListener("negotiation-modal-open", (event) => {
                const state = event.detail;
                this.status = state.status;
                this.influencerName = state.influencerName;
                this.influencerOffer = state.influencerOffer;
                this.brandOffer = state.brandOffer;
                this.lastCounterBy = state.lastCounterBy;
                this.newCounterPrice = "";
                this.open = true;

                // Trigger UI updates
                this.$nextTick(() => {
                    this.updateButtonVisibility();
                });
            });
        },

        get statusLabel() {
            const labels = {
                invited: "No price set yet - send your offer",
                applied: "Influencer initial offer pending",
                countered_by_brand: "Waiting for influencer response",
                countered_by_influencer: "Waiting for your response",
            };
            return labels[this.status] || this.status;
        },

        get canCounter() {
            // Brand can counter when: status = 'invited' (initial offer), 'applied' (respond to influencer's initial),
            // or 'countered_by_influencer' (respond to influencer's counter)
            return in_array(this.status, [
                "invited",
                "applied",
                "countered_by_influencer",
            ]);
        },

        get canAccept() {
            // Brand can accept when: status = 'applied' (accept influencer's initial)
            //                     or 'countered_by_influencer' (accept influencer's counter)
            // Influencer can accept when: status = 'countered_by_brand'

            if (this.status === "applied" && this.influencerOffer) {
                return true; // Brand accepts influencer's initial offer
            }
            if (
                this.status === "countered_by_influencer" &&
                this.influencerOffer
            ) {
                return true; // Brand accepts influencer's counter offer
            }
            if (this.status === "countered_by_brand" && this.brandOffer) {
                return true; // Influencer accepts brand's counter offer
            }
            return false;
        },

        get acceptDisabledReason() {
            if (!this.canAccept) {
                return "No valid offer to accept in this state";
            }
            return "";
        },

        get waitingForResponse() {
            return this.status === "countered_by_brand";
        },

        get waitingMessage() {
            if (this.status === "countered_by_brand") {
                return "Waiting for influencer's response to your counter offer...";
            }
            return "";
        },

        closeModal() {
            this.open = false;
            this.resetForm();
            document.body.style.overflow = "";
        },

        resetForm() {
            this.newCounterPrice = "";
            this.isSubmitting = false;
        },

        updateButtonVisibility() {
            // Force Alpine to re-evaluate computed properties
            this.$nextTick(() => {
                // This will trigger a re-render
            });
        },

        async acceptApplication() {
            await window.negotiationModalState.acceptApplication();
            this.isSubmitting = window.negotiationModalState.isSubmitting;
        },

        async counterApplication() {
            this.newCounterPrice = this.newCounterPrice || 0;
            window.negotiationModalState.newCounterPrice = this.newCounterPrice;
            await window.negotiationModalState.counterApplication();
            this.isSubmitting = window.negotiationModalState.isSubmitting;
        },

        async rejectApplication() {
            await window.negotiationModalState.rejectApplication();
            this.isSubmitting = window.negotiationModalState.isSubmitting;
        },
    };
};

// Helper function
function in_array(value, array) {
    return array.includes(value);
}
