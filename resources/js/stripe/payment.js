import { loadStripe } from '@stripe/stripe-js';

function resolveInitialTab(initialTab = 'details', availableTabs = ['details']) {
    const tabFromUrl = new URLSearchParams(window.location.search).get('tab');
    const desired = tabFromUrl || initialTab || 'details';

    if (Array.isArray(availableTabs) && availableTabs.includes(desired)) {
        return desired;
    }

    return Array.isArray(availableTabs) && availableTabs.length > 0
        ? availableTabs[0]
        : 'details';
}

function buildPaymentHandler({ stripePublicKey, paymentStoreUrl, csrfToken }) {
    return {
        stripe: null,
        elements: null,
        cardElement: null,
        isProcessing: false,
        isDefault: false,
        error: '',

    async init() {
            if (!stripePublicKey) {
                this.error = 'Payment processing is currently unavailable.';
                return;
            }

            this.stripe = await loadStripe(stripePublicKey);

            if (!this.stripe) {
                this.error = 'Payment processing is currently unavailable.';
                return;
            }

            this.elements = this.stripe.elements();

            this.cardElement = this.elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#222',
                        fontFamily: 'system-ui, -apple-system, sans-serif',
                        '::placeholder': {
                            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#d1d5db',
                        },
                    },
                    invalid: {
                        color: '#ef4444',
                    },
                },
            });

            this.cardElement.mount('#card-element');

            this.cardElement.addEventListener('change', (event) => {
                const errorEl = document.getElementById('card-errors');
                if (!errorEl) {
                    return;
                }

                if (event.error) {
                    this.error = event.error.message;
                    errorEl.textContent = event.error.message;
                } else {
                    this.error = '';
                    errorEl.textContent = '';
                }
            });
        },

        async submit(tab) {
            if (this.isProcessing || !this.stripe || !this.cardElement) {
                return;
            }

            this.error = '';
            this.isProcessing = true;

            try {
                const { paymentMethod, error } = await this.stripe.createPaymentMethod({
                    type: 'card',
                    card: this.cardElement,
                });

                if (error || !paymentMethod?.id) {
                    this.error = error?.message || 'Unable to validate card details.';
                    this.isProcessing = false;
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = paymentStoreUrl;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="stripe_payment_method_id" value="${paymentMethod.id}">
                    <input type="hidden" name="is_default" value="${this.isDefault ? 1 : 0}">
                    <input type="hidden" name="tab" value="${tab || 'payment'}">
                `;

                document.body.appendChild(form);
                form.submit();
            } catch (submitError) {
                this.error = 'An unexpected error occurred. Please try again.';
                this.isProcessing = false;
                console.error('Stripe submission error:', submitError);
            }
        },

        reset() {
            this.isProcessing = false;
            this.isDefault = false;
            this.error = '';
            if (this.cardElement) {
                this.cardElement.clear();
            }
            const errorEl = document.getElementById('card-errors');
            if (errorEl) {
                errorEl.textContent = '';
            }
        },
    };
}

window.accountPageData = function accountPageData(config = {}) {
    return {
        tab: resolveInitialTab(config.initialTab, config.availableTabs),
        availableTabs: Array.isArray(config.availableTabs) ? config.availableTabs : ['details'],
        showCardModal: false,
        payment: null,

        init() {
            if (!this.availableTabs.includes(this.tab)) {
                this.tab = this.availableTabs[0] || 'details';
            }

            this.$watch('tab', (value) => {
                if (!this.availableTabs.includes(value)) {
                    this.tab = this.availableTabs[0] || 'details';
                    return;
                }

                const url = new URL(window.location.href);
                url.searchParams.set('tab', value);
                window.history.replaceState({}, '', url);
            });

            this.payment = buildPaymentHandler({
                stripePublicKey: config.stripePublicKey,
                paymentStoreUrl: config.paymentStoreUrl,
                csrfToken: config.csrfToken,
            });
        },

        openPaymentModal() {
            this.showCardModal = true;
            this.$nextTick(() => {
                if (this.payment && !this.payment.cardElement) {
                    this.payment.init();
                }
            });
        },

        closePaymentModal() {
            this.showCardModal = false;
            if (this.payment) {
                this.payment.reset();
            }
        },

        submitPaymentMethod() {
            if (!this.payment) {
                return;
            }
            this.payment.submit(this.tab || 'payment');
        },
    };
};
