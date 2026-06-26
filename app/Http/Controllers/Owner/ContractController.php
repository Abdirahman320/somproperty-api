<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Contract, Unit};
use Illuminate\Http\Request;

class ContractController extends Controller {
    public function store(Request $request, \App\Models\Tenant $tenant) {
        abort_if($tenant->owner_id !== $request->owner->id, 403);
        $data = $request->validate([
            'unit_id' => 'required|exists:units,id', 'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date', 'monthly_rent' => 'required|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0', 'payment_due_day' => 'nullable|integer|min:1|max:28',
        ]);
        // Terminate any existing active contract
        Contract::where('tenant_id', $tenant->id)->where('status','active')->update(['status'=>'terminated','terminated_at'=>now()]);
        $contract = Contract::create(['owner_id' => $request->owner->id, 'tenant_id' => $tenant->id, 'status' => 'active', ...$data]);
        Unit::find($data['unit_id'])->update(['status' => 'occupied']);
        return back()->with('success', 'Contract created.');
    }
    public function terminate(Request $request, Contract $contract) {
        abort_if($contract->owner_id !== $request->owner->id, 403);
        $contract->update(['status' => 'terminated', 'terminated_at' => now(), 'termination_reason' => $request->reason]);
        $contract->unit->update(['status' => 'vacant']);
        return back()->with('success', 'Contract terminated.');
    }
}
