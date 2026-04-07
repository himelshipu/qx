<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Influencer;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // User stats
        $totalUsers            = User::count();
        $activeUsers           = User::where('is_active', true)->count();
        $emailUnverifiedUsers  = User::whereNull('email_verified_at')->count();
        $mobileUnverifiedUsers = User::whereNull('phone')->count();

        // Influencer stats
        $totalInfluencers            = Influencer::count();
        $activeInfluencers           = Influencer::where('is_active', true)->count();
        $emailUnverifiedInfluencers  = Influencer::whereHas('user', fn($q) => $q->whereNull('email_verified_at'))->count();
        $mobileUnverifiedInfluencers = Influencer::whereHas('user', fn($q) => $q->whereNull('phone'))->count();

        // Campaign stats
        $totalCampaigns    = Campaign::count();
        $pendingCampaigns  = Campaign::where('status', 'draft')->count();
        $approvedCampaigns = Campaign::where('status', 'published')->count();
        $rejectedCampaigns = Campaign::whereIn('status', ['archived', 'closed'])->count();

        // Financial stats (based on orders)
        $totalDeposited   = Order::where('status', 'completed')->sum('total_amount');
        $pendingDeposits  = Order::where('status', 'pending')->count();
        $rejectedDeposits = Order::whereIn('status', ['cancelled', 'refunded'])->count();
        $depositedCharge  = Order::where('status', 'completed')->sum('service_fee') + Order::where('status', 'completed')->sum('tax_amount');

        // For withdrawals, since no model, use placeholder or calculate from orders if applicable
        // Assuming withdrawals are for creators, perhaps sum of completed orders minus fees or something
        $totalWithdrawn      = 0; // Placeholder
        $pendingWithdrawals  = 0; // Placeholder
        $rejectedWithdrawals = 0; // Placeholder
        $withdrawalCharge    = 0; // Placeholder

        // Chart data: Monthly user registrations for the last 12 months
        $monthlyUsers = User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => \Carbon\Carbon::create($item->year, $item->month)->format('M Y'),
                    'count' => $item->count
                ];
            });

        // Monthly campaigns
        $monthlyCampaigns = Campaign::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => \Carbon\Carbon::create($item->year, $item->month)->format('M Y'),
                    'count' => $item->count
                ];
            });

        // Monthly orders total
        $monthlyOrders = Order::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as total')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => \Carbon\Carbon::create($item->year, $item->month)->format('M Y'),
                    'total' => $item->total
                ];
            });

        return view('backend.pages.index', compact(
            'totalUsers', 'activeUsers', 'emailUnverifiedUsers', 'mobileUnverifiedUsers',
            'totalInfluencers', 'activeInfluencers', 'emailUnverifiedInfluencers', 'mobileUnverifiedInfluencers',
            'totalCampaigns', 'pendingCampaigns', 'approvedCampaigns', 'rejectedCampaigns',
            'totalDeposited', 'pendingDeposits', 'rejectedDeposits', 'depositedCharge',
            'totalWithdrawn', 'pendingWithdrawals', 'rejectedWithdrawals', 'withdrawalCharge',
            'monthlyUsers', 'monthlyCampaigns', 'monthlyOrders'
        ));
    }
}
