<?php
namespace App\Http\Controllers\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller {
    public function index(Request $request) {
        $tenant   = $request->tenant;
        $contract = $tenant->activeContract?->load('unit.property');
        $unreadCount = $tenant->notifications()->where('is_read',false)->count();
        return view('tenant.home', compact('tenant','contract','unreadCount'));
    }
    public function documents(Request $request) {
        $tenant = $request->tenant;
        $contract = $tenant->activeContract()->with('unit.property')->first();
        $unreadCount = $tenant->notifications()->where('is_read',false)->count();
        return view('tenant.documents', compact('tenant','contract','unreadCount'));
    }
}
