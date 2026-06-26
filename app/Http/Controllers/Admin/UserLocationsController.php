<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Owner, Tenant, PropertyAgent};
use Illuminate\Http\Request;

class UserLocationsController extends Controller {
    public function index(Request $request) {
        $type    = $request->query('type', 'all');
        $city    = $request->query('city');
        $country = $request->query('country');

        $rows = [];

        if ($type === 'all' || $type === 'owner') {
            Owner::select('id','full_name','email','phone','city','country','status','created_at')
                ->when($city,    fn($q) => $q->where('city',    'like', "%$city%"))
                ->when($country, fn($q) => $q->where('country', 'like', "%$country%"))
                ->get()
                ->each(function ($r) use (&$rows) {
                    $rows[] = [
                        '_type'      => 'Owner',
                        'full_name'  => $r->full_name,
                        'email'      => $r->email,
                        'phone'      => $r->phone,
                        'city'       => $r->city,
                        'country'    => $r->country,
                        'status'     => $r->status,
                        'created_at' => $r->created_at?->format('M j, Y'),
                    ];
                });
        }

        if ($type === 'all' || $type === 'tenant') {
            Tenant::select('id','full_name','email','phone','city','country','status','created_at')
                ->when($city,    fn($q) => $q->where('city',    'like', "%$city%"))
                ->when($country, fn($q) => $q->where('country', 'like', "%$country%"))
                ->get()
                ->each(function ($r) use (&$rows) {
                    $rows[] = [
                        '_type'      => 'Tenant',
                        'full_name'  => $r->full_name,
                        'email'      => $r->email,
                        'phone'      => $r->phone,
                        'city'       => $r->city,
                        'country'    => $r->country,
                        'status'     => $r->status,
                        'created_at' => $r->created_at?->format('M j, Y'),
                    ];
                });
        }

        if ($type === 'all' || $type === 'agent') {
            PropertyAgent::select('id','full_name','email','phone','city','country','status','created_at')
                ->when($city,    fn($q) => $q->where('city',    'like', "%$city%"))
                ->when($country, fn($q) => $q->where('country', 'like', "%$country%"))
                ->get()
                ->each(function ($r) use (&$rows) {
                    $rows[] = [
                        '_type'      => 'Agent',
                        'full_name'  => $r->full_name,
                        'email'      => $r->email,
                        'phone'      => $r->phone,
                        'city'       => $r->city,
                        'country'    => $r->country,
                        'status'     => $r->status,
                        'created_at' => $r->created_at?->format('M j, Y'),
                    ];
                });
        }

        usort($rows, fn($a, $b) =>
            strcmp(
                ($a['country'] ?? '') . ($a['city'] ?? '') . ($a['full_name'] ?? ''),
                ($b['country'] ?? '') . ($b['city'] ?? '') . ($b['full_name'] ?? '')
            )
        );

        $users = collect($rows);

        $allCities = collect(array_values(array_unique(array_filter(array_merge(
            Owner::whereNotNull('city')->distinct()->orderBy('city')->pluck('city')->toArray(),
            Tenant::whereNotNull('city')->distinct()->orderBy('city')->pluck('city')->toArray(),
            PropertyAgent::whereNotNull('city')->distinct()->orderBy('city')->pluck('city')->toArray(),
        )))));

        $allCountries = collect(array_values(array_unique(array_filter(array_merge(
            Owner::whereNotNull('country')->distinct()->orderBy('country')->pluck('country')->toArray(),
            Tenant::whereNotNull('country')->distinct()->orderBy('country')->pluck('country')->toArray(),
            PropertyAgent::whereNotNull('country')->distinct()->orderBy('country')->pluck('country')->toArray(),
        )))));

        return view('admin.user_locations', compact('users', 'type', 'city', 'country', 'allCities', 'allCountries'));
    }
}
