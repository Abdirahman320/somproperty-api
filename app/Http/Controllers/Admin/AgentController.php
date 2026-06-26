<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\PropertyAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentController extends Controller {
    public function index() {
        $agents = PropertyAgent::latest()->paginate(25);
        return view('admin.agents.index', compact('agents'));
    }

    public function create() {
        return view('admin.agents.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'full_name'          => 'required|string|max:120',
            'company_name'       => 'nullable|string|max:150',
            'email'              => 'required|email|unique:property_agents,email',
            'phone'              => 'nullable|string|max:40',
            'city'               => 'nullable|string|max:100',
            'country'            => 'nullable|string|max:100',
            'subscription_plan'  => 'required|in:basic,pro',
            'subscription_price' => 'required|numeric|min:0',
            'subscription_ends_at' => 'required|date',
        ]);

        $password = Str::random(10);
        $agent = PropertyAgent::create([
            ...$data,
            'password_hash'          => Hash::make($password),
            'subscription_starts_at' => now()->toDateString(),
            'status'                 => 'active',
            'created_by_admin'       => auth('admin')->id(),
        ]);

        return redirect()->route('admin.agents.index')
            ->with('success', "Agent created successfully.")
            ->with('new_creds', ['name' => $agent->full_name, 'email' => $agent->email, 'password' => $password]);
    }

    public function suspend(PropertyAgent $agent) {
        $agent->update(['status' => 'suspended']);
        return back()->with('success', "Agent {$agent->full_name} suspended.");
    }

    public function activate(PropertyAgent $agent) {
        $agent->update(['status' => 'active']);
        return back()->with('success', "Agent {$agent->full_name} activated.");
    }

    public function destroy(PropertyAgent $agent) {
        $agent->delete();
        return back()->with('success', 'Agent deleted.');
    }
}
