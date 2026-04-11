<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $search     = trim((string) $request->string('q', ''));
        $visibility = (string) $request->string('visibility', 'all');
        $rating     = (string) $request->string('rating', 'all');

        $reviews = Review::query()
            ->with([
                'brand:id,brand_name',
                'influencer:id,user_id',
                'influencer.user:id,name',
                'orderItem:id,order_id,title'
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('title', 'like', '%' . $search . '%')
                        ->orWhere('comment', 'like', '%' . $search . '%')
                        ->orWhereHas('brand', function ($brandQuery) use ($search) {
                            $brandQuery->where('brand_name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('influencer.user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($visibility === 'public', fn($query) => $query->where('is_public', true))
            ->when($visibility === 'private', fn($query) => $query->where('is_public', false))
            ->when($rating !== 'all', fn($query) => $query->where('rating', (int) $rating))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'      => Review::count(),
            'public'     => Review::where('is_public', true)->count(),
            'private'    => Review::where('is_public', false)->count(),
            'avg_rating' => round((float) (Review::avg('rating') ?? 0), 1)
        ];

        return view('backend.pages.reviews.index', [
            'reviews'    => $reviews,
            'stats'      => $stats,
            'search'     => $search,
            'visibility' => $visibility,
            'rating'     => $rating
        ]);
    }

    public function show(Review $review): View
    {
        $review->load([
            'brand:id,brand_name,industry',
            'influencer:id,user_id,display_name',
            'influencer.user:id,name,email',
            'orderItem:id,order_id,title,status,quantity,unit_price,line_total',
            'orderItem.order:id,order_number,status,total_amount,currency,created_at'
        ]);

        return view('backend.pages.reviews.show', [
            'review' => $review
        ]);
    }

    public function toggleVisibility(Review $review): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Reviews are immutable. Visibility cannot be changed after submission.',
            'is_public' => true,
        ], 422);
    }
}
