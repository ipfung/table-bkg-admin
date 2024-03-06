<table>
    <tr>
        <td colspan="8" align="center" style="font-size:30px;"><h1>{{ __("Sales Report") }} </h1></td>
    </tr>
    <tr>
        <td colspan="8" align="right">Print Report Date:{{ date("Y-M-d")}}</td>
    </tr>
    <tr>
        <td colspan="8" align="right">Report Period:{{ date('Y-M-d', strtotime($report_s_date)) }}~{{ date('Y-M-d', strtotime($report_e_date)) }} </td>
    </tr>
</table>

    <table class="table table-striped table-hover">
        
        <thead class="thead-dark">
        <tr>
            <th style="width:150px;height:40px;"><b>{{ __("Order No") }}</b></th>
            <th style="width:100px;"><b>{{ __("Date") }}</b></th>
            <th style="width:120px;"><b>{{ __("Customer") }}</b></th>
            <th style="width:100px;"><b>{{ __("Paid Amount") }}</b></th>
            <th style="width:60px;"><b>{{ "Payment" }} <br />{{"Status"}}</b></th>                     
            <th style="width:60px;"><b>{{ "Payment" }} <br />{{"Method"}}</b></th>   
            <th style="width:140px;"><b>{{ __("Payment Date") }}</b></th>                 
            <th style="width:100px;"><b>{{ __("Reference") }}</b></th>
            {{-- <th style="width:100px;"><b>{{ __("Updated at") }}</b></th> --}}
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
            <td>{{ $order->customer->name }}</td>
            <td>{{ $order->order_total }}</td>  
            <td>{{ $order->payment_status }}</td>                 
            <td>{{ $order->payment->gateway }}</td>  
            <td>{{ $order->payment->payment_date_time }}</td>
            <td>
            @if (isset($order->payment->gateway_response))
            @php 
                $p = json_decode($order->payment->gateway_response) ;
            
                    echo $p->ref ; 
           

           @endphp
            @endif
            </td>
            {{-- <td>{{ $order->payment->gateway_response->ref}}</td>                    --}}
           
             {{--  <td>{{ date('Y-m-d', strtotime($order->updated_at)) }}</td>     --}}

                            
        </tr>
        @php
        if ($order->payment_status=="paid" ){
            $total = $total + $order->order_total;  
        } else {
            $total_unpaid = $total_unpaid + $order->order_total;
        }
        
        
           /*  if ($order->payment_status==2002) {//paid
                $total = $total + $order->paid_amount;    
            } else {
                //$total_unpaid = $total_unpaid + $order->order_total; 
            } */
        @endphp
        @endforeach
        
    <tr>
        <td colspan="3" align="right" ><strong>Total:</strong></td>
        <td align="right" ><strong>{{$total}}</strong></td>
        <td><strong>Unpaid Total:{{$total_unpaid}}</strong></td>
       {{--  <td align="right" ><strong>Unpaid Total:</strong></td>
        <td align="right" ><strong>HK${{$total_unpaid}}</strong></td> --}}
    </tr>
        </tbody>
    </table>
   