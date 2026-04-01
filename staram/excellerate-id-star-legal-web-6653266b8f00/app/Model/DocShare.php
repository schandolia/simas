<?php

namespace App\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;



class DocShare extends Model
{
    protected $table = 'doc_share';
    protected $fillable = [
        'folder_id', 'doc_name', 'company_name','doc_type',
        'agreement_date','agreement_number','parties',
        'expire_date','remark','description','attachment','submitter_id'
    ];

    const VIEW = 'doc_share_view';

    public static function getSharedDocView($userId, $folder)
    {
        return DB::table(DocShare::VIEW)->
            where(function($query) use ($userId){
                $query->where(DocShare::VIEW.'.user_id', $userId)->
                orWhere(DocShare::VIEW.'.submitter_id',$userId);
            })->
            where(DocShare::VIEW.'.folder_id', $folder)->
            groupBy(DocShare::VIEW.'.doc_id');
    }

    public static function searchSharedDoc($userId, $phrase)
    {
        return DB::table(DocShare::VIEW)->
            where(function($query) use ($userId){
                $query->where(DocShare::VIEW.'.user_id', $userId)->
                orWhere(DocShare::VIEW.'.submitter_id',$userId);
            })->
            where(DocShare::VIEW.'.doc_name', 'like', '%'.$phrase.'%')->
            groupBy(DocShare::VIEW.'.doc_id');
    }

    public static function getNearExpiredDocument()
    {
        $currentDay = time();
        $now = date('Y-m-d',$currentDay);
        $nowP30 = date('Y-m-d', strtotime('+30 day',$currentDay));

        return DB::table('doc_share_view')->
            select('doc_share_view.doc_id','doc_share_view.user_id','doc_share_view.submitter_id','doc_share_view.doc_name',
                   'doc_share_view.company_name','doc_share_view.expire_date','doc_share_view.parties','doc_share_view.agreement_number',
                   'doc_type.type')->
            whereBetween('doc_share_view.expire_date', [$now, $nowP30])->
            join('doc_type','doc_type.id','=','doc_share_view.doc_type')->
            orderBy('doc_share_view.expire_date', 'ASC');
    }
}
