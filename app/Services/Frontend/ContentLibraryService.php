<?php

namespace App\Services\Frontend;

use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContentLibraryService
{
    /**
     * @return array{entries:LengthAwarePaginator,search:string,status:string,perspective:string}
     */
    public function getListingPayload(User $user, string $search, string $status, int $perPage): array
    {
        $perspective = $this->resolvePerspective($user);

        $entries = OrderItem::query()
            ->with([
                'order:id,order_number,brand_id,campaign_id,status,currency,created_at',
                'order.brand:id,user_id,brand_name',
                'order.brand.user:id,profile_image_path',
                'order.campaign:id,title',
                'creator:id,user_id,display_name',
                'creator.user:id,name,profile_image_path'
            ])
            ->when($perspective === 'brand', fn($query) => $query->whereHas('order', fn($orderQuery) => $orderQuery->where('brand_id', $user->brand?->id ?? 0)))
            ->when($perspective === 'creator', fn($query) => $query->where('creator_id', $user->creator?->id ?? 0))
            ->when($search !== '', function ($query) use ($search, $perspective) {
                $query->where(function ($subQuery) use ($search, $perspective) {
                    $subQuery
                        ->where('title', 'like', '%' . $search . '%')
                        ->orWhereHas('order', fn($orderQuery) => $orderQuery->where('order_number', 'like', '%' . $search . '%'))
                        ->orWhereHas('order.campaign', fn($campaignQuery) => $campaignQuery->where('title', 'like', '%' . $search . '%'));

                    if ($perspective === 'brand') {
                        $subQuery->orWhereHas('creator.user', fn($userQuery) => $userQuery->where('name', 'like', '%' . $search . '%'));
                    }

                    if ($perspective === 'creator') {
                        $subQuery->orWhereHas('order.brand', fn($brandQuery) => $brandQuery->where('brand_name', 'like', '%' . $search . '%'));
                    }
                });
            })
            ->when($status !== 'all', fn($query) => $query->whereHas('order', fn($orderQuery) => $orderQuery->where('status', $status)))
            ->orderByDesc('accepted_at')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return [
            'entries' => $entries,
            'search' => $search,
            'status' => $status,
            'perspective' => $perspective,
        ];
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

