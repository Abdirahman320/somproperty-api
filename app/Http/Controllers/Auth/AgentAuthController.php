<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentAuthController extends Controller {
    public function showLogin() {
        if (auth('agent')->check()) return redirect()->route('agent.dashboard');
        return view('agent.auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('agent')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $agent = auth('agent')->user();
            if ($agent->status === 'suspended') {
                Auth::guard('agent')->logout();
                return back()->withErrors(['email' => 'Your account has been suspended.']);
            }
            $request->session()->regenerate();
            return redirect()->route('agent.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
    }

    public function logout(Request $request) {
        Auth::guard('agent')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('agent.login');
    }
}
