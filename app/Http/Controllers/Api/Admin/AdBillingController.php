<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Plan,Owner,Advertisement,Booking,AdBilling,AuditLog};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdBillingController extends Controller
{
    public function index()
    {
        $bills = AdBilling::with(['advertisement', 'owner'])->latest()->paginate(25);
        $stats = [
            'total_billed' => AdBilling::where('status', '!=', 'cancelled')->sum('amount'),
            'paid'         => AdBilling::where('status', 'paid')->sum('amount'),
            'unpaid'       => AdBilling::where('status', 'unpaid')->sum('amount'),
            'count'        => AdBilling::count(),
        ];
        return response()->json(['success' => true,
            'data' => $bills->getCollection()->map(fn($b) => [
                'id' => $b->id, 'category' => $b->category, 'description' => $b->description,
                'amount' => (float) $b->amount, 'currency' => $b->currency, 'status' => $b->status,
                'reference_number' => $b->reference_number,
                'billed_on' => $b->billed_on, 'paid_on' => $b->paid_on,
                'owner' => $b->owner ? ['id' => $b->owner->id, 'full_name' => $b->owner->full_name] : null,
                'advertisement' => $b->advertisement ? ['id' => $b->advertisement->id, 'title' => $b->advertisement->title] : null,
            ]),
            'stats' => $stats,
            'meta'  => ['total' => $bills->total(), 'current_page' => $bills->currentPage(), 'last_page' => $bills->lastPage()]]);
    }

    public function store(Request $request)
    {
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
        $bill = AdBilling::create([...$data,
            'currency'         => $data['currency'] ?? 'USD',
            'billed_on'        => $data['billed_on'] ?? now()->toDateString(),
            'paid_on'          => ($data['status'] === 'paid') ? now()->toDateString() : null,
            'created_by_admin' => $request->user()->id,
        ]);
        return response()->json(['success' => true, 'data' => $bill], 201);
    }

    public function update(Request $request, AdBilling $ad_billing)
    {
        $data = $request->validate(['status' => 'required|in:unpaid,paid,cancelled']);
        $ad_billing->update([
            'status'  => $data['status'],
            'paid_on' => $data['status'] === 'paid' ? ($ad_billing->paid_on ?? now()->toDateString()) : null,
        ]);
        return response()->json(['success' => true, 'data' => $ad_billing]);
    }

    public function destroy(AdBilling $ad_billing)
    {
        $ad_billing->delete();
        return response()->json(['success' => true, 'message' => 'Deleted.']);
    }
}

/* ────────────────────────────────────────────
   Backup (admin)
──────────────────────────────────────────── */
