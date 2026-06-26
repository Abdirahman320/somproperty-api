<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $owner = $request->user();
        $properties = Property::where('owner_id',$owner->id)
            ->withCount(['units','units as occupied_count' => fn($q)=>$q->where('status','occupied')])
            ->latest()->get()
            ->map(fn($p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'address'        => $p->address,
                'city'           => $p->city,
                'country'        => $p->country,
                'property_type'  => $p->property_type,
                'total_floors'   => $p->total_floors,
                'status'         => $p->status,
                'units_count'    => $p->units_count,
                'occupied_count' => $p->occupied_count,
                'vacant_count'   => $p->units_count - $p->occupied_count,
                'occupancy_rate' => $p->units_count > 0 ? round($p->occupied_count/$p->units_count*100,1) : 0,
            ]);

        return response()->json(['data' => $properties, 'total' => $properties->count()]);
    }

    public function store(Request $request)
    {
        $owner = $request->user();
        $data  = $request->validate([
            'name'          => 'required|string|max:150',
            'address'       => 'required|string',
            'city'          => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:100',
            'property_type' => 'nullable|in:residential,commercial,mixed',
            'total_floors'  => 'nullable|integer|min:1',
        ]);
        $property = Property::create(['owner_id'=>$owner->id, ...$data]);
        return response()->json(['message'=>'Property created','data'=>$property], 201);
    }

    public function units(Request $request, $propertyId)
    {
        $owner = $request->user();
        $units = \App\Models\Unit::where('owner_id',$owner->id)
            ->where('property_id',$propertyId)
            ->with(['activeContract.tenant'])
            ->get()
            ->map(fn($u) => [
                'id'           => $u->id,
                'unit_number'  => $u->unit_number,
                'floor_number' => $u->floor_number,
                'bedrooms'     => $u->bedrooms,
                'area_sqft'    => $u->area_sqft,
                'monthly_rent' => $u->monthly_rent,
                'status'       => $u->status,
                'tenant'       => $u->activeContract?->tenant ? [
                    'id'        => $u->activeContract->tenant->id,
                    'full_name' => $u->activeContract->tenant->full_name,
                    'email'     => $u->activeContract->tenant->email,
                ] : null,
            ]);
        return response()->json(['data'=>$units]);
    }
}
