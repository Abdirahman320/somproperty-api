<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Plan,Owner,Advertisement,Booking,AdBilling,AuditLog};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('owners')->get();
        return response()->json(['success' => true, 'data' => $plans]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:50',
            'slug'           => 'required|string|max:50|unique:plans',
            'price_monthly'  => 'required|numeric|min:0',
            'max_apartments' => 'required|integer|min:1',
            'features'       => 'nullable|array',
            'is_active'      => 'nullable|boolean',
        ]);
        $plan = Plan::create($data);
        return response()->json(['success' => true, 'data' => $plan], 201);
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        $data = $request->validate([
            'name'           => 'nullable|string|max:50',
            'price_monthly'  => 'nullable|numeric|min:0',
            'max_apartments' => 'nullable|integer|min:1',
            'features'       => 'nullable|array',
            'is_active'      => 'nullable|boolean',
        ]);
        $plan->update($data);
        return response()->json(['success' => true, 'data' => $plan]);
    }
}

/* ────────────────────────────────────────────
   Subscriptions
──────────────────────────────────────────── */
