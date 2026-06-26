<?php
namespace App\Http\Controllers;
use App\Models\{Advertisement, Booking};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicHomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Advertisement::public()->with(['unit.property']);

        if ($city = trim((string) $request->get('city'))) {
            $query->where('city', 'like', "%{$city}%");
        }
        if ($beds = $request->get('bedrooms')) {
            $query->where('bedrooms', $beds);
        }
        if ($max = $request->get('max_rent')) {
            $query->where('monthly_rent', '<=', (float) $max);
        }

        $ads = $query->latest()->paginate(12)->withQueryString();

        $cities = Advertisement::public()
            ->whereNotNull('city')->distinct()->orderBy('city')->pluck('city');

        return view('public.home', compact('ads', 'cities'));
    }

    public function show(Advertisement $advertisement)
    {
        abort_unless($advertisement->is_published && in_array($advertisement->status, ['available', 'reserved']), 404);
        $advertisement->increment('views_count');
        $advertisement->load(['unit.property', 'owner']);
        return view('public.listing', compact('advertisement'));
    }

    public function book(Request $request, Advertisement $advertisement)
    {
        abort_unless($advertisement->is_published, 404);
        $data = $request->validate([
            'name'              => 'required|string|max:120',
            'email'             => 'required|email|max:150',
            'phone'             => 'nullable|string|max:40',
            'preferred_move_in' => 'nullable|date|after_or_equal:today',
            'message'           => 'nullable|string|max:2000',
        ]);

        Booking::create([
            'advertisement_id'  => $advertisement->id,
            'owner_id'          => $advertisement->owner_id,
            'agent_id'          => $advertisement->agent_id,
            'unit_id'           => $advertisement->unit_id,
            'name'              => $data['name'],
            'email'             => $data['email'],
            'phone'             => $data['phone'] ?? null,
            'preferred_move_in' => $data['preferred_move_in'] ?? null,
            'message'           => $data['message'] ?? null,
            'status'            => 'new',
            'reference'         => 'BK-' . strtoupper(Str::random(8)),
        ]);

        return back()->with('booked', true)
            ->with('success', 'Your booking request has been sent to the owner. They will contact you directly — no payment is required.');
    }
}
