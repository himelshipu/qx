<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\OrderBrandPayment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceivedPaymentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->string('q', '')),
            'status' => (string) $request->string('status', 'all'),
            'method' => (string) $request->string('method', 'all'),
            'type' => (string) $request->string('type', 'all'),
        ];

        $payments = OrderBrandPayment::query()
            ->select([
                'id',
                'order_id',
                'brand_user_id',
                'amount',
                'currency',
                'payment_method',
                'reference_number',
                'invoice_id',
                'status',
                'paypal_transaction_id',
                'submitted_at',
                'created_at',
            ])
            ->with([
                'order:id,order_number,brand_id,campaign_id,status,total_amount,currency,placed_at,buyer_user_id',
                'order.brand:id,brand_name',
                'order.buyer:id,name,email',
                'order.campaign:id,title',
                'brandUser:id,name,email',
            ])
            ->when($filters['q'] !== '', function (Builder $query) use ($filters): void {
                $search = $filters['q'];

                $query->where(function (Builder $subQuery) use ($search): void {
                    $subQuery
                        ->where('reference_number', 'like', '%' . $search . '%')
                        ->orWhere('invoice_id', 'like', '%' . $search . '%')
                        ->orWhere('paypal_transaction_id', 'like', '%' . $search . '%')
                        ->orWhereHas('order', function (Builder $orderQuery) use ($search): void {
                            $orderQuery->where('order_number', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('brandUser', function (Builder $brandUserQuery) use ($search): void {
                            $brandUserQuery
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($filters['status'] !== 'all' && $filters['status'] !== '', fn (Builder $query) => $query->where('status', $filters['status']))
            ->when($filters['method'] !== 'all' && $filters['method'] !== '', fn (Builder $query) => $query->where('payment_method', $filters['method']))
            ->when($filters['type'] !== 'all' && $filters['type'] !== '', function (Builder $query) use ($filters): void {
                if ($filters['type'] === 'campaign') {
                    $query->whereHas('order', fn (Builder $orderQuery) => $orderQuery->whereNotNull('campaign_id'));

                    return;
                }

                if ($filters['type'] === 'package') {
                    $query->whereHas('order', fn (Builder $orderQuery) => $orderQuery->whereNull('campaign_id'));
                }
            })
            ->orderByRaw('COALESCE(submitted_at, created_at) DESC')
            ->paginate(15)
            ->withQueryString();

        $statsBaseQuery = OrderBrandPayment::query();
        $stats = [
            'total' => (int) (clone $statsBaseQuery)->count(),
            'confirmed' => (int) (clone $statsBaseQuery)->where('status', 'confirmed')->count(),
            'pending' => (int) (clone $statsBaseQuery)->where('status', 'pending')->count(),
            'amount' => (float) (clone $statsBaseQuery)->where('status', 'confirmed')->sum('amount'),
        ];

        return view('backend.pages.received-payments.index', [
            'payments' => $payments,
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    public function show(OrderBrandPayment $brandPayment): View
    {
        $brandPayment->load([
            'order:id,order_number,brand_id,campaign_id,status,total_amount,currency,placed_at,buyer_user_id,created_at',
            'order.brand:id,brand_name',
            'order.buyer:id,name,email',
            'order.campaign:id,title,status',
            'order.items:id,order_id,title,influencer_id,line_total,status',
            'order.items.influencer:id,display_name,user_id',
            'order.items.influencer.user:id,name',
            'order.subOrders:id,order_id,influencer_id,status,amount,currency',
            'order.subOrders.influencer:id,display_name,user_id',
            'order.subOrders.influencer.user:id,name',
            'brandUser:id,name,email',
            'confirmedBy:id,name,email',
            'rejectedBy:id,name,email',
        ]);

        return view('backend.pages.received-payments.show', [
            'brandPayment' => $brandPayment,
        ]);
    }
}
