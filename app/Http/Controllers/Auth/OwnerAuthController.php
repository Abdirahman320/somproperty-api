<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OwnerAuthController extends Controller {
    public function showLogin() {
        if (auth('owner')->check()) return redirect()->route('owner.dashboard');
        return view('auth.owner-login');
    }
    public function login(Request $request) {
        $creds    = $request->validate(['email' => 'required|email', 'password' => 'required']);
        $password = trim($creds['password']);
        $owner    = Owner::where('email', $creds['email'])->first();
        if (!$owner || !Hash::check($password, $owner->password_hash)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }
        if ($owner->status === 'suspended') {
            return back()->withErrors(['email' => 'Account suspended. Contact support.']);
        }
        auth('owner')->login($owner);
        return redirect()->route('owner.dashboard');
    }
    public function logout(Request $request) {
        auth('owner')->logout();
        $request->session()->invalidate();
        return redirect()->route('owner.login');
    }
}
