testtbl

{{-- @section('content') --}}
    <div class="container">
        <h2>{{ __("TESTTBL") }} </h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                <tr>
                    <th>{{ __("id") }}</th>
                    <th>{{ __("comment") }}</th>
                    
                    <th>{{ __("Created at") }}</th>
                    <th>{{ __("Updated at") }}</th>
                </tr>
                
                </thead>
                <tbody>
                @foreach($testtbls as $testtbl)
                <tr>
                    <td>{{ $testtbl->id }}</td>
                    <td>{{ $testtbl->comment }}</td>                    
                    <td>{{ date('Y-m-d h:m:s', strtotime($testtbl->created_at)) }}</td>
                    <td>{{ date('Y-m-d h:m:s', strtotime($testtbl->updated_at)) }}</td>
                    
                </tr>
                @endforeach
                </tbody>
            </table>
            {{ $testtbls->appends(Request::all())->links() }}
        </div>
    </div>
{{-- @endsection --}}