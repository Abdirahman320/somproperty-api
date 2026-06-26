<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller {
    public function index() {
        return view('admin.plans.index', ['plans' => Plan::withCount('owners')->orderBy('price_monthly')->get()]);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name'           => 'required|string|max:50',
            'slug'           => 'nullable|string|max:50',
            'price_monthly'  => 'required|numeric|min:0',
            'max_apartments' => 'required|integer|min:1',
        ]);
        $slug = Str::slug($data['slug'] ?: $data['name']);
        if (Plan::where('slug', $slug)->exists()) {
            return back()->withInput()->with('error', "A plan with the slug \"{$slug}\" already exists.");
        }
        Plan::create([
            'name'           => $data['name'],
            'slug'           => $slug,
            'price_monthly'  => $data['price_monthly'],
            'max_apartments' => $data['max_apartments'],
            'is_active'      => true,
            'features'       => ['tenant_portal','rent_billing','email_notifications','complaint_tracking','advanced_reports','water_electric_billing','contract_management','pdf_exports'],
        ]);
        return redirect()->route('admin.plans.index')->with('success', "Plan \"{$data['name']}\" created.");
    }

    public function update(Request $request, Plan $plan) {
        $data = $request->validate([
            'name'           => 'required|string|max:50',
            'price_monthly'  => 'required|numeric|min:0',
            'max_apartments' => 'required|integer|min:1',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $plan->update($data);
        return back()->with('success', "Plan \"{$plan->name}\" updated.");
    }
}
