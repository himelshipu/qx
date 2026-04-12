<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\SubOrder;
use Illuminate\Http\Request;

class PaymentQueueController extends Controller
{
    public function index(Request $request)
    {
        // Get unpaid OrderItems (Package flow)
        $unPaidItems = OrderItem::with([
            'order.brand',
            'order.campaign',
            'influencer.user',
            'package'
        ])
            ->whereNull('paid_at')
            ->where('status', 'approved')
            ->orderBy('due_date', 'asc')
            ->get();

        // Get unpaid SubOrders (Campaign flow)
        $unPaidSubOrders = SubOrder::with([
            'order.campaign',
            'influencer.user'
        ])
            ->whereNull('paid_at')
            ->where('status', 'completed')
            ->orderBy('created_at', 'asc')
            ->get();

        // Combined statistics
        $totalUnpaidItems     = $unPaidItems->count();
        $totalUnpaidSubOrders = $unPaidSubOrders->count();
        $totalUnpaidAmount    = $unPaidItems->sum('line_total') + $unPaidSubOrders->sum('amount');

        // Group by influencer
        $itemsByInfluencer     = $unPaidItems->groupBy('influencer.display_name');
        $subOrdersByInfluencer = $unPaidSubOrders->groupBy('influencer.display_name');

        // Top unpaid influencers
        $topUnpaidInfluencers = collect();
        foreach ($unPaidItems->groupBy('influencer_id') as $influencerId => $items) {
            $amount     = $items->sum('line_total');
            $influencer = $items->first()->influencer;
            $topUnpaidInfluencers->push([
                'influencer' => $influencer,
                'item_count' => $items->count(),
                'amount'     => $amount
            ]);
        }
        foreach ($unPaidSubOrders->groupBy('influencer_id') as $influencerId => $subOrders) {
            $amount     = $subOrders->sum('amount');
            $influencer = $subOrders->first()->influencer;
            $existing   = $topUnpaidInfluencers->firstWhere('influencer.id', $influencer->id);
            if ($existing) {
                $existing['amount'] += $amount;
                $existing['item_count'] += $subOrders->count();
            } else {
                $topUnpaidInfluencers->push([
                    'influencer' => $influencer,
                    'item_count' => $subOrders->count(),
                    'amount'     => $amount
                ]);
            }
        }
        $topUnpaidInfluencers = $topUnpaidInfluencers->sortByDesc('amount')->values();

        return view('backend.pages.payment-queue.index', compact(
            'unPaidItems',
            'unPaidSubOrders',
            'totalUnpaidItems',
            'totalUnpaidSubOrders',
            'totalUnpaidAmount',
            'topUnpaidInfluencers',
        ));
    }

    public function bulkMark(Request $request)
    {
        $validated = $request->validate([
            'items'             => 'required|array',
            'items.*.id'        => 'required|integer',
            'items.*.type'      => 'required|in:item,sub-order',
            'items.*.amount'    => 'required|numeric|min:0',
            'items.*.reference' => 'nullable|string|max:120'
        ]);

        $userId      = $request->user()->id;
        $markedCount = 0;

        foreach ($validated['items'] as $itemData) {
            if ($itemData['type'] === 'item') {
                $orderItem = OrderItem::find($itemData['id']);
                if ($orderItem) {
                    $orderItem->update([
                        'paid_at'                  => now(),
                        'payout_amount'            => $itemData['amount'],
                        'payout_reference'         => $itemData['reference'] ?? null,
                        'payout_marked_by_user_id' => $userId,
                        'payout_marked_at'         => now()
                    ]);
                    $markedCount++;
                }
            } elseif ($itemData['type'] === 'sub-order') {
                $subOrder = SubOrder::find($itemData['id']);
                if ($subOrder) {
                    $subOrder->update([
                        'paid_at'                  => now(),
                        'payout_amount'            => $itemData['amount'],
                        'payout_reference'         => $itemData['reference'] ?? null,
                        'payout_marked_by_user_id' => $userId,
                        'payout_marked_at'         => now()
                    ]);
                    $markedCount++;
                }
            }
        }

        return redirect()
            ->back()
            ->with('success', "Marked {$markedCount} items as paid");
    }
}
