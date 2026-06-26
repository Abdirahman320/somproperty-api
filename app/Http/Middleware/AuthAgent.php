<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;

class AuthAgent {
    public function handle(Request $request, Closure $next) {
        if (!auth('agent')->check()) return redirect()->route('agent.login');
        $agent = auth('agent')->user();
        $request->merge(['agent' => $agent]);
        return $next($request);
    }
}
