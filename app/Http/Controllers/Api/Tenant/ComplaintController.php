<?php
namespace App\Http\Controllers\Api\Tenant;
use App\Http\Controllers\Controller;
use App\Models\{Complaint, ComplaintReply};
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $tenant     = $request->user();
        $complaints = Complaint::where('tenant_id', $tenant->id)
            ->with(['unit','replies'])
            ->latest()->get()
            ->map(fn($c) => [
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
                'unit_number'   => $c->unit->unit_number,
                'replies'       => $c->replies->map(fn($r) => [
                    'sender_type' => $r->sender_type,
                    'message'     => $r->message,
                    'created_at'  => $r->created_at,
                ]),
            ]);

        return response()->json(['data' => $complaints]);
    }

    public function store(Request $request)
    {
        $tenant   = $request->user();
        $contract = $tenant->activeContract;

        if (!$contract) return response()->json(['message' => 'No active contract.'], 422);

        $data = $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'required|string|max:2000',
            'category'    => 'required|in:plumbing,electrical,structural,noise,cleaning,furniture,security,parking,other',
            'priority'    => 'required|in:low,medium,high,emergency',
        ]);

        $complaint = Complaint::create([
            'owner_id'    => $tenant->owner_id,
            'tenant_id'   => $tenant->id,
            'unit_id'     => $contract->unit_id,
            'status'      => 'open',
            ...$data,
        ]);

        return response()->json(['message' => 'Complaint submitted. We will respond within 24 hours.', 'ticket_number' => $complaint->ticket_number, 'id' => $complaint->id], 201);
    }

    public function reply(Request $request, $id)
    {
        $tenant    = $request->user();
        $complaint = Complaint::where('tenant_id', $tenant->id)->findOrFail($id);
        $request->validate(['message' => 'required|string|max:2000']);

        ComplaintReply::create([
            'complaint_id' => $complaint->id,
            'sender_type'  => 'tenant',
            'sender_id'    => $tenant->id,
            'message'      => $request->message,
        ]);

        return response()->json(['message' => 'Reply sent.'], 201);
    }
}
