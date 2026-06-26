<?php
namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdvertisementController extends Controller {
    public function index(Request $request) {
        $ads      = collect();
        $bookings = collect();
        return view('agent.advertisements.index', compact('ads', 'bookings'));
    }

    public function create(Request $request) {
        return view('agent.advertisements.create', ['agent' => $request->agent]);
    }

    public function store(Request $request) {
        return redirect()->route('agent.advertisements.index')
            ->with('error', 'Listings feature coming soon.');
    }

    public function update(Request $request, $advertisement) {
        return back()->with('error', 'Listings feature coming soon.');
    }

    public function destroy(Request $request, $advertisement) {
        return back()->with('error', 'Listings feature coming soon.');
    }

    public function updateBooking(Request $request, $booking) {
        return back()->with('error', 'Bookings feature coming soon.');
    }
}
