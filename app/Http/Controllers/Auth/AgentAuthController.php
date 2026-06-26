<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AgentAuthController extends Controller {
    public function showLogin() {
        if (auth('agent')->check()) return redirect()->route('agent.dashboard');
        return view('auth.agent-login');
    }
    public function login(Request $request) {
        $creds    = $request->validate(['email' => 'required|email', 'password' => 'required']);
        $password = trim($creds['password']);
        $agent    = Agent::where('email', $creds['email'])->where('is_active', true)->first();
        if (!$agent || !Hash::check($password, $agent->password_hash)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }
        auth('agent')->login($agent);
        return redirect()->route('agent.dashboard');
    }
    public function logout(Request $request) {
        auth('agent')->logout();
        $request->session()->invalidate();
        return redirect()->route('agent.login');
    }
}
