<div>
    <p>{{ __('Dear') }} {{ $customerBooking->customer_id }},</p>
    <p>{{ __('This is a reminder for below appointment.') }}</p>
    <div class="row">
        <div class="col-3">{{ __('Date & Time') }}</div>
        <div class="col-8"><b>{{ $customerBooking->start_time }} - {{ $customerBooking->end_time }}</b><br /><br /></div>
    </div>
    <div class="row">
        <div class="col-3">{{ __('Assigned Table') }}</div>
        <div class="col-8"><b>{{ $customerBooking->name }}</b><br /><br /></div>
    </div>
    <p>{{ __('See you then') }}</p>
</div>
