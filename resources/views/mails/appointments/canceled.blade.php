<div>
    <p>{{ __('Dear') }} {{ $customerBooking->customer->name }},</p>
    <p><b>{{ __('Below appointment is canceled') }}</b></p>
    <div class="row">
        <div class="col-3">{{ __('Date & Time') }}</div>
        <div class="col-8"><b>{{ $customerBooking->appointment->start_time }} - {{ $customerBooking->appointment->end_time }}</b><br /><br /></div>
    </div>
    <div class="row">
        <div class="col-3">{{ __('Duration') }}</div>
        <div class="col-8"><b>{{ $customerBooking->appointment->duration }}</b><br /><br /></div>
    </div>
    <div class="row">
        <div class="col-3">{{ __('Location') }}</div>
        <div class="col-8"><b>{{ $customerBooking->appointment->room->location->name }}</b><br /><br /></div>
    </div>
    <div class="row">
        <div class="col-3">{{ __('Assigned Table') }}</div>
        <div class="col-8"><b>{{ $customerBooking->appointment->room->name }}</b><br /><br /></div>
    </div>
{{--    <div class="row">--}}
{{--        <div class="col-3">{{ __('Payment') }}</div>--}}
{{--        <div class="col-8"><b>HK$ {{ $customerBooking->price }}</b><br /><br /></div>--}}
{{--    </div>--}}
{{--    <div class="row">--}}
{{--        <div class="col-3">{{ __('Remark') }}</div>--}}
{{--        <div class="col-8">{!! $results !!}</div>--}}
{{--    </div>--}}
    <p>{{ __('Hope to see you again soon.') }}</p>
{{--    <p><b>{{ $company->name }}</b></p>--}}
</div>
