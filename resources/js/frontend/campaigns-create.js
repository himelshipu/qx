window.campaignDesignedWizard = function campaignDesignedWizard(config) {
    return {
        step: Number(config.step || 1),
        campaignType: config.campaignType || 'instagram',
        campaignTypeOptions: Array.isArray(config.campaignTypeOptions) ? config.campaignTypeOptions : [],
        statusOptions: Array.isArray(config.statusOptions) ? config.statusOptions : [],
        genderOptions: Array.isArray(config.genderOptions) ? config.genderOptions : [],
        categoryOptions: Array.isArray(config.categoryOptions) ? config.categoryOptions : [],
        followerRangeOptions: Array.isArray(config.followerRangeOptions) ? config.followerRangeOptions : [],
        countryOptions: Array.isArray(config.countryOptions) ? config.countryOptions : [],
        selectedCategoryIds: Array.isArray(config.selectedCategoryIds)
            ? config.selectedCategoryIds.map((id) => Number(id)).filter((id) => Number.isInteger(id) && id > 0)
            : [],
        selectedFollowerRangeIds: Array.isArray(config.selectedFollowerRangeIds)
            ? config.selectedFollowerRangeIds.map((id) => Number(id)).filter((id) => Number.isInteger(id) && id > 0)
            : [],
        selectedCountryCodes: Array.isArray(config.selectedCountryCodes)
            ? config.selectedCountryCodes.map((code) => String(code).toUpperCase())
            : [],
        influencerCount: config.influencerCount || '',
        isAdvancedOpen: Boolean(config.isAdvancedOpen),
        showCategoryDropdown: false,
        showFollowerDropdown: false,
        showCountryDropdown: false,

        toggleSelection(arrayName, value) {
            if (! Array.isArray(this[arrayName])) {
                return;
            }

            const normalizedValue = arrayName === 'selectedCountryCodes'
                ? String(value).toUpperCase()
                : Number(value);

            if (this[arrayName].includes(normalizedValue)) {
                this[arrayName] = this[arrayName].filter((item) => item !== normalizedValue);
                return;
            }

            this[arrayName] = [...this[arrayName], normalizedValue];
        },

        getCategoryName(categoryId) {
            const option = this.categoryOptions.find((item) => Number(item.id) === Number(categoryId));
            return option ? option.name : `Category ${categoryId}`;
        },

        getFollowerRangeLabel(rangeId) {
            const option = this.followerRangeOptions.find((item) => Number(item.id) === Number(rangeId));
            return option ? option.label : `Range ${rangeId}`;
        },

        getCountryName(countryCode) {
            const option = this.countryOptions.find((item) => String(item.code).toUpperCase() === String(countryCode).toUpperCase());
            return option ? `${option.name} (${option.code})` : countryCode;
        },

        getCampaignTypeLabel() {
            const option = this.campaignTypeOptions.find((item) => item.value === this.campaignType);
            return option ? option.label : this.campaignType;
        },

        getEstimate() {
            const count = Math.max(parseInt(this.influencerCount || '1', 10) || 1, 1);
            const nicheFactor = Math.max(this.selectedCategoryIds.length, 1);
            const rangeFactor = Math.max(this.selectedFollowerRangeIds.length, 1);
            const countryFactor = Math.max(this.selectedCountryCodes.length, 1);

            const baseInfluencersPerNiche = 25;
            const nicheMultiplier = Math.min(nicheFactor, 3);
            const rangeMultiplier = Math.min(rangeFactor, 2);
            const countryMultiplier = Math.min(countryFactor, 5);

            const minInfluencers = Math.round(count * baseInfluencersPerNiche * nicheMultiplier * 0.4);
            const maxInfluencers = Math.round(count * baseInfluencersPerNiche * nicheMultiplier * rangeMultiplier * countryMultiplier * 0.9);

            const avgFollowersMin = 35000;
            const avgFollowersMax = 280000;
            const reachMin = Math.round((minInfluencers * avgFollowersMin) / 1000000 * 10) / 10;
            const reachMax = Math.round((maxInfluencers * avgFollowersMax) / 1000000 * 10) / 10;

            return {
                influencers: minInfluencers === maxInfluencers ? `${minInfluencers}` : `${minInfluencers}-${maxInfluencers}`,
                reach: reachMin === reachMax ? `${reachMin}M` : `${reachMin}-${reachMax}M`,
            };
        },
    };
};
