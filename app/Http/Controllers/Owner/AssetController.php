<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Asset, TechnicalIssue, Property, Unit};
use Illuminate\Http\Request;

class AssetController extends Controller {
    public function index(Request $request) {
        $owner  = $request->owner;
        $assets = Asset::where('owner_id', $owner->id)->with('property')->latest()->get();
        $issues = TechnicalIssue::where('owner_id', $owner->id)->whereNotIn('status',['closed'])->latest()->get();
        return view('owner.assets.index', compact('assets','issues'));
    }
    public function create(Request $request) {
        $properties = Property::where('owner_id', $request->owner->id)->orderBy('name')->get();
        return view('owner.assets.create', compact('properties'));
    }
    public function createIssue(Request $request) {
        $owner      = $request->owner;
        $properties = Property::where('owner_id', $owner->id)->orderBy('name')->get();
        $units      = Unit::where('owner_id', $owner->id)->with('property')->orderBy('unit_number')->get();
        return view('owner.assets.issues.create', compact('properties','units'));
    }
    public function store(Request $request) {
        $data = $request->validate([
            'property_id' => 'required|exists:properties,id', 'name' => 'required|string|max:150',
            'category'    => 'required|in:mechanical,electrical,plumbing,electronic,furniture,vehicle,other',
            'brand' => 'nullable|string', 'model' => 'nullable|string', 'serial_number' => 'nullable|string',
            'location' => 'nullable|string', 'purchase_value' => 'nullable|numeric',
            'purchase_date' => 'nullable|date', 'warranty_expires_at' => 'nullable|date',
        ]);
        Asset::create(['owner_id' => $request->owner->id, ...$data]);
        return redirect()->route('owner.assets.index')->with('success', 'Asset registered.');
    }
    public function storeIssue(Request $request) {
        $data = $request->validate([
            'property_id' => 'required|exists:properties,id', 'title' => 'required|string|max:200',
            'description' => 'required|string', 'priority' => 'required|in:low,medium,high,critical',
            'unit_id' => 'nullable|exists:units,id', 'assigned_to' => 'nullable|string|max:100',
            'scheduled_date' => 'nullable|date', 'estimated_cost' => 'nullable|numeric',
        ]);
        TechnicalIssue::create(['owner_id' => $request->owner->id, 'reported_by' => 'owner', 'reporter_id' => $request->owner->id, ...$data]);
        return redirect()->route('owner.assets.index')->with('success', 'Issue logged.');
    }
    public function updateIssue(Request $request, TechnicalIssue $issue) {
        abort_if($issue->owner_id !== $request->owner->id, 403);
        $issue->update($request->validate(['status' => 'required|in:open,assigned,in_progress,resolved,closed', 'actual_cost' => 'nullable|numeric', 'resolution_notes' => 'nullable|string']));
        return back()->with('success', 'Issue updated.');
    }
}
