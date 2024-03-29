<table>
    <tr>
        <td colspan="8" align="center" style="font-size:30px;"><h1>{{ __("Order Report") }} </h1></td>
    </tr>
    <tr>
        <td colspan="8" align="right">Print Report Date:{{ date("Y-M-d")}}</td>
    </tr>
    {{-- <tr>
        <td colspan="8" align="right">Report Period:{{ date('Y-M-d', strtotime($report_s_date)) }}~{{ date('Y-M-d', strtotime($report_e_date)) }} </td>
    </tr> --}}
</table>

    <table class="table table-striped table-hover">
        
        <thead class="thead-dark">
        <tr>
            <th style="width:100px;"><b>{{ __("Order No") }}</b></th>
            <th style="width:100px;"><b>{{ __("Date") }}</b></th>
           {{--  <th style="width:100px;"><b>{{ __("Customer") }}</b></th>
            <th style="width:80px;"><b>{{ __("Total") }}</b></th>
            <th style="width:100px;"><b>{{ __("Payment Status") }}</b></th>                     
            <th style="width:100px;"><b>{{ __("Payment Method") }}</b></th>                    
            <th style="width:100px;"><b>{{ __("Created at") }}</b></th>
            <th style="width:100px;"><b>{{ __("Updated at") }}</b></th> --}}
        </tr>
      
        </thead>
        <tbody>
            @php
                $total = 0;    
                $total_unpaid = 0;
            @endphp
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->order_number }}</td>
            <td>{{ $order->order_date }}</td>
            {{-- <td>{{ $order->customer->name }}</td>
            <td>{{ $order->order_total }}</td>  
            <td>{{ $order->getPaymentStatusText() }}</td>                 
            <td>{{ $order->getPaymentMethodText() }}</td>                   
            <td>{{ date('Y-m-d', strtotime($order->created_at)) }}</td>
            <td>{{ date('Y-m-d', strtotime($order->updated_at)) }}</td>     --}}

                            
        </tr>
        @php
            /* if ($order->payment_status==2002) {//paid
                $total = $total + $order->order_total;    
            } else {
                $total_unpaid = $total_unpaid + $order->order_total; 
            } */
        @endphp
        @endforeach
        
  {{--   <tr>
        <td colspan="3" align="right" ><strong>Total:</strong></td>
        <td align="right" ><strong>HK${{$total}}</strong></td>
        <td align="right" ><strong>Unpaid Total:</strong></td>
        <td align="right" ><strong>HK${{$total_unpaid}}</strong></td>
    </tr> --}}
        </tbody>
    </table>
   