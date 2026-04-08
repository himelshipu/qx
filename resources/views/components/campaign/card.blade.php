@props(['campaign', 'userType'])

<div class="group relative aspect-[4/3] overflow-hidden rounded-[1.8rem] border border-gray-100 bg-gray-100 shadow-sm transition-all duration-500 hover:shadow-2xl dark:border-gray-800 dark:bg-gray-900">
    <a :href="`/campaigns/${campaign.id}`" class="absolute inset-0 z-10"
        :aria-label="`View ${campaign.title}`"></a>

    <img :src="campaign.image" alt="Campaign Preview"
        class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
        onerror="this.onerror=null;this.src='{{ asset('images/campaignApply.png') }}';">

    <div class="absolute inset-0 z-10 bg-gradient-to-t from-black/95 via-black/30 to-transparent opacity-90"></div>

    <!-- Status and Inactive Badge -->
    <div class="absolute left-5 top-5 z-20 flex items-center gap-2">
        <span :class="getStatusBadgeClass(campaign.status)"
            class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-[10px] font-medium uppercase">
            <span x-text="capitalizeStatus(campaign.status)"></span>
        </span>
        <template x-if="!campaign.is_active">
            <span class="rounded-full bg-black/50 px-2.5 py-1 text-[10px] font-medium uppercase text-white backdrop-blur">
                Inactive
            </span>
        </template>
    </div>

    <!-- Action Buttons -->
    <div class="absolute right-5 top-5 z-20 flex items-center gap-2">
        <a :href="`/campaigns/${campaign.id}`"
            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-white/25"
            title="View campaign">
            <x-icons.eye class="h-4 w-4" />
        </a>
        <template x-if="campaign.canEdit">
            <a :href="`/campaigns/${campaign.id}/edit`"
                class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-white/25"
                title="Edit campaign">
                <x-icons.edit class="h-4 w-4" />
            </a>
            <form :action="`/campaigns/${campaign.id}`" method="POST" class="relative z-20"
                @submit.prevent="deleteCampaign($event, campaign.id)">
                @csrf
                @method('DELETE')
                <button type="submit" data-confirm-title="Delete Campaign"
                    data-confirm-message="Delete this campaign? This action cannot be undone." data-confirm-button="Delete"
                    data-confirm-variant="danger" title="Delete campaign"
                    class="js-confirmable inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-red-500/80">
                    <x-icons.trash class="h-4 w-4" />
                </button>
            </form>
        </template>
    </div>

    <!-- Bottom Info -->
    <div class="absolute bottom-0 left-0 right-0 z-20 p-8">
        <a :href="`/campaigns/${campaign.id}`">
            <h3 class="line-clamp-2 text-base font-bold leading-tight text-white md:text-lg group-hover:underline"
                x-text="campaign.title">
            </h3>
        </a>
        <p class="mt-1.5 text-[12px] font-semibold uppercase tracking-wider text-gray-300"
            x-text="capitalizeStatus(campaign.campaign_type)">
        </p>
        <div class="mt-3 flex flex-wrap items-center gap-2 text-[10px] uppercase tracking-[0.22em] text-white/70">
            <span x-text="`${campaign.applications_count} Applications`"></span>
            <span x-text="`${campaign.categories_count} Niches`"></span>
            <template x-if="campaign.targeting?.influencer_count">
                <span x-text="`${campaign.targeting.influencer_count} Influencers Target`"></span>
            </template>
        </div>
    </div>
</div>
