<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class AuthAgent {
    public function handle(Request $request, Closure $next) {
        if (!auth('agent')->check()) {
            return redirect()->route('agent.login');
        }
        $agent = auth('agent')->user();
        if ($agent->status === 'suspended') {
            auth('agent')->logout();
            return redirect()->route('agent.login')
                ->withErrors(['email' => 'Your account has been suspended. Contact admin.']);
        }
        $request->agent = $agent;
        return $next($request);
    }
}
