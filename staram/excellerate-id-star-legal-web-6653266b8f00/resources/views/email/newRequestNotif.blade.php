<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" crossorigin="anonymous">
        <title>New Request Notification</title>
    </head>
    <body class="pl-2"  style="font-size: 0.875rem;">
        <h3>Hi, {{$username}}</h3>
        <p>New request has been submitted and need to be reviewed.</p>
        <p>
            <table class="table" style="font-size: 0.875rem; max-width:60%">
                
                <tbody>
                    <tr>
                        @if($docData->approval_type=='REQUEST')
                        <th>REQUEST : {{$docData->id}}</th>
                        @else
                        <th>REVIEW : {{$docData->id}}</th>
                        @endif
                    </tr>
                    <tr>
                        <th>Document Type</th>
                        <td>{{App\Model\DocType::find($docData->doc_type)->type}}</td>
                    </tr>
                    <tr>
                        <th>Purpose/Nature of Agreement</th>
                        <td>{{$docData->purpose}}</td>
                    </tr>
                    <tr>
                        <th>The Parties</th>
                        <td>{{$docData->parties}}</td>
                    </tr>
                    <tr>
                        <th>Description/Notes</th>
                        <td>{!! $docData->description !!}</td>
                    </tr>
                    <tr>
                        @if($docData->approval_type=='REQUEST')
                        <td colspan="2"><a href="{{route('request')}}" class="btn btn-primary">Detail</a></td>
                        @else
                        <td colspan="2"><a href="{{route('review')}}" class="btn btn-primary">Detail</a></td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </p>
    </body>
</html>
