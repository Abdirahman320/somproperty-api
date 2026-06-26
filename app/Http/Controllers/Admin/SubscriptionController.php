<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Owner, Plan};

class SubscriptionController extends Controller {
    public function index() {
        $plans  = Plan::withCount(['owners as active' => fn($q) => $q->where('status','active')])->get();
        $mrr    = Owner::where('status','active')->join('plans','plans.id','=','owners.plan_id')->sum('plans.price_monthly');
        $owners = Owner::with('plan')->latest()->paginate(30);
        return view('admin.subscriptions', compact('plans','mrr','owners'));
    }
}
