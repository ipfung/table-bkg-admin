<table>
    <tr>
        <td colspan="8" align="center" style="font-size:30px;"><h1>{{ __("Trainer Commission Report") }} </h1></td>
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
            <th style="width:100px;"><b>{{ __("Trainer") }}</b></th>
            <th style="width:100px;"><b>{{ __("Lesson Date") }}</b></th>
            <th style="width:100px;"><b>{{ __("Duration") }}</b></th>            
            <th style="width:80px;"><b>{{ __("Student") }}</b></th>
            <th style="width:100px;"><b>{{ __("Commission") }}</b></th>
        </tr>
      
        </thead>
        <tbody>
            @php
                $total = 0;    
                $total_unpaid = 0;
            @endphp
        @foreach($trainer_commissions as $trainer_commission)
        <tr>
            <td>{{ $trainer_commission->user->name }}</td>
            <td>{{ $trainer_commission->start_time }}</td>            
            <td>{{ $trainer_commission->simpleduration }}</td>
       {{--      <td>{{ $trainer_commission->paid_amount }}</td>    --}}    
            <td> 
                 <table>                
                    @foreach($trainer_commission->customer_bookings as $student  )                     
                        <tr>
                            <td>{{$student->customer->name}}</td>   
                        </tr>       
                    @endforeach
                </table> 
            </td>
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
   