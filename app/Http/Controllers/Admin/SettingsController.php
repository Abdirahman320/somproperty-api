<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller {
    public function index()  { return view('admin.settings'); }
    public function update(Request $request) { return back()->with('success','Settings saved.'); }
}
