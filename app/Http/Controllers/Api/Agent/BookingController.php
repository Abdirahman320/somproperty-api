<?php
namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Record a showing/booking request from an agent.
     * Currently logs the request and returns confirmation.
     * A bookings table can be added in a future migration to persist these.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'listing_id'     => 'required|string',
            'client_name'    => 'required|string|max:255',
            'client_phone'   => 'required|string|max:30',
            'preferred_date' => 'required|string|max:50',
            'notes'          => 'nullable|string|max:500',
        ]);

        // Log the booking request for now (can persist to DB in future)
        \Log::info('Agent booking request', array_merge($validated, [
            'agent_id' => $request->user()?->id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Showing request recorded for ' . $validated['client_name'] . ' on ' . $validated['preferred_date'] . '.',
            'booking' => [
                'id'             => 'bk_' . time(),
                'listing_id'     => $validated['listing_id'],
                'client_name'    => $validated['client_name'],
                'client_phone'   => $validated['client_phone'],
                'preferred_date' => $validated['preferred_date'],
                'notes'          => $validated['notes'] ?? '',
                'status'         => 'pending',
            ],
        ], 201);
    }
}
