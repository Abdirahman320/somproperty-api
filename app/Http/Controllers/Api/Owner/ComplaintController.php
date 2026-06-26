<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Complaint, ComplaintReply};
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $owner = $request->user();
        $query = Complaint::where('owner_id', $owner->id)->with(['tenant','unit.property']);

        if ($request->has('status'))   $query->where('status',   $request->status);
        if ($request->has('priority')) $query->where('priority', $request->priority);

        $complaints = $query->latest()->paginate(20);

        $data = $complaints->getCollection()->map(fn($c) => [
            'id'            => $c->id,
            'ticket_number' => $c->ticket_number,
            'title'         => $c->title,
            'description'   => $c->description,
            'category'      => $c->category,
            'priority'      => $c->priority,
            'status'        => $c->status,
            'assigned_to'   => $c->assigned_to,
            'resolved_at'   => $c->resolved_at,
            'created_at'    => $c->created_at,
            'property_name' => $c->unit?->property?->name ?? '',
            'tenant'  => ['id'=>$c->tenant?->id,'full_name'=>$c->tenant?->full_name ?? ''],
            'unit'    => ['unit_number'=>$c->unit?->unit_number ?? ''],
        ]);

        return response()->json(['data' => $data, 'total' => $complaints->total()]);
    }

    public function update(Request $request, $id)
    {
        $owner     = $request->user();
        $complaint = Complaint::where('owner_id', $owner->id)->findOrFail($id);

        $data = $request->validate([
            'status'           => 'sometimes|in:open,assigned,in_progress,resolved,closed,rejected',
            'assigned_to'      => 'nullable|string|max:100',
            'resolution_notes' => 'nullable|string|max:2000',
        ]);

        if (isset($data['status']) && $data['status'] === 'resolved') {
            $data['resolved_at'] = now();
        }

        $complaint->update($data);
        return response()->json(['message' => 'Complaint updated.']);
    }

    public function reply(Request $request, $id)
    {
        $owner     = $request->user();
        $complaint = Complaint::where('owner_id', $owner->id)->findOrFail($id);

        $request->validate(['message' => 'required|string|max:2000']);

        $reply = ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'sender_type'  => 'owner',
            'sender_id'    => $owner->id,
            'message'      => $request->message,
        ]);

        return response()->json(['message' => 'Reply sent.', 'reply_id' => $reply->id], 201);
    }

    public function show(Request $request, $id)
    {
        $owner     = $request->user();
        $complaint = Complaint::where('owner_id', $owner->id)
            ->with(['tenant','unit','replies'])
            ->findOrFail($id);

        return response()->json(['data' => [
            'id'               => $complaint->id,
            'ticket_number'    => $complaint->ticket_number,
            'title'            => $complaint->title,
            'description'      => $complaint->description,
            'category'         => $complaint->category,
            'priority'         => $complaint->priority,
            'status'           => $complaint->status,
            'assigned_to'      => $complaint->assigned_to,
            'resolution_notes' => $complaint->resolution_notes,
            'resolved_at'      => $complaint->resolved_at,
            'created_at'       => $complaint->created_at,
            'tenant'  => ['full_name'=>$complaint->tenant->full_name,'email'=>$complaint->tenant->email],
            'unit'    => ['unit_number'=>$complaint->unit->unit_number],
            'replies' => $complaint->replies->map(fn($r) => [
                'sender_type' => $r->sender_type,
                'message'     => $r->message,
                'created_at'  => $r->created_at,
            ]),
        ]]);
    }
}
