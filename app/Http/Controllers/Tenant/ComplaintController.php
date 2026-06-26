<?php
namespace App\Http\Controllers\Tenant;
use App\Http\Controllers\Controller;
use App\Models\{Complaint, ComplaintReply};
use Illuminate\Http\Request;

class ComplaintController extends Controller {
    public function index(Request $request) {
        $tenant     = $request->tenant;
        $complaints = Complaint::where('tenant_id',$tenant->id)->with(['unit','replies'])->latest()->get();
        $unreadCount= $tenant->notifications()->where('is_read',false)->count();
        return view('tenant.complaints.index', compact('tenant','complaints','unreadCount'));
    }
    public function show(Request $request, Complaint $complaint) {
        abort_if($complaint->tenant_id !== $request->tenant->id, 403);
        $complaint->load(['unit','replies']);
        $unreadCount = $request->tenant->notifications()->where('is_read',false)->count();
        return view('tenant.complaints.show', compact('complaint','unreadCount'));
    }
    public function store(Request $request) {
        $tenant = $request->tenant;
        $data   = $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'required|string|max:2000',
            'category'    => 'required|in:plumbing,electrical,structural,noise,cleaning,furniture,security,parking,other',
            'priority'    => 'required|in:low,medium,high,emergency',
        ]);
        $contract = $tenant->activeContract;
        abort_if(!$contract, 422, 'No active contract found.');
        Complaint::create([
            'owner_id'    => $tenant->owner_id,
            'tenant_id'   => $tenant->id,
            'unit_id'     => $contract->unit_id,
            ...$data,
            'status'      => 'open',
        ]);
        return redirect()->route('tenant.complaints.index')->with('success','Complaint submitted. We will respond within 24 hours.');
    }
    public function reply(Request $request, Complaint $complaint) {
        abort_if($complaint->tenant_id !== $request->tenant->id, 403);
        $request->validate(['message'=>'required|string|max:2000']);
        ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'sender_type'  => 'tenant',
            'sender_id'    => $request->tenant->id,
            'message'      => $request->message,
        ]);
        return back()->with('success','Reply sent.');
    }
}
