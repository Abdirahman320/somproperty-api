<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;

class AuthOwner {
    public function handle(Request $request, Closure $next) {
        if (!auth('owner')->check()) return redirect()->route('owner.login');
        $owner = auth('owner')->user();
        $request->merge(['owner' => $owner]);
        return $next($request);
    }
}
