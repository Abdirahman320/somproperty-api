<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Complaint, ComplaintReply};
use Illuminate\Http\Request;

class ComplaintController extends Controller {
    public function index(Request $request) {
        $owner = $request->owner;
        $complaints = Complaint::where('owner_id',$owner->id)
            ->with(['tenant','unit'])
            ->latest()->paginate(20);
        return view('owner.complaints.index', compact('complaints'));
    }

    public function show(Request $request, Complaint $complaint) {
        abort_if($complaint->owner_id !== $request->owner->id, 403);
        $complaint->load(['tenant','unit','replies']);
        return view('owner.complaints.show', compact('complaint'));
    }

    public function updateStatus(Request $request, Complaint $complaint) {
        abort_if($complaint->owner_id !== $request->owner->id, 403);
        $data = $request->validate(['status'=>'required|in:open,assigned,in_progress,resolved,closed,rejected','assigned_to'=>'nullable|string|max:100','resolution_notes'=>'nullable|string']);
        if ($data['status'] === 'resolved') $data['resolved_at'] = now();
        $complaint->update($data);
        return back()->with('success','Complaint status updated.');
    }

    public function reply(Request $request, Complaint $complaint) {
        abort_if($complaint->owner_id !== $request->owner->id, 403);
        $request->validate(['message'=>'required|string|max:2000']);
        ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'sender_type'  => 'owner',
            'sender_id'    => $request->owner->id,
            'message'      => $request->message,
        ]);
        return back()->with('success','Reply sent to tenant.');
    }
}
