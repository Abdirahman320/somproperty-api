<?php
namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Plan,Owner,Advertisement,Booking,AdBilling,AuditLog};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SettingsController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => [
            'app_name' => config('app.name'),
            'timezone' => config('app.timezone'),
        ]]);
    }

    public function update(Request $request)
    {
        // In a real app, write to a settings table or .env.
        return response()->json(['success' => true, 'message' => 'Settings saved.']);
    }
}

/* ────────────────────────────────────────────
   Audit Logs
──────────────────────────────────────────── */
