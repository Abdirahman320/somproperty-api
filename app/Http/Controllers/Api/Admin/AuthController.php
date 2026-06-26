<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $admin = AdminUser::where('email', $request->email)->where('is_active', true)->first();
        if (!$admin || !Hash::check($request->password, $admin->password_hash)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }
        $admin->update(['last_login_at' => now()]);
        $token = $admin->createToken('mobile-admin')->plainTextToken;
        return response()->json(['success' => true, 'data' => [
            'token' => $token, 'role' => 'admin',
            'user'  => ['id' => $admin->id, 'name' => $admin->name, 'email' => $admin->email, 'role' => $admin->role],
        ]]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logged out']);
    }
}
