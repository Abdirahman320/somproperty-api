<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTokenRole
{
    /**
     * Enforce that the Sanctum token belongs to the expected model type.
     * Usage: EnsureTokenRole::class.':owner' / ':tenant' / ':admin'
     */
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $allowed = match ($role) {
            'owner'  => \App\Models\Owner::class,
            'tenant' => \App\Models\Tenant::class,
            'agent'  => \App\Models\PropertyAgent::class,
            default  => null,
        };

        if (!$allowed || !($user instanceof $allowed)) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return $next($request);
    }
}
