<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Models\OrderItem;
use App\Models\Payout;
use App\Models\PayoutItem;
use Illuminate\Http\Request;

class PayoutsController extends Controller
{
    public function index()
    {
        // Get all payouts with relationships
        $payouts = Payout::with(['influencer.user', 'payoutAccount', 'items.orderItem'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Payout statistics
        $totalPayouts      = Payout::count();
        $paidPayouts       = Payout::where('status', 'paid')->count();
        $processingPayouts = Payout::where('status', 'processing')->count();
        $pendingPayouts    = Payout::where('status', 'pending')->count();
        $failedPayouts     = Payout::where('status', 'failed')->count();

        // Payout amounts
        $totalPayoutAmount = Payout::sum('amount') ?? 0;
        $paidAmount        = Payout::where('status', 'paid')->sum('amount') ?? 0;
        $processingAmount  = Payout::where('status', 'processing')->sum('amount') ?? 0;
        $pendingAmount     = Payout::where('status', 'pending')->sum('amount') ?? 0;
        $failedAmount      = Payout::where('status', 'failed')->sum('amount') ?? 0;

        // Payout status distribution
        $payoutsByStatus = Payout::selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // Payouts this month
        $payoutsThisMonth = Payout::where('created_at', '>=', now()->startOfMonth())
            ->sum('amount') ?? 0;

        // Payouts this week
        $payoutsThisWeek = Payout::where('created_at', '>=', now()->startOfWeek())
            ->sum('amount') ?? 0;

        // Top influencers by payout amount
        $topInfluencersByPayout = Influencer::join('payouts', 'influencers.id', '=', 'payouts.influencer_id')
            ->join('users', 'influencers.user_id', '=', 'users.id')
            ->selectRaw('influencers.id, influencers.display_name, COUNT(payouts.id) as payout_count, SUM(payouts.amount) as total_amount')
            ->where('payouts.deleted_at', null)
            ->groupBy('influencers.id', 'influencers.display_name')
            ->orderBy('total_amount', 'desc')
            ->limit(5)
            ->get();

        // Pending payouts for quick action
        $pendingPayoutsList = Payout::where('status', 'pending')
            ->with(['influencer.user', 'payoutAccount', 'items.orderItem.order'])
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Recent paid payouts
        $recentPaidPayouts = Payout::where('status', 'paid')
            ->with(['influencer.user', 'payoutAccount'])
            ->orderBy('paid_at', 'desc')
            ->limit(5)
            ->get();

        // Get unassigned order items for payout creation
        $unassignedItems = OrderItem::with(['order.campaign', 'order.brand'])
            ->whereNotExists(function ($query) {
                $query->select('payout_items.id')
                    ->from('payout_items')
                    ->whereColumn('payout_items.order_item_id', 'order_items.id');
            })
            ->where('status', 'completed')
            ->get();

        // Get all influencers for payout creation
        $influencers = Influencer::with('user', 'payoutAccounts')->get();

        return view('backend.pages.payouts.index', compact(
            'payouts',
            'totalPayouts',
            'paidPayouts',
            'processingPayouts',
            'pendingPayouts',
            'failedPayouts',
            'totalPayoutAmount',
            'paidAmount',
            'processingAmount',
            'pendingAmount',
            'failedAmount',
            'payoutsByStatus',
            'payoutsThisMonth',
            'payoutsThisWeek',
            'topInfluencersByPayout',
            'pendingPayoutsList',
            'recentPaidPayouts',
            'unassignedItems',
            'influencers'
        ));
    }

    public function show(Payout $payout)
    {
        $payout->load(['influencer.user', 'payoutAccount', 'items.orderItem.order']);

        return response()->json([
            'payout' => $payout,
            'items'  => $payout->items->map(function ($item) {
                return [
                    'id'         => $item->id,
                    'order_id'   => $item->orderItem->order->id,
                    'order_item' => $item->orderItem->description ?? 'Task',
                    'campaign'   => $item->orderItem->order->campaign?->name ?? 'N/A',
                    'amount'     => $item->amount,
                    'currency'   => $item->currency
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'influencer_id'     => 'required|exists:influencers,id',
            'payout_account_id' => 'required|exists:payout_accounts,id',
            'amount'            => 'required|numeric|min:0.01',
            'currency'          => 'nullable|string|default:USD',
            'order_item_ids'    => 'sometimes|array',
            'order_item_ids.*'  => 'exists:order_items,id'
        ]);

        // Create payout
        $payout = Payout::create([
            'influencer_id'     => $validated['influencer_id'],
            'payout_account_id' => $validated['payout_account_id'],
            'amount'            => $validated['amount'],
            'currency'          => $validated['currency'] ?? 'USD',
            'status'            => 'pending'
        ]);

        // Link order items if provided
        if (!empty($validated['order_item_ids'])) {
            $orderItems = OrderItem::whereIn('id', $validated['order_item_ids'])->get();
            foreach ($orderItems as $item) {
                PayoutItem::create([
                    'payout_id'     => $payout->id,
                    'order_item_id' => $item->id,
                    'amount'        => $item->amount,
                    'currency'      => 'USD'
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Payout created successfully',
            'payout'  => $payout
        ], 201);
    }

    public function update(Request $request, Payout $payout)
    {
        $validated = $request->validate([
            'status'             => 'required|in:pending,processing,paid,failed,cancelled',
            'external_payout_id' => 'nullable|string'
        ]);

        $payout->update([
            'status'             => $validated['status'],
            'external_payout_id' => $validated['external_payout_id'] ?? $payout->external_payout_id,
            'paid_at'            => $validated['status'] === 'paid' ? now() : $payout->paid_at
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payout updated successfully',
            'payout'  => $payout
        ]);
    }

    public function markAsPaid(Request $request, Payout $payout)
    {
        $validated = $request->validate([
            'external_payout_id' => 'nullable|string',
            'reference'          => 'nullable|string'
        ]);

        $payout->update([
            'status'             => 'paid',
            'external_payout_id' => $validated['external_payout_id'] ?? null,
            'paid_at'            => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payout marked as paid successfully',
            'payout'  => $payout
        ]);
    }

    public function getInfluencerAccounts(Influencer $influencer)
    {
        $accounts = $influencer->payoutAccounts()->where('is_active', true)->get();

        return response()->json([
            'accounts' => $accounts
        ]);
    }
}
