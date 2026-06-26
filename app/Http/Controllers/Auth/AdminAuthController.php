<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller {
    public function showLogin() {
        if (auth('admin')->check()) return redirect()->route('admin.dashboard');
        return view('auth.admin-login');
    }
    public function login(Request $request) {
        $creds = $request->validate(['email' => 'required|email', 'password' => 'required']);
        $admin = AdminUser::where('email', $creds['email'])->where('is_active', true)->first();
        if (!$admin || !Hash::check($creds['password'], $admin->password_hash)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }
        $admin->update(['last_login_at' => now()]);
        auth('admin')->login($admin);
        return redirect()->route('admin.dashboard');
    }
    public function logout(Request $request) {
        auth('admin')->logout();
        $request->session()->invalidate();
        return redirect()->route('admin.login');
    }
}
