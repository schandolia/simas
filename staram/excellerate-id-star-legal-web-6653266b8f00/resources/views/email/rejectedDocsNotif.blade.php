<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" crossorigin="anonymous">
        <title>Request rejected notification</title>
    </head>
    <body class="pl-2"  style="font-size: 0.875rem;">
        <h3>Hi, {{$username}}</h3>
        <p>Following Request is rejected.</p>
        <p>
            <table class="table" style="font-size: 0.875rem; max-width:60%">
                <tbody>
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
                            <td>{!!$docData->description!!}</td>
                        </tr>
                        <tr>
                            <th>Transaction/Commercial Terms</th>
                            <td>{{$docData->commercial_terms}}</td>
                        </tr>
                        <tr>
                            <th>Value of Transaction</th>
                            <td>{{$docData->transaction_value}}</td>
                        </tr>
                        <tr>
                            <th>Toleration of Late Payment</th>
                            <td>{{$docData->late_payment_toleration}}</td>
                        </tr>
                        <tr>
                            <th>Condition Precedent</th>
                            <td>{{$docData->condition_precedent}}</td>
                        </tr>
                        <tr>
                            <th>Termination Terms</th>
                            <td>{{$docData->termination_terms}}</td>
                        </tr>
                        <tr>
                            <th>Term of Payment</th>
                            <td>{{$docData->payment_terms}}</td>
                        </tr>
                        <tr>
                            <th>Term of Delay and Percentage Penalty</th>
                            <td>{{$docData->delay_penalty}}</td>
                        </tr>
                        <tr>
                            <th>Guarantee/Security</th>
                            <td>{{$docData->guarantee}}</td>
                        </tr>
                        <tr>
                            <th>Term of Agreement</th>
                            <td>{{$docData->agreement_terms}}</td>
                        </tr>
                </tbody>
            </table>
        </p>
    </body>
</html>
