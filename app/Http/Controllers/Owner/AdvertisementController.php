<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Advertisement, AdvertisementImage, Unit, Booking};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    public function index(Request $request)
    {
        $owner = $request->owner;
        $ads = Advertisement::where('owner_id', $owner->id)
            ->with(['unit.property', 'images'])
            ->withCount('bookings')
            ->latest()->get();
        $bookings = Booking::where('owner_id', $owner->id)
            ->with('advertisement')
            ->latest()->limit(50)->get();
        return view('owner.advertisements.index', compact('ads', 'bookings'));
    }

    public function create(Request $request)
    {
        $owner = $request->owner;
        $units = Unit::where('owner_id', $owner->id)
            ->whereIn('status', ['vacant', 'reserved'])
            ->with('property')->orderBy('unit_number')->get();
        return view('owner.advertisements.create', compact('units', 'owner'));
    }

    public function store(Request $request)
    {
        $owner = $request->owner;
        $data = $request->validate([
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
            'images.*'      => 'nullable|image|max:5120',
        ]);

        $unit = null;
        if (!empty($data['unit_id'])) {
            $unit = Unit::where('owner_id', $owner->id)->find($data['unit_id']);
            abort_if(!$unit, 422, 'Invalid unit.');
        }

        $ad = Advertisement::create([
            'owner_id'       => $owner->id,
            'property_id'    => $unit?->property_id,
            'unit_id'        => $unit?->id,
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'monthly_rent'   => $data['monthly_rent'],
            'bedrooms'       => $data['bedrooms'] ?? $unit?->bedrooms,
            'bathrooms'      => $data['bathrooms'] ?? $unit?->bathrooms,
            'area_sqft'      => $data['area_sqft'] ?? $unit?->area_sqft,
            'city'           => $data['city'] ?? $unit?->property?->city,
            'address'        => $data['address'] ?? $unit?->property?->address,
            'contact_name'   => $data['contact_name'],
            'contact_phone'  => $data['contact_phone'],
            'contact_email'  => $data['contact_email'] ?? null,
            'created_by_type'=> 'owner',
            'created_by_id'  => $owner->id,
            'is_published'   => true,
            'status'         => 'available',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('ad_images', 'public');
                AdvertisementImage::create([
                    'advertisement_id' => $ad->id,
                    'image_path'       => $path,
                    'sort_order'       => $i,
                ]);
                if ($i === 0) $ad->update(['image_path' => $path]);
            }
        }

        return redirect()->route('owner.advertisements.index')
            ->with('success', 'Advertisement published. It is now visible on the public home page.');
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        abort_if($advertisement->owner_id !== $request->owner->id, 403);
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

    public function destroy(Request $request, Advertisement $advertisement)
    {
        abort_if($advertisement->owner_id !== $request->owner->id, 403);
        foreach ($advertisement->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        if ($advertisement->image_path) {
            Storage::disk('public')->delete($advertisement->image_path);
        }
        $advertisement->delete();
        return back()->with('success', 'Advertisement removed.');
    }

    public function updateBooking(Request $request, Booking $booking)
    {
        abort_if($booking->owner_id !== $request->owner->id, 403);
        $data = $request->validate([
            'status' => 'required|in:new,contacted,viewing_scheduled,closed,cancelled',
        ]);
        $booking->update($data);
        return back()->with('success', 'Inquiry status updated.');
    }
}
