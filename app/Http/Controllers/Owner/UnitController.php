<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Unit, Property};
use Illuminate\Http\Request;

class UnitController extends Controller {
    public function create(Request $request) {
        $properties = Property::where('owner_id', $request->owner->id)->orderBy('name')->get();
        return view('owner.units.create', compact('properties'));
    }
    public function store(Request $request) {
        $owner = $request->owner;
        if ($owner->isAtPlanLimit()) {
            return back()->with('error', "Plan limit reached ({$owner->max_apartments} apartments). Please upgrade.");
        }
        $data = $request->validate([
            'property_id'  => 'required|exists:properties,id', 'unit_number' => 'required|string|max:30',
            'floor_number' => 'nullable|integer', 'bedrooms' => 'nullable|in:studio,1br,2br,3br,4br+',
            'monthly_rent' => 'required|numeric|min:0', 'area_sqft' => 'nullable|numeric',
        ]);
        Unit::create(['owner_id' => $owner->id, ...$data]);
        return back()->with('success', 'Unit added.');
    }
    public function update(Request $request, Unit $unit) {
        abort_if($unit->owner_id !== $request->owner->id, 403);
        $unit->update($request->validate(['monthly_rent' => 'required|numeric|min:0', 'status' => 'required|in:vacant,occupied,maintenance,reserved']));
        return back()->with('success', 'Unit updated.');
    }
    public function destroy(Request $request, Unit $unit) {
        abort_if($unit->owner_id !== $request->owner->id, 403);
        $unit->update(['status' => 'disposed']);
        return back()->with('success', 'Unit removed.');
    }
}
