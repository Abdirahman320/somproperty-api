<?php
namespace App\Http\Controllers\Agent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller {
    public function index(Request $request) {
        return view('agent.profile', ['agent' => $request->agent]);
    }

    public function update(Request $request) {
        $agent = $request->agent;
        $data = $request->validate([
            'full_name'    => 'required|string|max:120',
            'company_name' => 'nullable|string|max:150',
            'phone'        => 'nullable|string|max:40',
            'city'         => 'nullable|string|max:100',
            'country'      => 'nullable|string|max:100',
        ]);
        $agent->update($data);
        return back()->with('success', 'Profile updated.');
    }

    public function changePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        $agent = auth('agent')->user()->fresh();

        if (!Hash::check($request->current_password, $agent->password_hash)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $agent->update(['password_hash' => Hash::make($request->new_password)]);
        return back()->with('success', 'Password changed successfully.');
    }
}
