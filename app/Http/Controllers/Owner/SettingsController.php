<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SettingsController extends Controller {
    public function index(Request $request) {
        $owner = $request->owner;
        return view('owner.settings.index', compact('owner'));
    }
    public function update(Request $request) {
        $owner = $request->owner;
        $data  = $request->validate([
            'company_name' => 'nullable|string|max:150',
            'phone'        => 'nullable|string|max:30',
            'timezone'     => 'nullable|string|max:50',
            'smtp_host'    => 'nullable|string|max:150',
            'smtp_port'    => 'nullable|integer',
            'smtp_user'    => 'nullable|email|max:150',
            'smtp_pass'    => 'nullable|string|max:255',
        ]);
        if (!empty($data['smtp_pass'])) {
            $data['smtp_pass_encrypted'] = Crypt::encryptString($data['smtp_pass']);
            $data['gmail_configured']    = true;
        }
        unset($data['smtp_pass']);
        $owner->update($data);
        return back()->with('success', 'Settings saved.');
    }
}
