@extends('layout.app')

@section('content')
<main class="main">
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="icon-cursor icon-gradient bg-ripe-malin"></i>
                </div>
                <div>Shared Folder</div>
            </div>
            <iframe id="file-downlod-frame" style="display: none"></iframe>
            <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('share')}}">Root</a></li>
                    @for ($i=count($folderPaths)-1;$i>=0;$i--)
                        <li class="breadcrumb-item"><a href="{{route('share').'/'.$folderPaths[$i]->id}}">{{$folderPaths[$i]->folder_name}}</a></li>
                    @endfor
            </ol>
            <!-- /.card-->
            <div class="row pt-1">
                <div class="col">
                    <table id="share-table" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th><input id="dt-row-selector" type="checkbox" class="_check" name="check"></th>
                                <th>Document Name</th>
                                <th>Date</th>
                                <th>Company Name</th>
                                <th>Document Type</th>
                                <th>Agreement Number</th>
                                <th>Date Agreement</th>
                                <th>Parties</th>
                                <th>Date Expired</th>
                                <th>Description</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection
@section ('modal')
<!-- Modal Add Folder-->
<div class="modal fade" id="addFolderModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Folder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frmAddFolder" name="frmAddFolder" class="form-horizontal" action="{{route('addFolder')}}" method="POST">
            <div class="modal-body">
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label" for="email-input">Folder Name</label>
                      <div class="col">
                        <input class="form-control" id="folder-name" type="text" name="folder-name" placeholder="Folder Name">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-md-3 col-form-label" for="password-input">Description</label>
                      <div class="col">
                        <div id="folder-description"></div>
                      </div>
                      <input type="hidden" id="folder-parent" name="folder-parent" value="{{$currentFolder}}">
                      @csrf
                    </div>
                    <br/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="add-button-folder"><i class="fa fa-folder pr-2"></i>Create Folder</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Add File-->
<div class="modal fade" id="addFileModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frmAddFile" class="form-horizontal" action="{{route('addFile')}}" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Company Name</label>
                        <div class="col">
                        <input class="form-control" id="company-name" type="text" name="company-name" placeholder="Company Name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Document Type</label>
                        <div class="col">
                            <select class="form-control" id="document-type" name="document-type">
                                <option value="0">Document Type</option>
                            @foreach ($doctypes as $doctype)
                                <option value="{{$doctype->id}}">{{$doctype->type}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Agreement Date</label>
                        <div class="col">
                            <input class="form-control" id="agreement-date" type="date" name="agreement-date" placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Parties</label>
                        <div class="col">
                        <input class="form-control" id="parties" type="text" name="parties" placeholder="Parties">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Agreement Number</label>
                        <div class="col">
                        <input class="form-control" id="agreement-number" type="text" name="agreement-number" placeholder="Agreement Number">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Expire Date</label>
                        <div class="col">
                        <input class="form-control" id="date" type="date" name="expire-date" placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Description</label>
                        <div class="col">
                            <div id="newfile-description"></div>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group row pt-3">
                        <label class="col-md-3 col-form-label">Remarks</label>
                        <div class="col">
                            <div id="newfile-remarks"></div>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group row pt-3 pt-3 pl-3 pr-3">
                        <input class="form-control" id="file-attachment" type="file" name="file-attachment" placeholder="Attachment">
                    </div>
                    <input type="hidden" id="folder-parent" name="folder-parent" value="{{$currentFolder}}">
                    @csrf
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-file pr-2"></i>Add File</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="shareModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share File/Folder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frmShareFile" class="form-horizontal" action="{{route('doDocShare')}}" method="post">
            <div class="modal-body">
                    <div class="form-group row">
                        <div class="col">
                            <input class="form-control" id="username-mgcSuggest" type="text" name="username-mgcSuggest">
                        </div>
                    </div>
                    @csrf
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-share-alt pr-2"></i>Share</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="deleteModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-warning" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete File/Folder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frmDeleteFile" class="form-horizontal" action="{{route('doDocDelete')}}" method="post">
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col col-form-label">You are about to delete shared file/folder. Are you sure to delete selected folder/files?</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-warning"><i class="fa fa-remove pr-2"></i>Delete</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="downloadModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Download Shared Files</h5>
                {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> --}}
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col col-form-label">Server is preparing files to be downloaded.</label>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="fileDetailModal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-primary" role="document" style="max-width: 90%;">
        <div class="modal-content" style="border: 0;border-radius: 0">
            <div class="modal-body" style="min-height: 90vh;padding:0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <iframe id="sharedFolderPreview"
                    width="100%" style="border: 0px;height:85vh"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection
