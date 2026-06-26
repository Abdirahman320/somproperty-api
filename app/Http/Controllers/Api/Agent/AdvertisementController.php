<?php
namespace App\Http\Controllers\Api\Agent;
use App\Http\Controllers\Controller;
use App\Models\{Advertisement, AdvertisementImage, Booking};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    public function index(Request $request)
    {
        $ads = Advertisement::where('agent_id', $request->user()->id)
            ->with('images')
            ->latest()
            ->paginate(20);

        return response()->json([
            'data'         => $ads->getCollection()->map(fn($ad) => $this->fmt($ad)),
            'total'        => $ads->total(),
            'current_page' => $ads->currentPage(),
            'last_page'    => $ads->lastPage(),
            'per_page'     => $ads->perPage(),
        ]);
    }

    public function show(Request $request, $id)
    {
        $ad = Advertisement::where('agent_id', $request->user()->id)
            ->with('images')
            ->findOrFail($id);
        return response()->json($this->fmt($ad));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:200',
            'description'   => 'nullable|string',
            'monthly_rent'  => 'required|numeric|min:0',
            'bedrooms'      => 'nullable|string',
            'bathrooms'     => 'nullable|integer',
            'city'          => 'nullable|string|max:100',
            'address'       => 'nullable|string|max:200',
            'contact_name'  => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:40',
            'contact_email' => 'nullable|email|max:150',
            'image'         => 'nullable|image|max:5120',
            'images.*'      => 'nullable|image|max:5120',
        ]);

        $agent = $request->user();
        $ad = Advertisement::create(array_merge(
            collect($data)->except(['image', 'images'])->toArray(),
            [
                'agent_id'        => $agent->id,
                'created_by_type' => 'agent',
                'created_by_id'   => $agent->id,
                'is_published'    => true,
                'status'          => 'available',
            ]
        ));

        // Single image upload (from mobile multipart POST)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('ad_images', 'public');
            AdvertisementImage::create(['advertisement_id' => $ad->id, 'image_path' => $path, 'sort_order' => 0]);
            $ad->update(['image_path' => $path]);
        }

        // Multiple images upload (from web form or mobile)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store('ad_images', 'public');
                AdvertisementImage::create(['advertisement_id' => $ad->id, 'image_path' => $path, 'sort_order' => $i]);
                if ($i === 0) $ad->update(['image_path' => $path]);
            }
        }

        $ad->load('images');
        return response()->json($this->fmt($ad), 201);
    }

    public function update(Request $request, $id)
    {
        $ad = Advertisement::where('agent_id', $request->user()->id)->findOrFail($id);
        $data = $request->validate([
            'title'         => 'sometimes|string|max:200',
            'description'   => 'nullable|string',
            'monthly_rent'  => 'sometimes|numeric|min:0',
            'bedrooms'      => 'nullable|string',
            'bathrooms'     => 'nullable|integer',
            'city'          => 'nullable|string|max:100',
            'address'       => 'nullable|string|max:200',
            'contact_name'  => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:40',
            'contact_email' => 'nullable|email|max:150',
            'status'        => 'sometimes|in:available,rented',
        ]);
        $ad->update($data);
        $ad->load('images');
        return response()->json($this->fmt($ad));
    }

    public function destroy(Request $request, $id)
    {
        $ad = Advertisement::where('agent_id', $request->user()->id)->findOrFail($id);
        $ad->delete();
        return response()->json(['message' => 'Deleted.']);
    }

    public function bookings(Request $request)
    {
        $bookings = Booking::whereHas('advertisement', fn($q) => $q->where('agent_id', $request->user()->id))
            ->with('advertisement')
            ->latest()
            ->paginate(20);
        return response()->json($bookings);
    }

    public function updateBooking(Request $request, $id)
    {
        $booking = Booking::whereHas('advertisement', fn($q) => $q->where('agent_id', $request->user()->id))
            ->findOrFail($id);
        $booking->update($request->validate(['status' => 'required|in:pending,confirmed,cancelled']));
        return response()->json($booking);
    }

    private function fmt(Advertisement $ad): array
    {
        return [
            'id'            => $ad->id,
            'title'         => $ad->title,
            'description'   => $ad->description,
            'monthly_rent'  => (float) $ad->monthly_rent,
            'bedrooms'      => $ad->bedrooms,
            'bathrooms'     => $ad->bathrooms,
            'area_sqft'     => $ad->area_sqft ? (float) $ad->area_sqft : null,
            'city'          => $ad->city,
            'address'       => $ad->address,
            'status'        => $ad->status,
            'is_published'  => $ad->is_published,
            'views_count'   => $ad->views_count,
            'contact_name'  => $ad->contact_name,
            'contact_phone' => $ad->contact_phone,
            'contact_email' => $ad->contact_email,
            'image_url'     => $ad->image_path
                ? Storage::disk('public')->url($ad->image_path)
                : null,
            'images'        => $ad->images->map(fn($img) => [
                'id'         => $img->id,
                'image_url'  => Storage::disk('public')->url($img->image_path),
                'sort_order' => $img->sort_order,
            ]),
            'created_at'    => $ad->created_at?->format('Y-m-d'),
        ];
    }
}
