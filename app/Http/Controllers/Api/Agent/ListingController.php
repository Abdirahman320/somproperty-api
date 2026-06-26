<?php
namespace App\Http\Controllers\Api\Agent;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingController extends Controller
{
    // Map DB enum values to agent-friendly display labels
    private static array $statusMap = [
        'vacant'      => 'available',
        'occupied'    => 'rented',
        'maintenance' => 'closed',
        'reserved'    => 'closed',
        'disposed'    => 'closed',
    ];

    // Map agent label back to DB enum
    private static array $dbStatusMap = [
        'available' => 'vacant',
        'rented'    => 'occupied',
        'closed'    => 'reserved',
    ];

    /**
     * Return all (non-disposed) units for the agent's owner account.
     * Includes property image URL when one exists.
     */
    public function index(Request $request)
    {
        $user  = $request->user();
        $owner = $user instanceof Owner ? $user : null;

        $units = Unit::with(['property'])
            ->whereNotIn('status', ['disposed'])
            ->whereHas('property', function ($q) use ($owner) {
                if ($owner) {
                    $q->where('owner_id', $owner->id);
                }
            })
            ->orderByDesc('id')
            ->get();

        $listings = $units->map(fn(Unit $unit) => $this->formatUnit($unit));

        return response()->json([
            'data'  => $listings,
            'total' => $listings->count(),
        ]);
    }

    /**
     * Create an agent listing (with optional image upload).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_name' => 'required|string|max:255',
            'unit_number'   => 'nullable|string|max:20',
            'floor_number'  => 'nullable|integer|min:0',
            'bedrooms'      => 'nullable|string|max:10',
            'area_sqft'     => 'nullable|numeric|min:0',
            'monthly_rent'  => 'required|numeric|min:0',
            'description'   => 'nullable|string|max:1000',
            'image'         => 'nullable|image|max:5120',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path     = $request->file('image')->store('agent_listings', 'public');
            $imageUrl = url('storage/' . $path);
        }

        $beds = match ($validated['bedrooms'] ?? 'Studio') {
            '1BR'  => 1, '2BR' => 2, '3BR' => 3, '4BR+' => 4, default => 0,
        };

        return response()->json([
            'success' => true,
            'listing' => [
                'id'           => 'agent_' . time(),
                'title'        => ($validated['bedrooms'] ?? 'Studio') . ' – ' . $validated['property_name'],
                'property_name'=> $validated['property_name'],
                'address'      => '',
                'type'         => 'Apartment',
                'status'       => 'available',
                'bedrooms'     => $beds,
                'monthly_rent' => (float) $validated['monthly_rent'],
                'unit_number'  => $validated['unit_number'] ?? '',
                'floor'        => (int) ($validated['floor_number'] ?? 0),
                'area_sqft'    => (float) ($validated['area_sqft'] ?? 0),
                'image_url'    => $imageUrl,
            ],
        ], 201);
    }

    /**
     * Update a unit's status from the agent app.
     * Accepts agent labels: available | rented | closed
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:available,rented,closed',
        ]);

        $user  = $request->user();
        $owner = $user instanceof Owner ? $user : null;

        $unit = Unit::with(['property'])
            ->where('id', $id)
            ->whereHas('property', function ($q) use ($owner) {
                if ($owner) {
                    $q->where('owner_id', $owner->id);
                }
            })
            ->firstOrFail();

        $dbStatus = self::$dbStatusMap[$request->status] ?? 'vacant';
        $unit->update(['status' => $dbStatus]);

        return response()->json([
            'success' => true,
            'listing' => $this->formatUnit($unit->refresh()),
        ]);
    }

    private function formatUnit(Unit $unit): array
    {
        $prop     = $unit->property;
        $imageUrl = $prop->image_path ? url('storage/' . $prop->image_path) : null;
        $agentStatus = self::$statusMap[$unit->status] ?? 'available';

        return [
            'id'           => $unit->id,
            'title'        => trim(($unit->bedrooms ? $unit->bedrooms . 'BR' : 'Studio') . ' – ' . $prop->name),
            'property_name'=> $prop->name,
            'address'      => $prop->address . ($prop->city ? ', ' . $prop->city : ''),
            'type'         => $prop->property_type ?? 'Apartment',
            'status'       => $agentStatus,
            'bedrooms'     => (int) $unit->bedrooms,
            'monthly_rent' => (float) $unit->monthly_rent,
            'unit_number'  => $unit->unit_number,
            'floor'        => (int) $unit->floor_number,
            'area_sqft'    => (float) $unit->area_sqft,
            'bathrooms'    => (int) $unit->bathrooms,
            'image_url'    => $imageUrl,
        ];
    }
}
