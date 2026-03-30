<?php

namespace App\Services\Frontend;

use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContentLibraryService
{
    public function getListingPayload(User $user, array $filters): array
    {
        $perspective = $this->resolvePerspective($user);
        
        $query = OrderItem::query()
            ->with([
                'order:id,order_number,brand_id,campaign_id,status,currency,accepted_at',
                'order.brand:id,user_id,brand_name',
                'order.brand.user:id,profile_image_path',
                'order.campaign:id,title,campaign_type',
                'creator:id,user_id,display_name',
                'creator.user:id,name,profile_image_path'
            ])
            ->when($perspective === 'brand', fn($q) => $q->whereHas('order', fn($oq) => $oq->where('brand_id', $user->brand?->id ?? 0)))
            ->when($perspective === 'creator', fn($q) => $q->where('creator_id', $user->creator?->id ?? 0));
        
        // Apply search
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters, $perspective) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('order', fn($oq) => $oq->where('order_number', 'like', '%' . $filters['search'] . '%'))
                  ->orWhereHas('order.campaign', fn($cq) => $cq->where('title', 'like', '%' . $filters['search'] . '%'));
                
                if ($perspective === 'brand') {
                    $q->orWhereHas('creator.user', fn($uq) => $uq->where('name', 'like', '%' . $filters['search'] . '%'));
                }
                
                if ($perspective === 'creator') {
                    $q->orWhereHas('order.brand', fn($bq) => $bq->where('brand_name', 'like', '%' . $filters['search'] . '%'));
                }
            });
        }
        
        // Apply status filter
        if ($filters['status'] !== 'all') {
            $query->whereHas('order', fn($q) => $q->where('status', $filters['status']));
        }
        
        // Apply platform filter
        if ($filters['platform'] !== 'all') {
            $query->whereHas('order.campaign', fn($q) => $q->where('campaign_type', $filters['platform']));
        }
        
        // Apply date range filter
        if ($filters['date_from']) {
            $query->whereDate('order_items.accepted_at', '>=', $filters['date_from']);
        }
        
        if ($filters['date_to']) {
            $query->whereDate('order_items.accepted_at', '<=', $filters['date_to']);
        }
        
        $entries = $query->orderByDesc('order_items.accepted_at')
            ->orderByDesc('created_at')
            ->paginate($filters['per_page'])
            ->withQueryString();
        
        // Get filter counts
        $platformCounts = $this->getPlatformCounts($user, $perspective, $filters);
        $statusCounts = $this->getStatusCounts($user, $perspective, $filters);
        
        return [
            'entries' => $entries,
            'perspective' => $perspective,
            'platforms' => $platformCounts,
            'statuses' => $statusCounts,
            'filters' => $filters
        ];
    }
    
    private function getPlatformCounts(User $user, string $perspective, array $currentFilters): array
    {
        $query = OrderItem::query()
            ->when($perspective === 'brand', fn($q) => $q->whereHas('order', fn($oq) => $oq->where('brand_id', $user->brand?->id ?? 0)))
            ->when($perspective === 'creator', fn($q) => $q->where('creator_id', $user->creator?->id ?? 0))
            ->when($currentFilters['status'] !== 'all', fn($q) => $q->whereHas('order', fn($oq) => $oq->where('status', $currentFilters['status'])))
            ->when($currentFilters['date_from'], fn($q) => $q->whereDate('order_items.accepted_at', '>=', $currentFilters['date_from']))
            ->when($currentFilters['date_to'], fn($q) => $q->whereDate('order_items.accepted_at', '<=', $currentFilters['date_to']));
        
        $counts = $query->selectRaw('campaigns.campaign_type, count(*) as total')
            ->join('campaigns', 'order_items.campaign_id', '=', 'campaigns.id')
            ->groupBy('campaigns.campaign_type')
            ->pluck('total', 'campaign_type')
            ->toArray();
        
        $allPlatforms = ['facebook', 'instagram', 'tiktok', 'linkedin', 'x', 'youtube', 'ugc', 'other'];
        $result = [];
        
        foreach ($allPlatforms as $platform) {
            $result[$platform] = $counts[$platform] ?? 0;
        }
        
        return $result;
    }
    
    private function getStatusCounts(User $user, string $perspective, array $currentFilters): array
    {
        $query = OrderItem::query()
            ->when($perspective === 'brand', fn($q) => $q->whereHas('order', fn($oq) => $oq->where('brand_id', $user->brand?->id ?? 0)))
            ->when($perspective === 'creator', fn($q) => $q->where('creator_id', $user->creator?->id ?? 0))
            ->when($currentFilters['platform'] !== 'all', fn($q) => $q->whereHas('order.campaign', fn($cq) => $cq->where('campaign_type', $currentFilters['platform'])))
            ->when($currentFilters['date_from'], fn($q) => $q->whereDate('order_items.accepted_at', '>=', $currentFilters['date_from']))
            ->when($currentFilters['date_to'], fn($q) => $q->whereDate('order_items.accepted_at', '<=', $currentFilters['date_to']));
        
        $counts = $query->selectRaw('orders.status, count(*) as total')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->groupBy('orders.status')
            ->pluck('total', 'status')
            ->toArray();
        
        $allStatuses = ['pending', 'accepted', 'in_progress', 'delivered', 'completed', 'cancelled', 'refunded'];
        $result = [];
        
        foreach ($allStatuses as $status) {
            $result[$status] = $counts[$status] ?? 0;
        }
        
        return $result;
    }
    
    private function resolvePerspective(User $user): string
    {
        if ($user->user_type === 'brand') {
            return 'brand';
        }
        
        if ($user->user_type === 'creator') {
            return 'creator';
        }
        
        return 'unknown';
    }
}