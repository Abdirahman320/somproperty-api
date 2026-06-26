<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Plan,Owner,Advertisement,Booking,AdBilling,AuditLog};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $q = Owner::with('plan')->whereIn('status', ['active', 'trial', 'suspended', 'cancelled']);
        if ($s = $request->get('status')) $q->where('status', $s);
        if ($p = $request->get('plan_id')) $q->where('plan_id', $p);
        $owners = $q->latest()->paginate(25);
        return response()->json(['success' => true,
            'data' => $owners->getCollection()->map(fn($o) => [
                'id' => $o->id, 'full_name' => $o->full_name, 'email' => $o->email,
                'status' => $o->status, 'plan' => $o->plan?->name,
                'price_monthly' => $o->plan?->price_monthly,
                'subscription_starts_at' => $o->subscription_starts_at,
                'subscription_ends_at'   => $o->subscription_ends_at,
                'trial_ends_at' => $o->trial_ends_at,
            ]),
            'meta' => ['total' => $owners->total(), 'per_page' => $owners->perPage(), 'current_page' => $owners->currentPage(), 'last_page' => $owners->lastPage()]
        ]);
    }
}

/* ────────────────────────────────────────────
   Analytics & Revenue
──────────────────────────────────────────── */
