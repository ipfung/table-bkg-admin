@php
    use Carbon\Carbon;
@endphp

<table>
    <tr>
        <td colspan="6" align="center" style="font-size:30px;"><h1>{{ __("Trainer Commission Report") }} </h1></td>
    </tr>
    <tr>
        <td colspan="6" align="right">Print Report Date:{{ date("Y-M-d")}}</td>
    </tr>
    <tr>
        <td colspan="6" align="right">Report Period:{{ date('Y-M-d', strtotime($report_s_date)) }}~{{ date('Y-M-d', strtotime($report_e_date)) }} </td>
    </tr>
</table>

    <table class="table table-striped table-hover">

        <thead class="thead-dark">
        <tr>
            <th style="width:150px;"><b>{{ __("Trainer") }}</b></th>
            <th style="width:100px;"><b>{{ __("Lesson Date") }}</b></th>
            <th style="width:100px;"><b>{{ __("Lesson Time") }}</b></th>
            <th style="width:100px;"><b>{{ __("Duration") }}</b></th>
            <th style="width:150px;"><b>{{ __("Student") }}</b></th>
            <th style="width:100px;"><b>{{ __("Commission") }}</b></th>
        </tr>

        </thead>
        <tbody>
            @php
                $total = 0;
                $total_unpaid = 0;
                $tmpTrainerId = 0;
                $showsubtotal = false;
                $subtotal = 0;
                $calsubtotal=0;
            @endphp
        @foreach($trainer_commissions as $index => $trainer_commission)
           
           
            @foreach($trainer_commission->customerBookings as $student  )
            <tr>
                <td>{{ $trainer_commission->user->name }}</td>
                <td>
                    {{ Carbon::parse($trainer_commission->start_time)->format('Y-m-d');
                   }}
                 
                </td>
                <td>{{ $trainer_commission->lessontime }}</td>
                <td>{{ $trainer_commission->simpleduration }}</td>
        {{--      <td>{{ $trainer_commission->paid_amount }}</td>    --}}
                <td>{{$student->customer->name}}</td>
                <td>{{ ($trainer_commission->simpleduration/60 )* $student->trainer_commission}}</td>      
        
                
            
            </tr>
            @php
                $tc = ($trainer_commission->simpleduration/60 )* $student->trainer_commission;
                if ($calsubtotal< $tc)
                 { 
                    $calsubtotal = $tc;
                };
            @endphp        
            
            @endforeach
           
            @php
                $subtotal = $subtotal +  $calsubtotal;
                $calsubtotal = 0;
            @endphp
            @if ( ($loop->index +1  <  count($trainer_commissions)) )
                @if ( ($trainer_commission->user_id != $trainer_commissions[$loop->index+1 ]->user_id)  )
                    <tr>
                        <td colspan='4'></td>
                        <td  align="right" ><b>Subtotal:</b></td>
                        <td><b>{{$subtotal}}</b></td>
                    </tr>
                    @php
                        $total = $total + $subtotal ;
                        $subtotal = 0;
                    @endphp
                @else
                    <tr>
                        <td colspan='6'></td>
                    </tr>
                @endif
            @else
                {{-- last row --}}
                <tr>
                    <td colspan='4'></td>
                    <td  align="right" ><b>Subtotal:</b></td>
                    <td><b>{{$subtotal}}</b></td>
                </tr>
                @php
                        $total = $total + $subtotal ;
                        $subtotal = 0;
                    @endphp
            @endif
            @php
               
            @endphp
        @endforeach

            <tr>
                <td colspan="5" align="right" ><strong>Total:</strong></td>
                <td align="right" ><strong>HK${{$total}}</strong></td>
            </tr>
        </tbody>
    </table>
