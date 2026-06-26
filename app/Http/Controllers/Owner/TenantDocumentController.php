<?php
namespace App\Http\Controllers\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Tenant, TenantDocument};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TenantDocumentController extends Controller
{
    public function store(Request $request, Tenant $tenant)
    {
        abort_if($tenant->owner_id !== $request->owner->id, 403);

        $data = $request->validate([
            'doc_type'  => 'required|in:passport,police_certificate,national_id,visa,residence_permit,employment_letter,bank_statement,other',
            'label'     => 'nullable|string|max:150',
            'issued_on' => 'nullable|date',
            'expires_on'=> 'nullable|date',
            'notes'     => 'nullable|string|max:1000',
            'files'     => 'required|array|min:1|max:20',
            'files.*'   => 'file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:10240',
        ]);

        $count = 0;
        foreach ($request->file('files') as $file) {
            // Private storage — sensitive ID documents are NOT publicly accessible.
            $path = $file->store("tenant_documents/{$tenant->id}");

            TenantDocument::create([
                'owner_id'       => $request->owner->id,
                'tenant_id'      => $tenant->id,
                'doc_type'       => $data['doc_type'],
                'label'          => $data['label'] ?? null,
                'file_path'      => $path,
                'original_name'  => $file->getClientOriginalName(),
                'mime_type'      => $file->getClientMimeType(),
                'size_bytes'     => $file->getSize(),
                'issued_on'      => $data['issued_on'] ?? null,
                'expires_on'     => $data['expires_on'] ?? null,
                'uploaded_by'    => 'owner',
                'uploaded_by_id' => $request->owner->id,
                'notes'          => $data['notes'] ?? null,
            ]);
            $count++;
        }

        $msg = $count === 1
            ? 'Document uploaded. It will be kept on file even if the tenant moves out.'
            : "{$count} documents uploaded. They will be kept on file even if the tenant moves out.";

        return back()->with('success', $msg);
    }

    /** Documents overview across all of the owner's tenants. */
    public function index(Request $request)
    {
        $owner = $request->owner;

        $query = TenantDocument::where('owner_id', $owner->id)->with('tenant');

        if ($type = $request->get('doc_type')) {
            $query->where('doc_type', $type);
        }
        if ($request->get('filter') === 'expiring') {
            $query->whereNotNull('expires_on')
                  ->whereDate('expires_on', '>=', now())
                  ->whereDate('expires_on', '<=', now()->addDays(30));
        } elseif ($request->get('filter') === 'expired') {
            $query->whereNotNull('expires_on')->whereDate('expires_on', '<', now());
        }

        $documents = $query->orderByRaw('expires_on IS NULL')->orderBy('expires_on')->latest('id')
            ->paginate(30)->withQueryString();

        $base = TenantDocument::where('owner_id', $owner->id);
        $stats = [
            'total'    => (clone $base)->count(),
            'expired'  => (clone $base)->whereNotNull('expires_on')->whereDate('expires_on', '<', now())->count(),
            'expiring' => (clone $base)->whereNotNull('expires_on')
                            ->whereDate('expires_on', '>=', now())
                            ->whereDate('expires_on', '<=', now()->addDays(30))->count(),
        ];

        return view('owner.documents.index', compact('documents', 'stats'));
    }

    public function download(Request $request, TenantDocument $document): StreamedResponse
    {
        abort_if($document->owner_id !== $request->owner->id, 403);
        abort_unless(Storage::exists($document->file_path), 404);
        return Storage::download($document->file_path, $document->original_name ?: basename($document->file_path));
    }

    public function destroy(Request $request, TenantDocument $document)
    {
        abort_if($document->owner_id !== $request->owner->id, 403);
        if (Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }
        $document->delete();
        return back()->with('success', 'Document removed.');
    }
}
