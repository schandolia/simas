<?php

namespace App\Http\Controllers;

use App\Model\DocShare;
use App\Model\DocType;
use App\Model\DocUser;
use App\Model\FolderShare;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SharedFileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($folder=null)
    {
	$userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        if($role=='LEGAL' || $role=='APPROVER'||$role=='ADMIN')
        {
            return view('docShare')->with('userInfo', Auth::user())->
                with('folderPaths',FolderShare::getFolderPath($folder))->
                with('currentFolder',$folder)->
                with('doctypes',DocType::select('id', 'type')->get())->
                with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                with('notif', RequestDocController::_getNotification());
        }
        else
            return abort(404);
    }

    public function addFolder(Request $request)
    {
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        if($role=='LEGAL' || $role=='APPROVER' || $role=='ADMIN')
        {
            $request->validate([
                'folder-name'=>'required|max:256'
            ]);

            FolderShare::create(array(
                'folder_name'=> $request->input('folder-name'),
                'folder_id'=>$request->input('folder-parent'),
                'creator_id'=>Auth::user()->id,
                'description'=>$request->input('description')
            ));
            return response()->json(['success'=>'done']);
        }
        else
            return abort(404);

    }

    public function addFile(Request $request)
    {
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        if($role=='LEGAL' || $role=='APPROVER' || $role=='ADMIN')
        {
            $request->validate([
                'company-name'=>'required|max:1024',
                'document-type'=>'required|numeric|min:0|not_in:0|exists:doc_type,id',
                'agreement-date'=>'required',
                'agreement-number'=>'required|max:1024',
                'expire-date'=>'required',
                'file-attachment'=>'required'
            ]);
	    
	    // upload the file
            $fileName=$request->file('file-attachment')->store('shared-folder');

            $docName = $request->input('file-attachment');
            $docName = substr($docName,strrpos($docName,'\\')+1);
            DocShare::create(array(
                "folder_id"=>$request->input('folder-parent'),
                "doc_name"=>$docName,
                "company_name"=>$request->input('company-name'),
                "doc_type"=>$request->input('document-type'),
                "agreement_date"=>$request->input('agreement-date'),
                "agreement_number"=>$request->input('agreement-number'),
                "parties"=>$request->input('parties'),
                "expire_date"=>$request->input('expire-date'),
                "remark"=>$request->input('remark'),
                "description"=>$request->input('description'),
                "attachment"=>'storage/'.$fileName,
                "submitter_id"=>Auth::user()->id
            ));
            return response()->json(['success'=>'done']);
        }
        else
            return abort(404);
    }

    public function doDocShare(Request $request)
    {
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        if($role=='LEGAL' || $role=='APPROVER' || $role=='ADMIN')
        {
            $request->validate(array(
                "user-ids"=>"required",
                "doc-ids"=>"required"
            ));
            $userIds =  json_decode($request->input("user-ids"));
            $docIds = json_decode($request->input("doc-ids"));
            SharedFileController::assignDocShare($userIds, $docIds);
            return response()->json(['success'=>'done']);
        }
        else
            abort(404);
    }

    public function doDocDelete(Request $request)
    {
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        if($role=='LEGAL' || $role=='APPROVER' || $role=='ADMIN')
        {
            $request->validate(array(
                "doc-ids"=>"required"
            ));
            $docIds = json_decode($request->input("doc-ids"));
            SharedFileController::deleteDocShare($docIds);
            return response()->json(['success'=>'done']);
        }
        else
            abort(404);
    }

    public function sharedFile($fileId)
    {
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        if($role=='LEGAL' || $role=='APPROVER' || $role=='ADMIN')
        {
            $doc = DocShare::where('id',$fileId)->first();
            if(DocShare::where('id', $fileId)->where('submitter_id',$userInfo->id)->exists() ||
                DocUser::where('user_id', $userInfo->id)->where('doc_id', $fileId)->exists())
            {
                return response()->download($doc->attachment, $doc->doc_name);
            }
            else
                abort(404);
        }
        else
            abort(404);
    }

    static function assignDocShare($userIds, $docIds)
    {
        foreach($docIds as $docId)
        {
            if($docId->type=='FOLDER')
            {
                $docs = DocShare::select('id', DB::raw("'DOC' as type"))->
                    where('folder_id', $docId->id)->
                    where('submitter_id', Auth::user()->id);
                $docs = DocUser::select('doc_user.doc_id as id', DB::raw("'DOC' as type"))->
                    where('doc_user.user_id', Auth::user()->id)->
                    where('doc_share.folder_id', $docId->id)->
                    join('doc_share','doc_share.id', '=', 'doc_user.doc_id')
                    ->union($docs);

                SharedFileController::assignDocShare($userIds, $docs->get());
                $folders = FolderShare::select('id', DB::raw("'FOLDER' as type"))->where('folder_id', $docId->id)->get();
                SharedFileController::assignDocShare($userIds, $folders);
            }
            else
            {
                foreach($userIds as $userId)
                {
                    if(DocUser::where('user_id',$userId)->
                        where('doc_id', $docId->id)->exists()==false)
                    {
                        DocUser::create(array(
                            "doc_id"=>$docId->id,
                            "user_id"=>$userId
                        ));
                    }
                }
            }
        }
    }

    static function deleteDocShare($docIds)
    {
        foreach($docIds as $docId)
        {
            if($docId->type=='FOLDER')
            {
                $docs = DocShare::select('id', DB::raw("'DOC' as type"))->
                    where('folder_id', $docId->id)->
                    where('submitter_id', Auth::user()->id);
                $docs = DocUser::select('doc_user.doc_id as id', DB::raw("'DOC' as type"))->
                    where('doc_user.user_id', Auth::user()->id)->
                    where('doc_share.folder_id', $docId->id)->
                    join('doc_share','doc_share.id', '=', 'doc_user.doc_id')
                    ->union($docs);

                SharedFileController::deleteDocShare($docs->get());
                $folders = FolderShare::select('id', DB::raw("'FOLDER' as type"))->where('folder_id', $docId->id)->get();
                SharedFileController::deleteDocShare($folders);

                if(DocShare::where('folder_id',$docId->id)->exists()==false &&
                    FolderShare::where('folder_id',$docId->id)->exists()==false)
                {
                    FolderShare::where('id',$docId->id)->delete();
                }
            }
            else
            {
                try
                {
                    //TODO delete from doc user
                    $docUser = DocUser::where('doc_id',$docId->id)->delete();
                    //delete the file
                    $file = DocShare::where('id',$docId->id);
                    Storage::delete($oldAvatar=str_replace('storage/','', $file->first()->attachment));
                    $file->delete();
                }
                catch(Exception $e)
                {
                    abort(500);
                }
            }
        }
    }


    public function _getSharedFolders($folderId=null){
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        if($role=='LEGAL' || $role=='APPROVER' ||$role=='ADMIN')
        {
            return response()->json(FolderShare::getSharedFolderView(Auth::user()->id, $folderId)->get());
        }
        else
            return abort(404);
    }

    public function _getSharedDocs($folderId=null){
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        if($role=='LEGAL' || $role=='APPROVER' || $role=='ADMIN')
        {
            return response()->json(DocShare::getSharedDocView(Auth::user()->id, $folderId)->get());
        }
        else
            return abort(404);
    }

    public function _getSharedFoldersDocs($folderId=null, $phrase=null)
    {
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        if($role=='LEGAL' || $role=='APPROVER' || $role=='ADMIN')
        {
            if($folderId=='q')
            {
                if($phrase==null)
                    abort(404);
                $phrase = str_replace('+','%',$phrase);

                return response()->json(FolderShare::searchSharedFolder(Auth::user()->id, $phrase)->
                    unionAll(DocShare::searchSharedDoc(Auth::user()->id, $phrase))->
                    orderBy(DB::raw("CASE
                        WHEN doc_name LIKE '".$phrase."' THEN 1
                        WHEN doc_name LIKE '".$phrase."%' THEN 2
                        WHEN doc_name LIKE '%".$phrase."' THEN 4
                        ELSE 3
                        END"))->
                    get());
            }
            else
                return response()->json(FolderShare::getSharedFolderView(Auth::user()->id, $folderId)->
                    unionAll(DocShare::getSharedDocView(Auth::user()->id, $folderId))->
                    orderBy('doc_name','asc')->
                    get());
        }
        else
            return abort(404);
    }

    public function _getDocPath($folder=null)
    {
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();

        if($role=='LEGAL' || $role=='APPROVER' || $role=='ADMIN')
        {
            return response()->json(FolderShare::getFolderPath($folder));
        }
        else
            return abort(404);
    }

    public function _getDocType()
    {
        return response()->json(DocType::select('id', 'type', 'sla_min','sla_max')->get());
    }

    public function _getDownloadLinks(Request $request)
    {
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        if($role=='LEGAL' || $role=='APPROVER' || $role=='ADMIN')
        {
            $request->validate(array(
                "doc-ids"=>"required",
            ));

            $docIds = json_decode($request->input("doc-ids"));

            $links = SharedFileController::_getFileLink($docIds, array());
            return response()->json(['success'=>'done','links' => $links]);
        }
        else
            abort(404);
    }

    static function _getFileLink($docIds, $links)
    {
        foreach($docIds as $docId)
        {
            if($docId->type=='FOLDER')
            {
                $docs = DocShare::select('id', DB::raw("'DOC' as type"), DB::raw("NULL as doc_name"))->
                    where('folder_id', $docId->id)->
                    where('submitter_id', Auth::user()->id);
                $docs = DocUser::select('doc_user.doc_id as id', DB::raw("'DOC' as type"), 'doc_share.doc_name')->
                    where('doc_user.user_id', Auth::user()->id)->
                    where('doc_share.folder_id', $docId->id)->
                    join('doc_share','doc_share.id', '=', 'doc_user.doc_id')
                    ->union($docs);

                $links = SharedFileController::_getFileLink($docs->get(), $links);

                $folders = FolderShare::select('id', DB::raw("'FOLDER' as type"))->where('folder_id', $docId->id)->get();
                $links = SharedFileController::_getFileLink($folders, $links);
            }
            else
            {
                if(!isset($docId->doc_name))
                    array_push($links, array('name'=>DocShare::select('doc_name')->
                            where('id',$docId->id)->first()->doc_name,
                        'link'=>route('sharedFile',[$docId->id])
                    ));
                else
                    array_push($links, array('name'=>$docId->doc_name,
                        'link'=>route('sharedFile',[$docId->id])
                    ));
            }
        }
        return $links;
    }
}
