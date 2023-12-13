
    
    <div class="container">
        <h2>order{{ __("Order Report") }} </h2>
    </div>
    <div class="row rounded border-1 border-gray-400 form-group pt-2">
       
   {{--      
        <div class="col-md-2">Start: {{ Form::date('start_date', new \DateTime(), ['id'=>'start_date', 'class' => 'form-control']) }}</div>
        <div class="col-md-2">End: {{ Form::date('end_date', new \DateTime(), ['id'=>'end_date', 'class' => 'form-control']) }}</div>
        <div class="col-md-2">Payment Status: 
            <select id="search_payment_status" name="search_payment_status" class="form-control" >
                <option value="" >-</option>
                <option value="2002" >Paid</option>
                <option value="2001" >Unpaid</option>
            </select>               
        </div>
        <div class="col-md-2"><br /><button id="btn-search" class="btn btn-primary form-control" >{{ __('Search')}}</button></div> --}}
        <div class="col-md-2"><br /><button id="btn-export" class="btn btn-info form-control" >{{ __('Export')}}</button></div>       
       {{--  {{ Form::close() }} --}}
       
    </div>
    
  
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
            <tr>
                <th>{{ __("Order No") }}</th>
                <th>{{ __("Date") }}</th>{{-- 
                <th>{{ __("Customer") }}</th>
                <th>{{ __("Total") }}</th>
                <th>{{ __("Payment Status") }}</th>                     
                <th>{{ __("Payment Method") }}</th>                    
                <th>{{ __("Created at") }}</th>
                <th>{{ __("Updated at") }}</th> --}}
            </tr>
          
            </thead>
            <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->order_date }}</td>{{-- 
                <td>{{ $order->customer->name }}</td>
                <td>HK${{ $order->order_total }}</td>  
                <td>{{ $order->getPaymentStatusText() }}</td>                 
                <td>{{ $order->getPaymentMethodText() }}</td>  
                <td>{{ date('Y-m-d', strtotime($order->created_at)) }}</td>
                <td>{{ date('Y-m-d', strtotime($order->updated_at)) }}</td>         --}}      
                

                                
            </tr>
            @endforeach
            </tbody>
        </table>
        {{ $orders->appends(Request::all())->links() }}
    </div>
</div>
<script>
    $(function(){
        var url = new URL(document.location);
        // Get query parameters object
        var params = url.searchParams;

        // Get value of paper
        var start_date = params.get("star_date");
        if (start_date){
            $("#start_date").val(start_date);
            console.log(start_date);
        }   

        // Set it as the dropdown value

        $('#btn-export').click(function(){
            //alert('yo');
            start_date = $("#start_date").val();
            end_date = $('#end_date').val();
            console.log("date="+start_date );
            window.location.href = '{{ route('export.report.order') }}'+ '?start_date=' + start_date + '&end_date=' + end_date + '&search_payment_status=' + $("#search_payment_status").val();
        })

        $('#btn-search').click(function(){
            //alert('yo');
            start_date = $("#start_date").val();
            end_date = $('#end_date').val();
            console.log("date="+start_date );
            window.location.href = '{{ route('report.order') }}'+ '?start_date=' + start_date + '&end_date=' + end_date + '&search_payment_status=' + $("#search_payment_status").val();
        })
    })

    //$("#start_date").val("2023-01-01");
    var url = new URL(document.location);
        // Get query parameters object
        var params = url.searchParams;
        // Get value of paper
        var start_date = params.get("start_date");
        if (start_date){
            $("#start_date").val(start_date);
            console.log(start_date);
        }   
        var end_date = params.get("end_date");
        if (end_date){
            $("#end_date").val(end_date);                
        }
        var search_payment_status = params.get("search_payment_status");
        if (search_payment_status){
            $("#search_payment_status").val(search_payment_status);                
        }

        //
</script>