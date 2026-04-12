<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Influencer;
use App\Models\Order;
use App\Models\Package;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ============= KEY PERFORMANCE INDICATORS =============
        // Orders KPIs
        $totalOrders      = Order::count();
        $completedOrders  = Order::where('status', 'completed')->count();
        $pendingOrders    = Order::where('status', 'pending')->count();
        $inProgressOrders = Order::where('status', 'in-progress')->count();

        // Revenue KPIs
        $totalOrderValue     = Order::sum('total_amount') ?? 0;
        $completedOrderValue = Order::where('status', 'completed')->sum('total_amount') ?? 0;
        $pendingOrderValue   = Order::where('status', 'pending')->sum('total_amount') ?? 0;
        $totalServiceFees    = Order::sum('service_fee') ?? 0;
        $totalTaxes          = Order::sum('tax_amount') ?? 0;
        $conversionRate      = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0;

        // Platform KPIs
        $totalBrands        = Brand::count();
        $activeBrands       = Brand::where('is_verified', true)->count();
        $totalInfluencers   = Influencer::count();
        $activeInfluencers  = Influencer::where('is_active', true)->count();
        $totalPackages      = Package::count();
        $totalCampaigns     = Campaign::count();
        $publishedCampaigns = Campaign::where('status', 'published')->count();
        $totalUsers         = User::count();
        $totalReviews       = Review::count();

        // ============= USER & INFLUENCER STATS =============
        $activeUsers                 = User::where('is_active', true)->count();
        $emailUnverifiedUsers        = User::whereNull('email_verified_at')->count();
        $mobileUnverifiedUsers       = User::whereNull('phone')->count();
        $emailUnverifiedInfluencers  = Influencer::whereHas('user', fn($q) => $q->whereNull('email_verified_at'))->count();
        $mobileUnverifiedInfluencers = Influencer::whereHas('user', fn($q) => $q->whereNull('phone'))->count();

        // ============= CAMPAIGN STATS =============
        $pendingCampaigns  = Campaign::where('status', 'draft')->count();
        $approvedCampaigns = Campaign::where('status', 'published')->count();
        $rejectedCampaigns = Campaign::whereIn('status', ['archived', 'closed'])->count();

        // ============= FINANCIAL STATS =============
        $totalDeposited   = $completedOrderValue;
        $pendingDeposits  = Order::where('status', 'pending')->count();
        $rejectedDeposits = Order::whereIn('status', ['cancelled', 'refunded'])->count();
        $depositedCharge  = $totalServiceFees + $totalTaxes;

        // Influencer earnings
        $totalWithdrawn      = Order::where('status', 'completed')->sum('subtotal') ?? 0;
        $pendingWithdrawals  = Order::where('status', 'in-progress')->count();
        $rejectedWithdrawals = Order::whereIn('status', ['cancelled', 'refunded'])->count();
        $withdrawalCharge    = $totalTaxes;

        // ============= TOP PERFORMERS =============
        $topBrands = Brand::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get(['id', 'brand_name', 'industry']);

        $topInfluencers = Influencer::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get(['id', 'display_name']);

        $topPackages = Package::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'price']);

        // ============= RECENT ORDERS =============
        $recentOrders = Order::with(['brand', 'campaign'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'order_number', 'brand_id', 'status', 'total_amount', 'created_at', 'campaign_id']);

        // ============= CHART DATA =============
        // Monthly user registrations for the last 12 months
        $monthlyUsers = User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::create($item->year, $item->month)->format('M Y'),
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
                    'month' => Carbon::create($item->year, $item->month)->format('M Y'),
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
                    'month' => Carbon::create($item->year, $item->month)->format('M Y'),
                    'total' => $item->total ?? 0
                ];
            });

        // Order status distribution
        $orderStatusDistribution = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return view('backend.pages.index', compact(
            'totalOrders', 'completedOrders', 'pendingOrders', 'inProgressOrders',
            'totalOrderValue', 'completedOrderValue', 'pendingOrderValue',
            'totalServiceFees', 'totalTaxes', 'conversionRate',
            'totalBrands', 'activeBrands', 'totalInfluencers', 'activeInfluencers',
            'totalPackages', 'totalCampaigns', 'publishedCampaigns', 'totalUsers',
            'totalReviews',
            'totalUsers', 'activeUsers', 'emailUnverifiedUsers', 'mobileUnverifiedUsers',
            'totalInfluencers', 'activeInfluencers', 'emailUnverifiedInfluencers', 'mobileUnverifiedInfluencers',
            'totalCampaigns', 'pendingCampaigns', 'approvedCampaigns', 'rejectedCampaigns',
            'totalDeposited', 'pendingDeposits', 'rejectedDeposits', 'depositedCharge',
            'totalWithdrawn', 'pendingWithdrawals', 'rejectedWithdrawals', 'withdrawalCharge',
            'monthlyUsers', 'monthlyCampaigns', 'monthlyOrders',
            'topBrands', 'topInfluencers', 'topPackages', 'recentOrders', 'orderStatusDistribution'
        ));
    }
}
