<?php
namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function index(Request $request) {
        $agent = $request->agent;
        $stats = [
            'ads'          => 0,
            'active'       => 0,
            'bookings'     => 0,
            'new_bookings' => 0,
        ];
        $recentBookings = collect();
        $recentAds      = collect();
        return view('agent.dashboard', compact('agent', 'stats', 'recentBookings', 'recentAds'));
    }
}
