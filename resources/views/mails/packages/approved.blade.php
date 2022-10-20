<div>
    <p>{{ __('Dear') }} {{ $order->customer->name }},</p>
    <p>{{ __('Your appointments are approved') }}</p>
    <div class="row">
        <div class="col-3">{{ __('Date & Time') }}</div>
        <div class="col-8">
            @foreach($order->details as $detail)
            <b>{{ $detail->description->start_time }} - {{ $detail->description->end_time }}</b><br />
            @endforeach
        </div>
    </div>
    @if ($order->trainer_id > 0)
    <div class="row">
        <div class="col-3">{{ __('Trainer') }}</div>
        <div class="col-8"><b>HK$ {{ $order->trainer->name }}</b><br /><br /></div>
    </div>
    @endif
    <div class="row">
        <div class="col-3">{{ __('Payment') }}</div>
        <div class="col-8"><b>HK$ {{ $order->order_total }}</b><br /><br /></div>
    </div>
{{--    <div class="row">--}}
{{--        <div class="col-3">{{ __('Remark') }}</div>--}}
{{--        <div class="col-8">{!! $results !!}</div>--}}
{{--    </div>--}}
    <p>{{ __('Thank you for your order') }}</p>
{{--    <p><b>{{ $company->name }}</b></p>--}}
</div>
