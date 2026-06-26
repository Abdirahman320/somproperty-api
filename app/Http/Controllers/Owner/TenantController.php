<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Tenant, Unit, Contract};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller {
    public function index(Request $request) {
        $owner   = $request->owner;
        $tenants = Tenant::where('owner_id',$owner->id)->with(['activeContract.unit'])->latest()->paginate(25);
        return view('owner.tenants.index', compact('tenants'));
    }

    public function create(Request $request) {
        $owner = $request->owner;
        $units = Unit::where('owner_id',$owner->id)
            ->where('status','vacant')
            ->with('property')
            ->orderBy('unit_number')
            ->get();
        return view('owner.tenants.create', compact('units'));
    }

    public function show(Request $request, Tenant $tenant) {
        abort_if($tenant->owner_id !== $request->owner->id, 403);
        $tenant->load(['activeContract.unit.property','contracts.unit','bills' => fn($q) => $q->latest()->limit(12)]);
        return view('owner.tenants.show', compact('tenant'));
    }

    public function store(Request $request) {
        $owner = $request->owner;

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
        ]);

        // Pre-check duplicate email for this owner to return a friendly error instead of a 500
        if (Tenant::where('owner_id', $owner->id)->where('email', $data['email'])->exists()) {
            return back()->withInput()->withErrors(['email' => 'A tenant with this email already exists.']);
        }

        $password = 'password123';

        $tenant = Tenant::create([
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
            'status'           => 'active',
        ]);

        Unit::where('id', $data['unit_id'])->update(['status' => 'occupied']);

        try {
            \Mail::to($tenant->email)->send(new \App\Mail\TenantWelcomeMail($tenant, $password, $owner));
        } catch (\Throwable $e) {
            // Email failed silently — credentials shown in success banner below
        }

        return redirect()->route('owner.tenants.index')
            ->with('success', "Tenant {$tenant->full_name} created successfully.")
            ->with('new_creds', ['name' => $tenant->full_name, 'email' => $tenant->email, 'password' => $password]);
    }

    public function destroy(Request $request, Tenant $tenant) {
        abort_if($tenant->owner_id !== $request->owner->id, 403);
        $tenant->update(['status' => 'inactive']);
        return back()->with('success', 'Tenant deactivated.');
    }
}
