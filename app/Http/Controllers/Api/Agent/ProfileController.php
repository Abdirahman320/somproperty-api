<?php
namespace App\Http\Controllers\Api\Agent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $agent = $request->user();
        return response()->json([
            'id'                  => $agent->id,
            'full_name'           => $agent->full_name,
            'email'               => $agent->email,
            'phone'               => $agent->phone,
            'company_name'        => $agent->company_name,
            'city'                => $agent->city,
            'country'             => $agent->country,
            'subscription_plan'   => $agent->subscription_plan,
            'subscription_price'  => $agent->subscription_price,
            'subscription_ends_at'=> $agent->subscription_ends_at,
            'is_subscription_active' => $agent->isSubscriptionActive(),
        ]);
    }

    public function update(Request $request)
    {
        $agent = $request->user();
        $data  = $request->validate([
            'full_name'    => 'sometimes|string|max:120',
            'company_name' => 'nullable|string|max:150',
            'phone'        => 'nullable|string|max:40',
            'city'         => 'nullable|string|max:100',
            'country'      => 'nullable|string|max:100',
        ]);
        $agent->update($data);
        return response()->json(['message' => 'Profile updated.', 'user' => $agent->fresh()]);
    }
}
