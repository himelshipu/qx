window.campaignFilter = function campaignFilter(config = {}) {
    return {
        search: config.search || '',
        status: config.status || 'all',
        type: config.type || 'all',
        campaigns: Array.isArray(config.campaigns) ? config.campaigns : [],
        filteredCampaigns: [],

        init() {
            this.filterCampaigns();
        },

        filterCampaigns() {
            const search = (this.search || '').toLowerCase();
            const status = this.status;
            const type = this.type;

            this.filteredCampaigns = (this.campaigns || []).filter((campaign) => {
                const matchesSearch = !search ||
                    String(campaign.title || '').toLowerCase().includes(search) ||
                    String(campaign.campaign_type || '').toLowerCase().includes(search);

                const matchesStatus = status === 'all' || campaign.status === status;
                const matchesType = type === 'all' || campaign.campaign_type === type;

                return matchesSearch && matchesStatus && matchesType;
            });
        },

        resetFilters() {
            this.search = '';
            this.status = 'all';
            this.type = 'all';
            this.filterCampaigns();
        },

        getStatusBadgeClass(status) {
            const classes = {
                published: 'bg-emerald-500 text-white',
                paused: 'bg-amber-500 text-white',
                closed: 'bg-red-500 text-white',
                archived: 'bg-gray-500 text-white',
                draft: 'bg-pink-500 text-white',
            };

            return classes[status] || 'bg-pink-500 text-white';
        },

        capitalizeStatus(text) {
            if (! text) return '';

            return String(text)
                .replace(/([A-Z])/g, ' $1')
                .replace(/^./, (str) => str.toUpperCase())
                .trim();
        },

        deleteCampaign(e) {
            const button = e.target.closest('button');
            if (button && button.classList.contains('js-confirmable')) {
                button.click();
            }
        },
    };
};
