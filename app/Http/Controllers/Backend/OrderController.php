<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');

        $orders = Order::query()
            ->with([
                'buyer:id,name,email',
                'brand:id,brand_name',
                'campaign:id,title'
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('order_number', 'like', '%' . $search . '%')
                        ->orWhereHas('buyer', function ($buyerQuery) use ($search) {
                            $buyerQuery
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('brand', function ($brandQuery) use ($search) {
                            $brandQuery->where('brand_name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('campaign', function ($campaignQuery) use ($search) {
                            $campaignQuery->where('title', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($status !== 'all', fn($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'revenue' => (float) Order::where('status', 'completed')->sum('total_amount'),
        ];

        return view('backend.pages.orders.index', [
            'orders' => $orders,
            'stats' => $stats,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load([
            'buyer:id,name,email,phone',
            'brand:id,brand_name',
            'campaign:id,title,status',
            'acceptedBy:id,name,email',
            'acceptedForCreator:id,user_id,display_name',
            'acceptedForCreator.user:id,name',
            'items:id,order_id,creator_id,package_id,title,quantity,unit_price,line_total,status,due_date',
            'items.creator:id,user_id,display_name',
            'items.creator.user:id,name',
            'items.package:id,name,base_price,currency',
            'payments:id,order_id,status,amount,currency,payment_provider,paid_at,created_at'
        ]);

        return view('backend.pages.orders.show', [
            'order' => $order,
        ]);
    }
}

