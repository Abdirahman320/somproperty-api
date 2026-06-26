<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;

class AuthTenant {
    public function handle(Request $request, Closure $next) {
        if (!auth('tenant')->check()) return redirect()->route('tenant.login');
        $request->merge(['tenant' => auth('tenant')->user()]);
        return $next($request);
    }
}
