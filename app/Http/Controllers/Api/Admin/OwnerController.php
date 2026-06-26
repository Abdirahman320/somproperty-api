<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Owner, Plan};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OwnerController extends Controller
{
    public function index(Request $request)
    {
        $q = Owner::with('plan')->withCount(['properties', 'units', 'tenants']);
        if ($s = $request->get('search')) {
            $q->where(fn($w) => $w->where('full_name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('company_name', 'like', "%$s%"));
        }
        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }
        $owners = $q->latest()->paginate(25);
        return response()->json(['success' => true, 'data' => $owners->getCollection()->map(fn($o) => $this->fmt($o)),
            'meta' => ['total' => $owners->total(), 'current_page' => $owners->currentPage(), 'last_page' => $owners->lastPage(), 'per_page' => $owners->perPage()]]);
    }

    public function show($id)
    {
        $o = Owner::with(['plan'])->withCount(['properties', 'units', 'tenants'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $this->fmt($o)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'      => 'required|string|max:100',
            'company_name'   => 'nullable|string|max:150',
            'email'          => 'required|email|unique:owners,email',
            'phone'          => 'nullable|string|max:30',
            'plan_id'        => 'required|exists:plans,id',
            'max_apartments' => 'nullable|integer|min:1',
            'password'       => 'nullable|string|min:8',
        ]);
        $plan     = Plan::findOrFail($data['plan_id']);
        $password = $data['password'] ?? Str::random(12);
        $owner = Owner::create([
            'full_name'      => $data['full_name'],
            'company_name'   => $data['company_name'] ?? null,
            'email'          => $data['email'],
            'phone'          => $data['phone'] ?? null,
            'plan_id'        => $data['plan_id'],
            'max_apartments' => $data['max_apartments'] ?? $plan->max_apartments,
            'password_hash'  => Hash::make($password),
            'status'         => 'active',
            'created_by'     => $request->user()->id,
        ]);
        return response()->json(['success' => true, 'message' => 'Owner created.', 'data' => $this->fmt($owner), 'temp_password' => $password], 201);
    }

    public function suspend($id)
    {
        Owner::findOrFail($id)->update(['status' => 'suspended']);
        return response()->json(['success' => true, 'message' => 'Owner suspended.']);
    }

    public function activate($id)
    {
        Owner::findOrFail($id)->update(['status' => 'active']);
        return response()->json(['success' => true, 'message' => 'Owner activated.']);
    }

    public function destroy($id)
    {
        Owner::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Owner deleted.']);
    }

    private function fmt(Owner $o): array
    {
        return [
            'id'             => $o->id,
            'full_name'      => $o->full_name,
            'company_name'   => $o->company_name,
            'email'          => $o->email,
            'phone'          => $o->phone,
            'status'         => $o->status,
            'max_apartments' => $o->max_apartments,
            'used_apartments'=> $o->units_count ?? 0,
            'plan'           => $o->plan ? ['id' => $o->plan->id, 'name' => $o->plan->name, 'price_monthly' => $o->plan->price_monthly] : null,
            'subscription_starts_at' => $o->subscription_starts_at,
            'subscription_ends_at'   => $o->subscription_ends_at,
            'trial_ends_at'          => $o->trial_ends_at,
            'properties_count'       => $o->properties_count ?? 0,
            'tenants_count'          => $o->tenants_count ?? 0,
            'created_at'             => $o->created_at,
        ];
    }
}
