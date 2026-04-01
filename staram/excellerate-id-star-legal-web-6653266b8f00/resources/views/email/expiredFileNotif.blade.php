<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" crossorigin="anonymous">
        <title>Expired file notification</title>
    </head>
    <body class="pl-2"  style="font-size: 0.875rem;">
        <h3>Hi, {{$username}}</h3>
        <p>Following file will be expired in 30 days.</p>
        <p>
            <table class="table" style="font-size: 0.875rem; max-width:60%">
                <thead>
                    <tr>
                        <th>Agreement Number</th>
                        <th>Document Type</th>
                        <th>Document Name</th>
                        <th>Company Name</th>
                        <th>Parties</th>
                        <th>Expiration Date</th>
                        <th>Download Link</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($docData as $item)
                        <tr>
                            <td>{{$item->agreement_number}}</td>
                            <td>{{$item->type}}</td>
                            <td>{{$item->doc_name}}</td>
                            <td>{{$item->company_name}}</td>
                            <td>{{$item->parties}}</td>
                            <td>{{$item->expire_date}}</td>
                            <td><a href="{{route('sharedFile', $item->doc_id)}}" class="btn btn-primary">Download</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </p>
    </body>
</html>
