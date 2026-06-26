<?php
namespace App\Http\Controllers\Api\Agent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $agent = $request->user();
        $ads   = $agent->advertisements()->withCount('bookings')->get();

        return response()->json([
            'total_listings'   => $ads->count(),
            'active_listings'  => $ads->where('status', 'available')->count(),
            'total_bookings'   => $ads->sum('bookings_count'),
            'subscription'     => [
                'plan'       => $agent->subscription_plan,
                'price'      => $agent->subscription_price,
                'ends_at'    => $agent->subscription_ends_at,
                'is_active'  => $agent->isSubscriptionActive(),
            ],
        ]);
    }
}
