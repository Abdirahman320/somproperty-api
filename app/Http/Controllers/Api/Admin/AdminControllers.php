<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Plan, Owner, Advertisement, Booking, AdBilling, AuditLog};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Carbon\Carbon;

/* ────────────────────────────────────────────
   Plans
──────────────────────────────────────────── */
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
class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $period = (int) $request->get('months', 12);
        $now = Carbon::now();
        $monthly = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i)->startOfMonth();
            $monthly[] = [
                'month'   => $m->format('M Y'),
                'key'     => $m->format('Y-m'),
                'new_owners' => Owner::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count(),
                'mrr'     => (float) Owner::where('status', 'active')
                    ->join('plans', 'owners.plan_id', '=', 'plans.id')
                    ->sum('plans.price_monthly'),
            ];
        }
        return response()->json(['success' => true, 'data' => ['monthly' => $monthly,
            'plan_breakdown' => Plan::withCount(['owners' => fn($q) => $q->where('status', 'active')])->get()
                ->map(fn($p) => ['plan' => $p->name, 'count' => $p->owners_count, 'mrr' => $p->owners_count * $p->price_monthly]),
        ]]);
    }

    public function revenue(Request $request)
    {
        return $this->index($request);
    }
}

/* ────────────────────────────────────────────
   Settings
──────────────────────────────────────────── */
class SettingsController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => [
            'app_name' => config('app.name'),
            'timezone' => config('app.timezone'),
        ]]);
    }

    public function update(Request $request)
    {
        // In a real app, write to a settings table or .env.
        return response()->json(['success' => true, 'message' => 'Settings saved.']);
    }
}

/* ────────────────────────────────────────────
   Audit Logs
──────────────────────────────────────────── */
class AuditController extends Controller
{
    public function index(Request $request)
    {
        $q = AuditLog::latest();
        if ($from = $request->get('from')) $q->whereDate('created_at', '>=', $from);
        if ($to   = $request->get('to'))   $q->whereDate('created_at', '<=', $to);
        if ($a    = $request->get('action')) $q->where('action', 'like', "%$a%");
        $logs = $q->paginate(50);
        return response()->json(['success' => true, 'data' => $logs->items(),
            'meta' => ['total' => $logs->total(), 'current_page' => $logs->currentPage(), 'last_page' => $logs->lastPage()]]);
    }
}

/* ────────────────────────────────────────────
   Advertisements (admin)
──────────────────────────────────────────── */
class AdvertisementController extends Controller
{
    public function index(Request $request)
    {
        $q = Advertisement::with(['owner', 'unit.property'])->withCount('bookings');
        if ($oid = $request->get('owner_id')) $q->where('owner_id', $oid);
        if ($s   = $request->get('status'))   $q->where('status', $s);
        $ads = $q->latest()->paginate(25);
        return response()->json(['success' => true,
            'data' => $ads->getCollection()->map(fn($a) => $this->fmt($a)),
            'meta' => ['total' => $ads->total(), 'current_page' => $ads->currentPage(), 'last_page' => $ads->lastPage()]]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'owner_id'      => 'nullable|exists:owners,id',
            'unit_id'       => 'nullable|exists:units,id',
            'title'         => 'required|string|max:180',
            'description'   => 'nullable|string',
            'monthly_rent'  => 'required|numeric|min:0',
            'bedrooms'      => 'nullable|string|max:20',
            'city'          => 'nullable|string|max:100',
            'address'       => 'nullable|string|max:255',
            'contact_name'  => 'required|string|max:120',
            'contact_phone' => 'required|string|max:40',
            'contact_email' => 'nullable|email',
        ]);
        $ad = Advertisement::create([...$data,
            'created_by_type' => 'admin',
            'created_by_id'   => $request->user()->id,
            'is_published'    => true,
            'status'          => 'available',
        ]);
        return response()->json(['success' => true, 'data' => $this->fmt($ad)], 201);
    }

    public function update(Request $request, $id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->update($request->only(['status', 'is_published', 'title', 'description', 'monthly_rent']));
        return response()->json(['success' => true, 'data' => $this->fmt($ad)]);
    }

    public function destroy($id)
    {
        Advertisement::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Advertisement deleted.']);
    }

    private function fmt(Advertisement $a): array
    {
        return [
            'id' => $a->id, 'title' => $a->title, 'monthly_rent' => $a->monthly_rent,
            'status' => $a->status, 'is_published' => $a->is_published,
            'city' => $a->city, 'address' => $a->address, 'bedrooms' => $a->bedrooms,
            'contact_name' => $a->contact_name, 'contact_phone' => $a->contact_phone, 'contact_email' => $a->contact_email,
            'created_by_type' => $a->created_by_type,
            'bookings_count'  => $a->bookings_count ?? 0,
            'views_count'     => $a->views_count,
            'owner' => $a->owner ? ['id' => $a->owner->id, 'full_name' => $a->owner->full_name] : null,
            'created_at' => $a->created_at,
        ];
    }
}

/* ────────────────────────────────────────────
   Ad & Report Billing (admin)
──────────────────────────────────────────── */
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
class BackupController extends Controller
{
    public function __construct(private DataBackupService $backup) {}

    public function export(Request $request)
    {
        $format = $request->validate(['format' => 'required|in:excel,csv,sql'])['format'];
        $stamp  = now()->format('Ymd_His');
        [$content, $filename, $mime] = match ($format) {
            'csv'   => [$this->backup->exportCsv('admin'),   "som_admin_{$stamp}.csv", 'text/csv'],
            'sql'   => [$this->backup->exportSql('admin'),   "som_admin_{$stamp}.sql", 'application/sql'],
            default => [$this->backup->exportExcel('admin'), "som_admin_{$stamp}.xls", 'application/vnd.ms-excel'],
        };
        return response($content, 200, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|extensions:csv,txt,sql,xls,xml|max:51200']);
        $file = $request->file('file');
        try {
            $report = $this->backup->import($file->getRealPath(), strtolower($file->getClientOriginalExtension()), 'admin');
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Restore failed: ' . $e->getMessage()], 422);
        }
        $summary = $report['tables'] === null ? "{$report['rows']} SQL statements executed."
            : "{$report['rows']} rows imported across {$report['tables']} table(s).";
        return response()->json(['success' => true, 'message' => $summary, 'data' => $report]);
    }
}
