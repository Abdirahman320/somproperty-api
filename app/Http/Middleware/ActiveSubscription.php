<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;

class ActiveSubscription {
    public function handle(Request $request, Closure $next) {
        $owner = auth('owner')->user();
        if ($owner && $owner->status === 'suspended') {
            auth('owner')->logout();
            return redirect()->route('owner.login')
                ->with('error','Your account has been suspended. Please contact support.');
        }
        return $next($request);
    }
}
