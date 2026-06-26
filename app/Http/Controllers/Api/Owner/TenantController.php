<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Tenant, Contract, Unit};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $owner   = $request->user();
        $tenants = Tenant::where('owner_id',$owner->id)
            ->with(['activeContract.unit.property'])
            ->latest()->paginate(25);

        $data = $tenants->getCollection()->map(fn($t) => [
            'id'        => $t->id,
            'full_name' => $t->full_name,
            'email'     => $t->email,
            'phone'     => $t->phone,
            'status'    => $t->status,
            'contract'  => $t->activeContract ? [
                'id'           => $t->activeContract->id,
                'start_date'   => $t->activeContract->start_date,
                'end_date'     => $t->activeContract->end_date,
                'monthly_rent' => $t->activeContract->monthly_rent,
                'status'       => $t->activeContract->status,
                'unit'         => [
                    'unit_number'   => $t->activeContract->unit->unit_number,
                    'property_name' => $t->activeContract->unit->property->name,
                ],
            ] : null,
        ]);

        return response()->json([
            'data'       => $data,
            'total'      => $tenants->total(),
            'per_page'   => $tenants->perPage(),
            'current_page'=> $tenants->currentPage(),
        ]);
    }

    public function store(Request $request)
    {
        $owner = $request->user();

        // Check plan limit
        if ($owner->isAtPlanLimit()) {
            return response()->json(['message' => "Plan limit reached ({$owner->usedApartments()}/{$owner->max_apartments} apartments). Please upgrade."], 403);
        }

        $data = $request->validate([
            'full_name'        => 'required|string|max:100',
            'email'            => 'required|email',
            'phone'            => 'nullable|string|max:30',
            'national_id'      => 'nullable|string|max:50',
            'unit_id'          => 'required|integer|exists:units,id,owner_id,'.$owner->id,
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'monthly_rent'     => 'required|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'payment_due_day'  => 'nullable|integer|min:1|max:28',
        ]);

        // Check email uniqueness for this owner
        if (Tenant::where('owner_id', $owner->id)->where('email', $data['email'])->exists()) {
            return response()->json(['message' => 'A tenant with this email already exists.'], 422);
        }

        $password = Str::random(10);
        $tenant   = Tenant::create([
            'owner_id'      => $owner->id,
            'full_name'     => $data['full_name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'national_id'   => $data['national_id'] ?? null,
            'password_hash' => Hash::make($password),
            'status'        => 'active',
        ]);

        Contract::create([
            'owner_id'         => $owner->id,
            'tenant_id'        => $tenant->id,
            'unit_id'          => $data['unit_id'],
            'start_date'       => $data['start_date'],
            'end_date'         => $data['end_date'],
            'monthly_rent'     => $data['monthly_rent'],
            'security_deposit' => $data['security_deposit'] ?? 0,
            'payment_due_day'  => $data['payment_due_day'] ?? 1,
            'status'           => 'active',
        ]);

        Unit::where('id', $data['unit_id'])->update(['status' => 'occupied']);

        // Send welcome email with temp password
        try {
            \Mail::to($tenant->email)->queue(new \App\Mail\TenantWelcomeMail($tenant, $password, $owner));
        } catch (\Throwable $e) {}

        return response()->json([
            'message'  => 'Tenant created and welcome email sent.',
            'tenant_id'=> $tenant->id,
        ], 201);
    }
}
