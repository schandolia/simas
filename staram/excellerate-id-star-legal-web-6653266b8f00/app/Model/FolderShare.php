<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;



class FolderShare extends Model
{
    protected $table = 'folder_share';
    protected $fillable = [
        'folder_name', 'description', 'folder_id','creator_id'
    ];
    const VIEW = 'folder_share_view';
    const MAX_FOLDER_DEPTHS = 100;
    public static function getSharedFolderView($userId, $folder)
    {
        return DB::table(FolderShare::VIEW)->
            where(FolderShare::VIEW.'.folder_id', $folder)->
            //jagoan.neon no Folder sharing
            // where(function($query) use ($userId){
            //     $query->where(FolderShare::VIEW.'.user_id', $userId)->
            //     orWhere(FolderShare::VIEW.'.submitter_id',$userId);
            // })->
            groupBy(FolderShare::VIEW.'.doc_id');
    }

    public static function searchSharedFolder($userId, $phrase)
    {
        return DB::table(FolderShare::VIEW)->
            where(FolderShare::VIEW.'.doc_name', 'like', '%'.$phrase.'%')->
            //jagoan.neon no Folder sharing
            // where(function($query) use ($userId){
            //     $query->where(FolderShare::VIEW.'.user_id', $userId)->
            //     orWhere(FolderShare::VIEW.'.submitter_id',$userId);
            // })->
            groupBy(FolderShare::VIEW.'.doc_id');

    }

    public static function getFolderPath($folder)
    {
        $paths = array();
        $folderData = (object) array("id"=>$folder,'folder_id'=>$folder);
        $counter = 0;
        while($folderData->folder_id != null)
        {
            $folders = FolderShare::select('id','folder_id','folder_name')->where('id','=',$folderData->folder_id)->get();
            if(sizeof($folders)==0)
                break;
            $folderData = $folders[0];

            // array_push( $paths, ((object)["id"=>$folderData->id,"parent_id"=>$folderData->folder_id, "name"=>$folderData->folder_name]));
            array_push( $paths, $folderData);
            if($counter>=FolderShare::MAX_FOLDER_DEPTHS)
                break;
            $counter++;
            // dd(json_encode($folderData));
        }
        return $paths;
    }
}
