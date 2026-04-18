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
        $packageItemsBaseQuery = OrderItem::query()
            ->unpaidForDashboard()
            ->whereHas('order', fn ($query) => $query->whereNull('campaign_id'));

        $campaignItemsBaseQuery = OrderItem::query()
            ->unpaidForDashboard()
            ->whereHas('order', fn ($query) => $query->whereNotNull('campaign_id'));

        $campaignSubOrdersBaseQuery = SubOrder::query()->unpaidForDashboard();

        // Get unpaid OrderItems (Package flow)
        $unPaidItems = (clone $packageItemsBaseQuery)
            ->forPaymentDashboard()
            ->with([
                'order:id,order_number,brand_id,campaign_id',
                'order.brand:id,brand_name',
                'order.campaign:id,title',
                'influencer:id,user_id,display_name',
                'influencer.user:id,name,email',
                'package:id,name',
            ])
            ->orderBy('due_date', 'asc')
            ->get();

        // Get unpaid campaign-linked order items (legacy/flat campaign flow)
        $unPaidCampaignItems = (clone $campaignItemsBaseQuery)
            ->forPaymentDashboard()
            ->with([
                'order:id,order_number,brand_id,campaign_id',
                'order.brand:id,brand_name',
                'order.campaign:id,title',
                'influencer:id,user_id,display_name',
                'influencer.user:id,name,email',
                'package:id,name',
            ])
            ->orderBy('due_date', 'asc')
            ->get();

        // Get unpaid SubOrders (Campaign flow)
        $unPaidSubOrders = (clone $campaignSubOrdersBaseQuery)
            ->forPaymentDashboard()
            ->with([
                'order:id,order_number,campaign_id,brand_id',
                'order.brand:id,brand_name',
                'order.campaign:id,title',
                'influencer:id,user_id,display_name',
                'influencer.user:id,name,email',
            ])
            ->orderBy('completed_at', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Combined statistics
        $totalUnpaidItems = (clone $packageItemsBaseQuery)->count();
        $totalUnpaidSubOrders = (clone $campaignItemsBaseQuery)->count() + (clone $campaignSubOrdersBaseQuery)->count();
        $totalUnpaidAmount = (float) (clone $packageItemsBaseQuery)->sum('line_total')
            + (float) (clone $campaignItemsBaseQuery)->sum('line_total')
            + (float) (clone $campaignSubOrdersBaseQuery)->sum('amount');

        // Top unpaid influencers
        $packageItemInfluencers = (clone $packageItemsBaseQuery)
            ->selectRaw('influencer_id, COUNT(*) as item_count, SUM(line_total) as amount')
            ->groupBy('influencer_id')
            ->with('influencer:id,user_id,display_name')
            ->get()
            ->map(fn ($row) => [
                'influencer' => $row->influencer,
                'item_count' => (int) $row->item_count,
                'amount' => (float) $row->amount,
            ]);

        $campaignItemInfluencers = (clone $campaignItemsBaseQuery)
            ->selectRaw('influencer_id, COUNT(*) as item_count, SUM(line_total) as amount')
            ->groupBy('influencer_id')
            ->with('influencer:id,user_id,display_name')
            ->get()
            ->map(fn ($row) => [
                'influencer' => $row->influencer,
                'item_count' => (int) $row->item_count,
                'amount' => (float) $row->amount,
            ]);

        $subOrderInfluencers = (clone $campaignSubOrdersBaseQuery)
            ->selectRaw('influencer_id, COUNT(*) as item_count, SUM(amount) as amount')
            ->groupBy('influencer_id')
            ->with('influencer:id,user_id,display_name')
            ->get()
            ->map(fn ($row) => [
                'influencer' => $row->influencer,
                'item_count' => (int) $row->item_count,
                'amount' => (float) $row->amount,
            ]);

        $topUnpaidInfluencers = $packageItemInfluencers
            ->concat($campaignItemInfluencers)
            ->concat($subOrderInfluencers)
            ->groupBy(fn ($entry) => $entry['influencer']?->id)
            ->map(function ($group) {
                $first = $group->first();

                return [
                    'influencer' => $first['influencer'],
                    'item_count' => (int) $group->sum('item_count'),
                    'amount' => (float) $group->sum('amount'),
                ];
            })
            ->sortByDesc('amount')
            ->values();

        return view('backend.pages.payment-queue.index', compact(
            'unPaidItems',
            'unPaidCampaignItems',
            'unPaidSubOrders',
            'totalUnpaidItems',
            'totalUnpaidSubOrders',
            'totalUnpaidAmount',
            'topUnpaidInfluencers',
        ));
    }

    public function markPaid(Request $request)
    {
        $validated = $request->validate([
            'item_id'   => 'required|integer',
            'item_type' => 'required|in:item,sub-order',
            'amount'    => 'required|numeric|min:0',
            'reference' => 'nullable|string|max:120'
        ]);

        $userId = $request->user()->id;

        try {
            if ($validated['item_type'] === 'item') {
                $orderItem = OrderItem::find($validated['item_id']);
                if (!$orderItem) {
                    return response()->json(['success' => false, 'message' => 'Item not found'], 404);
                }

                $orderItem->update([
                    'paid_at'                  => now(),
                    'payout_amount'            => $validated['amount'],
                    'payout_reference'         => $validated['reference'] ?? null,
                    'payout_marked_by_user_id' => $userId,
                    'payout_marked_at'         => now()
                ]);
            } elseif ($validated['item_type'] === 'sub-order') {
                $subOrder = SubOrder::find($validated['item_id']);
                if (!$subOrder) {
                    return response()->json(['success' => false, 'message' => 'Sub-order not found'], 404);
                }

                $subOrder->update([
                    'paid_at'                  => now(),
                    'payout_amount'            => $validated['amount'],
                    'payout_reference'         => $validated['reference'] ?? null,
                    'payout_marked_by_user_id' => $userId,
                    'payout_marked_at'         => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item marked as paid successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking item as paid', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error marking item as paid'
            ], 500);
        }
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
                /** @var OrderItem|null $orderItem */
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
                /** @var SubOrder|null $subOrder */
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
