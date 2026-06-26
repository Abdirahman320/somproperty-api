<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{AdBilling, Advertisement, Owner};
use Illuminate\Http\Request;

class AdBillingController extends Controller
{
    public function index()
    {
        $billings = AdBilling::with(['advertisement', 'owner'])->latest()->paginate(25);
        $stats = [
            'total_billed' => AdBilling::where('status', '!=', 'cancelled')->sum('amount'),
            'paid'         => AdBilling::where('status', 'paid')->sum('amount'),
            'unpaid'       => AdBilling::where('status', 'unpaid')->sum('amount'),
            'count'        => AdBilling::count(),
        ];
        $owners = Owner::orderBy('full_name')->get();
        $ads    = Advertisement::with('owner')->latest()->limit(200)->get();
        return view('admin.ad_billing.index', compact('billings', 'stats', 'owners', 'ads'));
    }

    public function store(Request $request)
    {
        $admin = auth('admin')->user();
        $data = $request->validate([
            'category'         => 'required|in:advertisement,report,feature,other',
            'advertisement_id' => 'nullable|exists:advertisements,id',
            'owner_id'         => 'nullable|exists:owners,id',
            'description'      => 'required|string|max:200',
            'amount'           => 'required|numeric|min:0',
            'currency'         => 'nullable|string|max:8',
            'status'           => 'required|in:unpaid,paid,cancelled',
            'reference_number' => 'nullable|string|max:50',
            'billed_on'        => 'nullable|date',
            'notes'            => 'nullable|string|max:1000',
        ]);

        if (empty($data['owner_id']) && !empty($data['advertisement_id'])) {
            $data['owner_id'] = Advertisement::find($data['advertisement_id'])?->owner_id;
        }

        AdBilling::create([
            ...$data,
            'currency'        => $data['currency'] ?? 'USD',
            'billed_on'       => $data['billed_on'] ?? now()->toDateString(),
            'paid_on'         => ($data['status'] === 'paid') ? now()->toDateString() : null,
            'created_by_admin'=> $admin?->id,
        ]);

        return redirect()->route('admin.ad-billing.index')
            ->with('success', 'Billing record registered.');
    }

    public function update(Request $request, AdBilling $ad_billing)
    {
        $data = $request->validate([
            'status' => 'required|in:unpaid,paid,cancelled',
        ]);
        $ad_billing->update([
            'status'  => $data['status'],
            'paid_on' => $data['status'] === 'paid' ? ($ad_billing->paid_on ?? now()->toDateString()) : null,
        ]);
        return back()->with('success', 'Billing record updated.');
    }

    public function destroy(AdBilling $ad_billing)
    {
        $ad_billing->delete();
        return back()->with('success', 'Billing record deleted.');
    }
}
