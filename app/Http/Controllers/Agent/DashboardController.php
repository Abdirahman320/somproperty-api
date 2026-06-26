<?php
namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function index(Request $request) {
        $agent = $request->agent;
        $owner = $agent->owner;

        $properties = $owner->properties()->where('status', 'active')->with('units')->get();

        $totalUnits  = $owner->units()->whereNotIn('status', ['disposed'])->count();
        $occupied    = $owner->units()->where('status', 'occupied')->count();
        $vacant      = $owner->units()->where('status', 'vacant')->count();
        $tenants     = $owner->tenants()->where('status', 'active')->count();

        return view('agent.dashboard', compact('agent', 'owner', 'properties', 'totalUnits', 'occupied', 'vacant', 'tenants'));
    }
}
