<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Owner, Tenant};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** Owner login — returns Sanctum token */
    public function ownerLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $owner = Owner::where('email', $request->email)
                      ->where('status', 'active')
                      ->with('plan')
                      ->first();

        if (!$owner || !Hash::check($request->password, $owner->password_hash)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $owner->createToken('mobile-owner')->plainTextToken;

        return response()->json([
            'token' => $token,
            'role'  => 'owner',
            'user'  => [
                'id'             => $owner->id,
                'full_name'      => $owner->full_name,
                'email'          => $owner->email,
                'company_name'   => $owner->company_name,
                'phone'          => $owner->phone,
                'max_apartments' => $owner->max_apartments,
                'plan'           => [
                    'id'            => $owner->plan->id,
                    'name'          => $owner->plan->name,
                    'price_monthly' => $owner->plan->price_monthly,
                    'max_apartments'=> $owner->plan->max_apartments,
                    'features'      => $owner->plan->features,
                ],
            ],
        ]);
    }

    /** Tenant login — returns Sanctum token */
    public function tenantLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $tenant = Tenant::where('email', $request->email)
                        ->where('status', 'active')
                        ->first();

        if (!$tenant || !Hash::check($request->password, $tenant->password_hash)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $tenant->createToken('mobile-tenant')->plainTextToken;

        $contract = $tenant->activeContract?->load('unit.property');

        return response()->json([
            'token' => $token,
            'role'  => 'tenant',
            'user'  => [
                'id'        => $tenant->id,
                'full_name' => $tenant->full_name,
                'email'     => $tenant->email,
                'phone'     => $tenant->phone,
                'owner_id'  => $tenant->owner_id,
                'contract'  => $contract ? [
                    'id'              => $contract->id,
                    'start_date'      => $contract->start_date,
                    'end_date'        => $contract->end_date,
                    'monthly_rent'    => $contract->monthly_rent,
                    'security_deposit'=> $contract->security_deposit,
                    'status'          => $contract->status,
                    'unit'            => [
                        'id'          => $contract->unit->id,
                        'unit_number' => $contract->unit->unit_number,
                        'floor_number'=> $contract->unit->floor_number,
                        'bedrooms'    => $contract->unit->bedrooms,
                        'area_sqft'   => $contract->unit->area_sqft,
                        'property'    => [
                            'id'      => $contract->unit->property->id,
                            'name'    => $contract->unit->property->name,
                            'address' => $contract->unit->property->address,
                        ],
                    ],
                ] : null,
            ],
        ]);
    }

    /** Agent login — uses owner credentials, returns agent role */
    public function agentLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $owner = Owner::where('email', $request->email)
                      ->where('status', 'active')
                      ->first();

        if (!$owner || !Hash::check($request->password, $owner->password_hash)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $owner->createToken('mobile-agent')->plainTextToken;

        return response()->json([
            'token' => $token,
            'role'  => 'agent',
            'user'  => [
                'id'           => $owner->id,
                'full_name'    => $owner->full_name,
                'email'        => $owner->email,
                'company_name' => $owner->company_name,
            ],
        ]);
    }

    /** Change password — works for both owner and tenant tokens */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->update(['password_hash' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    /** Logout — revoke current token */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
