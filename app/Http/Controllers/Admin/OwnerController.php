<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Owner, Plan};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OwnerController extends Controller {
    public function index() {
        $owners = Owner::with('plan')->latest()->paginate(20);
        $plans  = Plan::where('is_active',true)->get();
        return view('admin.owners.index', compact('owners','plans'));
    }

    public function create() {
        $plans = Plan::where('is_active',true)->get();
        return view('admin.owners.create', compact('plans'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'full_name'       => 'required|string|max:100',
            'company_name'    => 'nullable|string|max:150',
            'email'           => 'required|email|unique:owners,email',
            'phone'           => 'nullable|string|max:30',
            'plan_id'         => 'required|exists:plans,id',
            'max_apartments'  => 'required|integer|min:1',
        ]);
        $password = Str::random(10);
        $owner = Owner::create([
            ...$data,
            'password_hash'  => Hash::make($password),
            'status'         => 'trial',
            'trial_ends_at'  => now()->addDays(14),
            'created_by'     => auth('admin')->id(),
        ]);

        // Attempt to send welcome email but don't crash if mail not configured
        try {
            \Mail::to($owner->email)->send(new \App\Mail\OwnerWelcomeMail($owner, $password));
            $msg = "Owner created & welcome email sent to {$owner->email}.";
        } catch (\Exception $e) {
            $msg = "Owner created. Welcome email could not be sent — configure Gmail SMTP in Settings.";
        }

        return redirect()->route('admin.owners.index')
            ->with('success', $msg)
            ->with('new_creds', ['name' => $owner->full_name, 'email' => $owner->email, 'password' => $password]);
    }

    public function suspend(Owner $owner) {
        $owner->update(['status'=>'suspended']);
        return back()->with('success',"Owner {$owner->full_name} suspended.");
    }

    public function activate(Owner $owner) {
        $owner->update(['status'=>'active']);
        return back()->with('success',"Owner {$owner->full_name} activated.");
    }

    public function destroy(Owner $owner) {
        $owner->delete();
        return redirect()->route('admin.owners.index')->with('success','Owner deleted.');
    }
}
