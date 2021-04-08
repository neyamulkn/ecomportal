<table class="table display table-bordered table-striped">
    <thead>
        <tr>
            <th>Withdraw Date</th>
            <th>Payment Method</th>
            <th>Amount</th>
            <th>Details</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>

    @if(count($withdraws)>0)
        @foreach($withdraws as $withdraw)
        <tr>
           <td>{{\Carbon\Carbon::parse($withdraw->created_at)->format(Config::get('siteSetting.date_format'))}}
           ({{\Carbon\Carbon::parse($withdraw->created_at)->diffForHumans()}})
           </td>
            <td>@if($withdraw->paymethod_name){{$withdraw->paymethod_name->acc_name}} @endif</td>
           
            <td> <span class="label label-info">{{Config::get('siteSetting.currency_symble'). $withdraw->amount }}</span></td>
             <td>{{$withdraw->transaction_details }}</td>
           
            <td>@if($withdraw->status == 'paid') <span class="label label-success"> {{$withdraw->status}}</span> @elseif($withdraw->status == 'cancel') <span class="label label-danger"> {{$withdraw->status}} </span> @else <span class="label label-info"> {{$withdraw->status}} </span> @endif</td>
        </tr>
       @endforeach
    @else <tr><td colspan="8"> <h1>No Withdraw found.</h1></td></tr>@endif

    </tbody>
</table>