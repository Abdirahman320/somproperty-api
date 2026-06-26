<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Asset, TechnicalIssue};
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $owner  = $request->user();
        $assets = Asset::where('owner_id', $owner->id)
            ->with('property')
            ->latest()->get()
            ->map(fn($a) => [
                'id'             => $a->id,
                'name'           => $a->name,
                'category'       => $a->category,
                'brand'          => $a->brand,
                'model'          => $a->model,
                'serial_number'  => $a->serial_number,
                'location'       => $a->location,
                'status'         => $a->status,
                'purchase_value' => $a->purchase_value,
                'purchase_date'  => $a->purchase_date,
                'warranty_expires_at' => $a->warranty_expires_at,
                'last_maintenance_at' => $a->last_maintenance_at,
                'next_maintenance_at' => $a->next_maintenance_at,
                'property_name'  => $a->property->name,
            ]);

        $issues = TechnicalIssue::where('owner_id', $owner->id)
            ->whereNotIn('status',['closed'])
            ->latest()->take(20)->get()
            ->map(fn($i) => [
                'id'          => $i->id,
                'title'       => $i->title,
                'description' => $i->description,
                'priority'    => $i->priority,
                'status'      => $i->status,
                'assigned_to' => $i->assigned_to,
                'estimated_cost' => $i->estimated_cost,
                'actual_cost'    => $i->actual_cost,
                'scheduled_date' => $i->scheduled_date,
                'created_at'     => $i->created_at,
            ]);

        return response()->json(['assets' => $assets, 'issues' => $issues]);
    }

    public function storeAsset(Request $request)
    {
        $owner = $request->user();
        $data  = $request->validate([
            'property_id'    => 'required|integer|exists:properties,id',
            'name'           => 'required|string|max:150',
            'category'       => 'required|in:mechanical,electrical,plumbing,electronic,furniture,vehicle,other',
            'brand'          => 'nullable|string|max:100',
            'model'          => 'nullable|string|max:100',
            'serial_number'  => 'nullable|string|max:100',
            'location'       => 'nullable|string|max:200',
            'purchase_value' => 'nullable|numeric',
            'purchase_date'  => 'nullable|date',
            'warranty_expires_at' => 'nullable|date',
        ]);
        $asset = Asset::create(['owner_id'=>$owner->id, ...$data]);
        return response()->json(['message'=>'Asset registered.','id'=>$asset->id], 201);
    }

    public function storeIssue(Request $request)
    {
        $owner = $request->user();
        $data  = $request->validate([
            'property_id'  => 'required|integer|exists:properties,id',
            'unit_id'      => 'nullable|integer|exists:units,id',
            'asset_id'     => 'nullable|integer|exists:assets,id',
            'title'        => 'required|string|max:200',
            'description'  => 'required|string',
            'priority'     => 'required|in:low,medium,high,critical',
            'assigned_to'  => 'nullable|string|max:100',
            'scheduled_date'=> 'nullable|date',
            'estimated_cost'=> 'nullable|numeric',
        ]);
        $issue = TechnicalIssue::create(['owner_id'=>$owner->id,'reported_by'=>'owner','reporter_id'=>$owner->id, ...$data]);
        return response()->json(['message'=>'Issue logged.','id'=>$issue->id], 201);
    }
}
