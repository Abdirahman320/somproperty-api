<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TenantAuthController extends Controller {
    public function showLogin() {
        if (auth('tenant')->check()) return redirect()->route('tenant.home');
        return view('auth.tenant-login');
    }
    public function login(Request $request) {
        $creds    = $request->validate(['email' => 'required|email', 'password' => 'required']);
        $password = trim($creds['password']);
        $tenant   = Tenant::where('email', $creds['email'])->where('status', 'active')->first();
        if (!$tenant || !Hash::check($password, $tenant->password_hash)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }
        auth('tenant')->login($tenant);
        return redirect()->route('tenant.home');
    }
    public function logout(Request $request) {
        auth('tenant')->logout();
        $request->session()->invalidate();
        return redirect()->route('tenant.login');
    }
}
