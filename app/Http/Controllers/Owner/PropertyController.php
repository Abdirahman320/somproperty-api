<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Property, Unit};
use Illuminate\Http\Request;

class PropertyController extends Controller {
    public function index(Request $request) {
        $owner = $request->owner;
        $properties = Property::where('owner_id', $owner->id)
            ->withCount(['units', 'units as occupied_count' => fn($q) => $q->where('status','occupied')])
            ->latest()->get();
        return view('owner.properties.index', compact('properties'));
    }
    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:150', 'address' => 'required|string',
            'city' => 'nullable|string|max:100', 'country' => 'nullable|string|max:100',
            'property_type' => 'nullable|in:residential,commercial,mixed',
            'total_floors' => 'nullable|integer|min:1',
        ]);
        Property::create(['owner_id' => $request->owner->id, ...$data]);
        return redirect()->route('owner.properties.index')->with('success', 'Property created.');
    }
    public function update(Request $request, Property $property) {
        abort_if($property->owner_id !== $request->owner->id, 403);
        $property->update($request->validate(['name' => 'required|string|max:150', 'address' => 'required|string']));
        return back()->with('success', 'Property updated.');
    }
    public function destroy(Request $request, Property $property) {
        abort_if($property->owner_id !== $request->owner->id, 403);
        $property->update(['status' => 'inactive']);
        return redirect()->route('owner.properties.index')->with('success', 'Property deactivated.');
    }
}
