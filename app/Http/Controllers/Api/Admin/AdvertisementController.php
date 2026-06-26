<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Plan,Owner,Advertisement,Booking,AdBilling,AuditLog};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
