<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;

class PlanLimit {
    public function handle(Request $request, Closure $next) {
        $owner = $request->owner;
        if ($owner && $owner->isAtPlanLimit()) {
            $next = $this->getNextPlan($owner->plan->slug);
            if ($request->expectsJson()) {
                return response()->json([
                    'error'     => "Plan limit reached: {$owner->usedApartments()}/{$owner->max_apartments} apartments.",
                    'next_plan' => $next,
                    'upgrade'   => url('/owner/billing/upgrade'),
                ], 403);
            }
            return redirect()->back()->with('error',
                "You have reached your plan limit of {$owner->max_apartments} apartments. Please upgrade.");
        }
        return $next($request);
    }
    private function getNextPlan(string $slug): array {
        return [
            'pro'     => ['name'=>'Premium','price'=>30,'limit'=>27],
            'premium' => ['name'=>'Maxi',   'price'=>50,'limit'=>49],
            'maxi'    => ['name'=>'Maxi-2', 'price'=>100,'limit'=>99],
            'maxi2'   => ['name'=>'Maxi-3', 'price'=>150,'limit'=>149],
            'maxi3'   => ['name'=>'Contact sales','price'=>null,'limit'=>null],
        ][$slug] ?? [];
    }
}
