<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Advertisement, Unit, Owner, Booking};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    public function index()
    {
        $ads = Advertisement::with(['owner', 'unit.property'])
            ->withCount('bookings')
            ->latest()->paginate(25);
        $stats = [
            'total'     => Advertisement::count(),
            'published' => Advertisement::where('is_published', true)->count(),
            'available' => Advertisement::where('status', 'available')->count(),
            'bookings'  => Booking::count(),
        ];
        return view('admin.advertisements.index', compact('ads', 'stats'));
    }

    public function create()
    {
        $owners = Owner::orderBy('full_name')->get();
        $units  = Unit::whereIn('status', ['vacant', 'reserved'])
            ->with(['property', 'owner'])->orderBy('unit_number')->get();
        return view('admin.advertisements.create', compact('owners', 'units'));
    }

    public function store(Request $request)
    {
        $admin = $request->admin ?? auth('admin')->user();
        $data = $request->validate([
            'owner_id'      => 'nullable|exists:owners,id',
            'unit_id'       => 'nullable|exists:units,id',
            'title'         => 'required|string|max:180',
            'description'   => 'nullable|string|max:4000',
            'monthly_rent'  => 'required|numeric|min:0',
            'bedrooms'      => 'nullable|string|max:20',
            'bathrooms'     => 'nullable|integer|min:0|max:20',
            'area_sqft'     => 'nullable|numeric|min:0',
            'city'          => 'nullable|string|max:100',
            'address'       => 'nullable|string|max:255',
            'contact_name'  => 'required|string|max:120',
            'contact_phone' => 'required|string|max:40',
            'contact_email' => 'nullable|email|max:150',
            'image'         => 'nullable|image|max:5120',
        ]);

        $unit = !empty($data['unit_id']) ? Unit::find($data['unit_id']) : null;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('ad_images', 'public');
        }

        Advertisement::create([
            'owner_id'        => $data['owner_id'] ?? $unit?->owner_id,
            'property_id'     => $unit?->property_id,
            'unit_id'         => $unit?->id,
            'title'           => $data['title'],
            'description'     => $data['description'] ?? null,
            'monthly_rent'    => $data['monthly_rent'],
            'bedrooms'        => $data['bedrooms'] ?? $unit?->bedrooms,
            'bathrooms'       => $data['bathrooms'] ?? $unit?->bathrooms,
            'area_sqft'       => $data['area_sqft'] ?? $unit?->area_sqft,
            'city'            => $data['city'] ?? $unit?->property?->city,
            'address'         => $data['address'] ?? $unit?->property?->address,
            'contact_name'    => $data['contact_name'],
            'contact_phone'   => $data['contact_phone'],
            'contact_email'   => $data['contact_email'] ?? null,
            'image_path'      => $imagePath,
            'created_by_type' => 'admin',
            'created_by_id'   => $admin?->id,
            'is_published'    => true,
            'status'          => 'available',
        ]);

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement published to the public home page.');
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        $data = $request->validate([
            'status'       => 'required|in:available,reserved,rented,closed',
            'is_published' => 'nullable|boolean',
        ]);
        $advertisement->update([
            'status'       => $data['status'],
            'is_published' => $request->boolean('is_published'),
        ]);
        return back()->with('success', 'Advertisement updated.');
    }

    public function destroy(Advertisement $advertisement)
    {
        if ($advertisement->image_path) {
            Storage::disk('public')->delete($advertisement->image_path);
        }
        $advertisement->delete();
        return back()->with('success', 'Advertisement removed.');
    }
}
