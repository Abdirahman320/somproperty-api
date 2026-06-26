<?php
namespace App\Http\Controllers\Api\Owner;
use App\Http\Controllers\Controller;
use App\Models\{Property,Unit,Tenant,Contract,TenantDocument,Advertisement,Booking,UtilityReading,TechnicalIssue};
use App\Services\DataBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash,Storage};
class OwnerBackupController extends Controller {
    public function __construct(private DataBackupService $backup){}
    public function export(Request $r){
        $oid=$r->user()->id;$format=$r->validate(['format'=>'required|in:excel,csv,sql'])['format'];$stamp=now()->format('Ymd_His');
        [$content,$filename,$mime]=match($format){'csv'=>[$this->backup->exportCsv('owner',$oid),"som_owner{$oid}_{$stamp}.csv",'text/csv'],'sql'=>[$this->backup->exportSql('owner',$oid),"som_owner{$oid}_{$stamp}.sql",'application/sql'],default=>[$this->backup->exportExcel('owner',$oid),"som_owner{$oid}_{$stamp}.xls",'application/vnd.ms-excel']};
        return response($content,200,['Content-Type'=>$mime,'Content-Disposition'=>'attachment; filename="'.$filename.'"']);
    }
    public function import(Request $r){
        $r->validate(['file'=>'required|file|extensions:csv,txt,sql,xls,xml|max:51200']);$file=$r->file('file');
        try{$report=$this->backup->import($file->getRealPath(),strtolower($file->getClientOriginalExtension()),'owner',$r->user()->id);}catch(\Throwable $e){return response()->json(['success'=>false,'message'=>'Restore failed: '.$e->getMessage()],422);}
        $s=$report['tables']===null?"{$report['rows']} SQL statements executed.":"{$report['rows']} rows imported across {$report['tables']} table(s).";
        return response()->json(['success'=>true,'message'=>$s,'data'=>$report]);
    }
}
